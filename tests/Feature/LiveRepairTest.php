<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CashfreeController;
use App\Models\Package;
use App\Services\CashfreeOrderVerifier;
use App\Services\CashfreePaymentCredit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LiveRepairTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        Schema::create('tbl_payment_option', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('key_1'); $t->string('key_2'); $t->string('key_3')->default('');
            $t->string('is_live')->default('0'); $t->string('visibility')->default('1');
        });
        DB::table('tbl_payment_option')->insert(['name' => 'cashfree', 'key_1' => 'public-id', 'key_2' => 'secret', 'key_3' => 'another-secret']);
        Schema::create('tbl_general_setting', function (Blueprint $t) { $t->id(); $t->string('key'); $t->string('value'); });
        Schema::create('tbl_package', function (Blueprint $t) {
            $t->id(); $t->decimal('price',12,2); $t->integer('status')->default(1); $t->integer('time')->default(1); $t->string('type')->default('month'); $t->timestamps();
        });
        Schema::create('tbl_user', function (Blueprint $t) { $t->id(); $t->integer('status')->default(1); $t->string('role')->default('user'); $t->timestamps(); });
        Schema::create('tbl_transaction', function (Blueprint $t) {
            $t->id(); $t->integer('user_id'); $t->integer('package_id'); $t->decimal('price',12,2);
            $t->string('description'); $t->string('transaction_id'); $t->string('expiry_date'); $t->integer('status'); $t->timestamps();
        });
        (require database_path('migrations/2026_07_10_000001_create_tbl_cashfree_orders.php'))->up();
        DB::table('tbl_package')->insert(['id'=>7,'price'=>99,'status'=>1,'time'=>1,'type'=>'month']);
        DB::table('tbl_user')->insert(['id'=>42,'status'=>1]);
        DB::table('tbl_cashfree_orders')->insert(['order_id'=>'existing_order','user_id'=>42,'package_id'=>7,'amount'=>99]);
        Http::preventStrayRequests();
    }
    private function order(array $override=[]): array
    {
        return array_replace(['order_id'=>'existing_order','order_status'=>'PAID','order_currency'=>'INR','order_amount'=>99,'customer_details'=>['customer_id'=>'42']],$override);
    }
    public function test_staging_tests_use_live_baseline_not_old_local_controllers(): void
    {
        $this->assertSame(realpath(app_path('Http/Controllers/Api/CashfreeController.php')), (new \ReflectionClass(CashfreeController::class))->getFileName());
        $this->assertTrue(method_exists(CashfreeController::class,'createSubscription'));
    }
    public function test_public_settings_remove_secrets_and_preserve_subscription_toggle(): void
    {
        DB::table('tbl_general_setting')->insert([
            ['key'=>'cashfree_subscription_enabled','value'=>'1'], ['key'=>'bunny_storage_access_key','value'=>'private'], ['key'=>'app_name','value'=>'Jailaoi'],
        ]);
        $result=(new HomeController)->general_setting();
        $this->assertSame(200,$result['status']);
        $values=collect($result['result'])->pluck('value','key');
        $this->assertSame('1',$values['cashfree_subscription_enabled']);
        $this->assertFalse($values->has('bunny_storage_access_key'));
        $payment=(new HomeController)->get_payment_option()['result']['cashfree'];
        $this->assertSame('',$payment['key_2']); $this->assertSame('',$payment['key_3']);
        $this->assertSame('1',$payment['visibility']);
    }
    public function test_existing_ledger_order_without_new_note_is_supported(): void
    {
        Http::fake(['*'=>Http::response($this->order())]);
        $this->assertSame('PAID',(new CashfreeOrderVerifier)->verify('existing_order',42,Package::find(7))['order_status']);
    }
    public function test_wrong_account_cannot_reuse_order(): void
    {
        $this->expectException(ValidationException::class);
        (new CashfreeOrderVerifier)->verify('existing_order',99,Package::find(7));
    }
    public function test_provider_underpayment_is_rejected(): void
    {
        Http::fake(['*'=>Http::response($this->order(['order_amount'=>1]))]);
        $this->expectException(ValidationException::class);
        (new CashfreeOrderVerifier)->verify('existing_order',42,Package::find(7));
    }
    public function test_legacy_underpriced_ledger_cannot_unlock_full_price_package(): void
    {
        DB::table('tbl_cashfree_orders')->update(['amount'=>1]);
        $this->expectException(ValidationException::class);
        (new CashfreeOrderVerifier)->verify('existing_order',42,Package::find(7));
    }
    public function test_unpaid_provider_order_cannot_unlock_package(): void
    {
        Http::fake(['*'=>Http::response($this->order(['order_status'=>'ACTIVE']))]);
        $this->expectException(ValidationException::class);
        (new CashfreeOrderVerifier)->verify('existing_order',42,Package::find(7));
    }
    public function test_client_and_webhook_retries_credit_an_order_once(): void
    {
        Http::fake(['*'=>Http::response($this->order())]);
        $service=new CashfreePaymentCredit;
        $first=$service->order('existing_order',42,7); $again=$service->order('existing_order',42,7);
        $this->assertSame($first->id,$again->id); $this->assertSame(1,DB::table('tbl_transaction')->count());
        $this->assertSame('paid',DB::table('tbl_cashfree_orders')->value('status'));
    }
    public function test_create_order_uses_package_price_instead_of_submitted_amount(): void
    {
        Http::fake(['*'=>Http::response(['order_id'=>'new_order','payment_session_id'=>'session'])]);
        $request=Request::create('/api/cashfree/create-order','POST',['user_id'=>42,'package_id'=>7,'amount'=>1,'order_id'=>'new_order']);
        $result=(new CashfreeController)->createOrder($request);
        $this->assertSame(200,$result['status']);
        Http::assertSent(fn($request)=>$request['order_amount']===99.0);
        $this->assertSame(99.0,(float)DB::table('tbl_cashfree_orders')->where('order_id','new_order')->value('amount'));
    }
    public function test_guest_catalog_cache_miss_and_hit_preserve_response(): void
    {
        Schema::create('tbl_section', function (Blueprint $t) {
            $t->id(); $t->integer('user_id'); $t->integer('section_type'); $t->integer('status');
            $t->integer('is_pinned')->default(0); $t->integer('sortable')->default(0);
            foreach (['type','artist_id','category_id','language_id','city_id','order_by_upload','order_by_play','is_premium','no_of_content'] as $field) $t->integer($field)->default(0);
        });
        DB::table('tbl_section')->insert(['user_id'=>0,'section_type'=>3,'status'=>1]);
        $controller=new HomeController;
        $controller->common=\Mockery::mock(\App\Models\Common::class)->makePartial();
        $controller->common->shouldReceive('section_query')->once()->andReturn([]);
        $first=$controller->get_radio_section_list(Request::create('/api/get_radio_section_list','POST'));
        $second=$controller->get_radio_section_list(Request::create('/api/get_radio_section_list','POST'));
        $this->assertSame(200,$first['status']); $this->assertSame(200,$second->getData(true)['status']);
    }
    public function test_settlement_preview_includes_paid_purchases_that_are_no_longer_active(): void
    {
        RevenueMachineTest::tables();
        DB::table('tbl_transaction')->insert(['user_id'=>42,'package_id'=>7,'price'=>99,'description'=>'cashfree','transaction_id'=>'paid_previous','expiry_date'=>'2026-10-01','status'=>0,'created_at'=>'2026-09-15']);
        DB::table('tbl_cashfree_orders')->insert(['order_id'=>'paid_previous','user_id'=>42,'package_id'=>7,'amount'=>99,'status'=>'paid']);
        $this->artisan('earnings:settle',['--month'=>'2026-09','--pretend'=>true])->expectsOutput('  Subscription revenue:          99')->assertSuccessful();
    }
    public function test_forced_settlement_cannot_overwrite_historical_credits(): void
    {
        $this->artisan('earnings:settle',['--month'=>'2026-09','--force'=>true])->assertFailed();
    }

    public function test_unknown_content_types_are_rejected_without_database_queries(): void
    {
        foreach (['get_content_by_artist','get_related_data','search_content','get_favorite_list','get_comment'] as $method) {
            $response=(new HomeController)->$method(Request::create('/api/'.$method,'POST',['type'=>99,'artist_id'=>1,'content_id'=>1,'user_id'=>42,'search'=>'x']));
            $this->assertSame(400,$response['status']);
        }
    }
    public function test_public_artist_serialization_excludes_wallet_balance(): void
    {
        $artist=new \App\Models\Artist(['id'=>1,'name'=>'Artist','wallet_balance'=>500]);
        $this->assertArrayNotHasKey('wallet_balance',$artist->toArray());
        $this->assertSame(500.0,$artist->wallet_balance);
    }
    private function artistAccessTables(): void
    {
        Schema::create('tbl_artist',function(Blueprint $t){$t->id();$t->integer('user_id');$t->integer('is_suspended')->default(0);});
    }
    public function test_suspended_artist_existing_web_session_is_blocked(): void
    {
        $this->artistAccessTables();
        $user=\App\Models\User::find(42); $user->update(['role'=>'artist']);
        DB::table('tbl_artist')->insert(['user_id'=>42,'is_suspended'=>1]);
        $this->actingAs($user,'user');
        $request=Request::create('/user/music','GET');$request->headers->set('Accept','application/json');
        $response=(new \App\Http\Middleware\AuthUser)->handle($request,fn()=>response()->json(['status'=>200]));
        $this->assertSame(423,$response->getStatusCode());
        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('user')->check());
    }
    public function test_active_artist_web_session_remains_allowed(): void
    {
        $this->artistAccessTables();
        $user=\App\Models\User::find(42);$user->update(['role'=>'artist']);
        DB::table('tbl_artist')->insert(['user_id'=>42,'is_suspended'=>0]);
        $this->actingAs($user,'user');
        $response=(new \App\Http\Middleware\AuthUser)->handle(Request::create('/user/dashboard','GET'),fn()=>response()->json(['status'=>200]));
        $this->assertSame(200,$response->getStatusCode());
    }
    public function test_suspended_artist_cannot_redeem_cached_portal_token(): void
    {
        $this->artistAccessTables();
        \App\Models\User::find(42)->update(['role'=>'artist']);
        DB::table('tbl_artist')->insert(['user_id'=>42,'is_suspended'=>1]);
        $token=str_repeat('a',48);\Illuminate\Support\Facades\Cache::put('portal_token:'.$token,42,300);
        $request=Request::create('/user/dashboard?portal_token='.$token,'GET');
        (new \App\Http\Middleware\PortalTokenLogin)->handle($request,fn()=>response()->json([]));
        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('user')->check());
    }
    public function test_active_artist_portal_token_can_only_be_used_once(): void
    {
        $this->artistAccessTables();
        \App\Models\User::find(42)->update(['role'=>'artist']);
        DB::table('tbl_artist')->insert(['user_id'=>42,'is_suspended'=>0]);
        $token=str_repeat('a',48);\Illuminate\Support\Facades\Cache::put('portal_token:'.$token,42,300);
        $request=Request::create('/user/dashboard?portal_token='.$token,'GET');
        (new \App\Http\Middleware\PortalTokenLogin)->handle($request,fn()=>response()->json([]));
        $this->assertSame(42,\Illuminate\Support\Facades\Auth::guard('user')->id());
        $this->assertNull(\Illuminate\Support\Facades\Cache::get('portal_token:'.$token));
    }

    private function subscriptionLedger(): void
    {
        (require database_path('migrations/2026_07_10_000002_create_tbl_cashfree_subscriptions.php'))->up();
        DB::table('tbl_cashfree_subscriptions')->insert(['subscription_id'=>'sub_test','user_id'=>42,'package_id'=>7,'plan_amount'=>99,'status'=>'created']);
    }

    private function webhookEvent(array $payload, ?string $timestamp = null, ?string $signature = null)
    {
        $timestamp ??= (string) (time() * 1000);
        $raw = json_encode($payload);
        $request = Request::create('/api/cashfree/webhook', 'POST', [], [], [], [], $raw);
        $request->headers->set('x-webhook-timestamp', $timestamp);
        $request->headers->set('x-webhook-signature', $signature ?? base64_encode(hash_hmac('sha256', $timestamp . $raw, 'secret', true)));
        return (new CashfreeController)->webhook($request);
    }

    private function subscriptionPayment(array $overrides = [], string $type = 'SUBSCRIPTION_PAYMENT_SUCCESS'): array
    {
        return ['type'=>$type,'data'=>array_replace(['subscription_id'=>'sub_test','cf_payment_id'=>'charge_1',
            'payment_status'=>'SUCCESS','payment_amount'=>99,'payment_currency'=>'INR','payment_type'=>'CHARGE'], $overrides)];
    }

    public function test_millisecond_signed_webhook_is_accepted_but_stale_and_forged_are_rejected(): void
    {
        $payload=['type'=>'UNKNOWN','data'=>[]];
        $this->assertSame(200,$this->webhookEvent($payload)->getStatusCode());
        $this->assertSame(200,$this->webhookEvent($payload,(string)time())->getStatusCode());
        $this->assertSame(400,$this->webhookEvent($payload,(string)((time()-600)*1000))->getStatusCode());
        $this->assertSame(401,$this->webhookEvent($payload,null,'forged')->getStatusCode());
        $this->assertSame(400,$this->webhookEvent($payload,'invalid')->getStatusCode());
    }

    public function test_subscription_creation_respects_disabled_live_toggle(): void
    {
        $result=(new CashfreeController)->createSubscription(Request::create('/','POST',[]));
        $this->assertSame(400,$result['status']); Http::assertNothingSent();
    }

    public function test_existing_subscription_cannot_be_overwritten_by_new_account(): void
    {
        $this->subscriptionLedger();
        DB::table('tbl_general_setting')->insert(['key'=>'cashfree_subscription_enabled','value'=>'1']);
        $result=(new CashfreeController)->createSubscription(Request::create('/','POST',[
            'user_id'=>99,'package_id'=>7,'subscription_id'=>'sub_test','email'=>'test@example.com','phone'=>'9999999999']));
        $this->assertSame(400,$result['status']); $this->assertSame(42,DB::table('tbl_cashfree_subscriptions')->value('user_id')); Http::assertNothingSent();
    }

    public function test_auth_and_success_notifications_credit_the_same_charge_once_and_renewal_separately(): void
    {
        $this->subscriptionLedger();
        $auth=$this->subscriptionPayment(['payment_type'=>'AUTH'],'SUBSCRIPTION_AUTH_STATUS');
        $this->assertSame(200,$this->webhookEvent($auth)->getStatusCode());
        $this->assertSame(200,$this->webhookEvent($this->subscriptionPayment())->getStatusCode());
        $this->assertSame(1,DB::table('tbl_transaction')->count());
        $this->assertSame(200,$this->webhookEvent($this->subscriptionPayment(['cf_payment_id'=>'charge_2']))->getStatusCode());
        $this->assertSame(2,DB::table('tbl_transaction')->count());
        $this->assertSame(1,DB::table('tbl_transaction')->where('status',1)->count());
    }

    public function test_unpaid_underpaid_foreign_currency_or_missing_id_cannot_credit_subscription(): void
    {
        $this->subscriptionLedger();
        foreach ([['payment_status'=>'PENDING'],['payment_amount'=>1],['payment_currency'=>'USD'],['cf_payment_id'=>'']] as $override) {
            $this->assertSame(503,$this->webhookEvent($this->subscriptionPayment($override))->getStatusCode());
        }
        $this->assertSame(0,DB::table('tbl_transaction')->count());
    }

    public function test_refundable_authorization_is_not_subscription_revenue(): void
    {
        $this->subscriptionLedger();
        $payload=$this->subscriptionPayment(['payment_type'=>'AUTH','authorization_details'=>['authorization_amount_refund'=>true]],'SUBSCRIPTION_AUTH_STATUS');
        $this->assertSame(503,$this->webhookEvent($payload)->getStatusCode());
        $this->assertSame(0,DB::table('tbl_transaction')->count());
    }

    public function test_checkout_completion_without_recorded_payment_does_not_claim_premium(): void
    {
        $this->subscriptionLedger();
        $controller=new CashfreeController;
        $request=Request::create('/','POST',['subscription_id'=>'sub_test','user_id'=>42]);
        $this->assertFalse($controller->subscriptionStatus($request)->getData(true)['result']['paid']);
        $this->webhookEvent($this->subscriptionPayment());
        $this->assertTrue($controller->subscriptionStatus($request)->getData(true)['result']['paid']);
        $request->merge(['user_id'=>99]);
        $this->assertSame(404,$controller->subscriptionStatus($request)->getStatusCode());
    }

    public function test_cancellation_preserves_paid_access_and_failed_renewal_grants_nothing(): void
    {
        $this->subscriptionLedger(); $this->webhookEvent($this->subscriptionPayment());
        $this->assertSame(200,$this->webhookEvent(['type'=>'SUBSCRIPTION_STATUS_CHANGED','data'=>[
            'subscription_details'=>['subscription_id'=>'sub_test','subscription_status'=>'CUSTOMER_CANCELLED']]])->getStatusCode());
        $this->assertSame('customer_cancelled',DB::table('tbl_cashfree_subscriptions')->value('status'));
        $this->webhookEvent($this->subscriptionPayment(['cf_payment_id'=>'charge_failed','payment_status'=>'FAILED'],'SUBSCRIPTION_PAYMENT_FAILED'));
        $this->assertSame(1,DB::table('tbl_transaction')->count());
        $result=(new CashfreeController)->subscriptionStatus(Request::create('/','POST',['subscription_id'=>'sub_test','user_id'=>42]));
        $this->assertTrue($result->getData(true)['result']['paid']);
        DB::table('tbl_transaction')->update(['expiry_date'=>now()->subMinute()->format('Y-m-d H:i')]);
        $this->assertFalse((new CashfreeController)->subscriptionStatus(Request::create('/','POST',['subscription_id'=>'sub_test','user_id'=>42]))->getData(true)['result']['paid']);
    }

    public function test_month_end_expiry_and_second_purchase_preserve_paid_time(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-01-31 12:00:00'));
        $service=app(\App\Services\SubscriptionExpiry::class);
        $this->assertSame('2026-02-28 12:00',$service->next(42,Package::find(7)));
        DB::table('tbl_transaction')->insert(['user_id'=>42,'package_id'=>7,'price'=>99,'description'=>'cashfree','transaction_id'=>'previous',
            'expiry_date'=>'2026-02-28 12:00','status'=>1]);
        $this->assertSame('2026-03-28 12:00',$service->next(42,Package::find(7)));
        DB::table('tbl_transaction')->delete();
        DB::table('tbl_package')->where('id',7)->update(['type'=>'year']);
        $this->travelTo(\Carbon\Carbon::parse('2024-02-29 12:00:00'));
        $this->assertSame('2025-02-28 12:00',$service->next(42,Package::find(7)));
        $this->travelBack();
    }

    public function test_verified_paid_order_with_existing_purchase_repairs_ledger_without_new_access(): void
    {
        Http::fake(['*'=>Http::response($this->order())]);
        $service=new CashfreePaymentCredit; $first=$service->order('existing_order',42,7);
        DB::table('tbl_cashfree_orders')->update(['status'=>'created','paid_at'=>null]);
        $first->description='Monthly Plan'; $first->save();
        $expiry=$first->expiry_date;
        $again=$service->order('existing_order',42,7);
        $this->assertSame($expiry,$again->expiry_date);
        $this->assertSame('cashfree',$again->description);
        $this->assertSame(1,DB::table('tbl_transaction')->count());
        $this->assertSame('paid',DB::table('tbl_cashfree_orders')->value('status'));
    }
}
