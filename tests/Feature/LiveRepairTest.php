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
        Schema::create('tbl_earnings_settlements',function(Blueprint $t){$t->id();$t->string('month');});
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('settled_month')->nullable();$t->timestamps();});
        Schema::create('tbl_monetization_applications',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('status');});
        DB::table('tbl_transaction')->insert(['user_id'=>42,'package_id'=>7,'price'=>99,'description'=>'cashfree','transaction_id'=>'paid_previous','expiry_date'=>'2026-10-01','status'=>0,'created_at'=>'2026-09-15']);
        $this->artisan('earnings:settle',['--month'=>'2026-09','--pretend'=>true])->expectsOutput('  Subscription revenue:          99')->assertSuccessful();
    }
    public function test_forced_settlement_preview_counts_previously_settled_plays(): void
    {
        Schema::create('tbl_earnings_settlements',function(Blueprint $t){$t->id();$t->string('month');});
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('settled_month')->nullable();$t->timestamps();});
        Schema::create('tbl_monetization_applications',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('status');});
        DB::table('tbl_earnings_settlements')->insert(['month'=>'2026-09']);
        DB::table('tbl_monetization_applications')->insert(['artist_id'=>1,'status'=>'approved']);
        DB::table('tbl_artist_earnings')->insert(['artist_id'=>1,'settled_month'=>'2026-09','created_at'=>'2026-09-15']);
        $this->artisan('earnings:settle',['--month'=>'2026-09','--pretend'=>true,'--force'=>true])->expectsOutput('  Eligible streams (approved):    1')->assertSuccessful();
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
}
