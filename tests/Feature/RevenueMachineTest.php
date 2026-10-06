<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\RevenueSettlement;
use RuntimeException;
class RevenueMachineTest extends TestCase {
    public static function tables(): void {
        if(!Schema::hasTable('tbl_general_setting'))Schema::create('tbl_general_setting',fn(Blueprint $t)=>[$t->id(),$t->string('key'),$t->string('value')]);
        if(!Schema::hasTable('tbl_artist'))Schema::create('tbl_artist',fn(Blueprint $t)=>[$t->id(),$t->string('name'),$t->integer('status')->default(1),$t->integer('is_suspended')->default(0),$t->decimal('wallet_balance',12,4)->default(0)]);
        if(!Schema::hasTable('tbl_transaction'))Schema::create('tbl_transaction',fn(Blueprint $t)=>[$t->id(),$t->integer('user_id'),$t->integer('package_id'),$t->string('price'),$t->string('description'),$t->string('transaction_id'),$t->string('cf_subscription_id')->nullable(),$t->integer('status')->default(1),$t->timestamps()]);
        if(!Schema::hasTable('tbl_cashfree_orders'))Schema::create('tbl_cashfree_orders',fn(Blueprint $t)=>[$t->id(),$t->string('order_id'),$t->integer('user_id'),$t->integer('package_id'),$t->decimal('amount',12,2),$t->string('status')]);
        if(!Schema::hasTable('tbl_cashfree_subscriptions'))Schema::create('tbl_cashfree_subscriptions',fn(Blueprint $t)=>[$t->id(),$t->string('subscription_id'),$t->integer('user_id'),$t->integer('package_id'),$t->decimal('plan_amount',12,2)]);
        if(!Schema::hasTable('tbl_artist_earnings'))Schema::create('tbl_artist_earnings',fn(Blueprint $t)=>[$t->id(),$t->integer('artist_id'),$t->integer('user_id'),$t->integer('content_id'),$t->integer('content_type'),$t->decimal('amount',10,6)->default(0),$t->string('settled_month')->nullable(),$t->timestamps()]);
        if(!Schema::hasTable('tbl_monetization_applications'))Schema::create('tbl_monetization_applications',fn(Blueprint $t)=>[$t->id(),$t->integer('artist_id'),$t->string('status')]);
        if(!Schema::hasTable('tbl_earnings_settlements'))Schema::create('tbl_earnings_settlements',function(Blueprint $t){$t->id();$t->string('month')->unique();foreach(['total_revenue','platform_cut','pool_amount','rate_per_stream','additional_revenue'] as $c)$t->decimal($c,12,6);$t->integer('total_streams');$t->timestamp('settled_at');$t->timestamps();});
        (require database_path('migrations/2026_10_06_000001_create_revenue_review_tables.php'))->up();
    }
    protected function setUp(): void {
        parent::setUp();config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','services.revenue.identity_enforced'=>true]);$this->travelTo(now()->setDate(2026,10,6));self::tables();
        DB::table('tbl_general_setting')->insert([['key'=>'earnings_model','value'=>'pool'],['key'=>'platform_cut_pct','value'=>'30'],['key'=>'payout_currency','value'=>'INR']]);
        DB::table('tbl_artist')->insert([['id'=>1,'name'=>'A'],['id'=>2,'name'=>'B']]);
        DB::table('tbl_monetization_applications')->insert([['artist_id'=>1,'status'=>'approved'],['artist_id'=>2,'status'=>'approved']]);
        DB::table('tbl_cashfree_orders')->insert(['order_id'=>'paid','user_id'=>10,'package_id'=>1,'amount'=>'100.01','status'=>'paid']);
        DB::table('tbl_transaction')->insert(['user_id'=>10,'package_id'=>1,'price'=>'100.01','description'=>'cashfree','transaction_id'=>'paid','status'=>0,'created_at'=>'2026-09-10 00:00:00']);
        foreach([1,2] as $id)DB::table('tbl_artist_earnings')->insert(['artist_id'=>$id,'user_id'=>10,'content_id'=>$id,'content_type'=>8,'created_at'=>'2026-09-10 00:00:00']);
    }
    public function test_review_does_not_credit_wallet_and_approval_conserves_every_paise(): void {
        $s=app(RevenueSettlement::class);$r=$s->prepare('2026-09',7);
        $this->assertSame(10001,$r['subscription_cents']);$this->assertSame(7001,$r['pool_cents']);$this->assertSame(0.0,(float)DB::table('tbl_artist')->sum('wallet_balance'));
        $s->approve('2026-09',$r['fingerprint'],7);
        $this->assertSame(70.01,round((float)DB::table('tbl_artist')->sum('wallet_balance'),2));$this->assertSame(7001,(int)DB::table('tbl_artist_statements')->sum('amount_cents'));$this->assertSame(70.01,round((float)DB::table('tbl_artist_earnings')->sum('amount'),2));
        $this->assertSame('settled',DB::table('tbl_revenue_reviews')->value('status'));
        $this->expectException(RuntimeException::class);$s->approve('2026-09',$r['fingerprint'],7);
    }
    public function test_changed_revenue_invalidates_approval_without_partial_credits(): void {
        $s=app(RevenueSettlement::class);$r=$s->prepare('2026-09');
        DB::table('tbl_revenue_entries')->insert(['month'=>'2026-09','kind'=>'refund','amount_cents'=>100,'reference'=>'refund-1','note'=>'Verified refund','created_by'=>7]);
        try{$s->approve('2026-09',$r['fingerprint'],7);$this->fail('Stale snapshot accepted');}catch(RuntimeException $e){$this->assertStringContainsString('changed',$e->getMessage());}
        $this->assertSame(0,DB::table('tbl_artist_statements')->count());$this->assertSame(0.0,(float)DB::table('tbl_artist')->sum('wallet_balance'));
    }
    public function test_ad_income_and_deductions_enter_pool_only_from_confirmed_entries(): void {
        foreach([['ad_income',10000],['refund',1000],['tax',500],['payment_fee',200]] as $i=>$entry)DB::table('tbl_revenue_entries')->insert(['month'=>'2026-09','kind'=>$entry[0],'amount_cents'=>$entry[1],'reference'=>'entry-'.$i,'note'=>'Provider statement checked','created_by'=>7]);
        $r=app(RevenueSettlement::class)->calculate('2026-09');$this->assertSame(18301,$r['net_cents']);$this->assertSame(12811,$r['pool_cents']);
    }
    public function test_unmatched_payment_and_legacy_duplicate_streams_block_approval(): void {
        DB::table('tbl_transaction')->insert(['user_id'=>10,'package_id'=>1,'price'=>'5.00','description'=>'unverified','transaction_id'=>'unknown','created_at'=>'2026-09-10']);
        DB::table('tbl_artist_earnings')->insert(['artist_id'=>1,'user_id'=>10,'content_id'=>1,'content_type'=>3,'created_at'=>'2026-09-10 12:00:00']);
        $r=app(RevenueSettlement::class)->prepare('2026-09');$this->assertCount(2,$r['blockers']);
        $this->expectException(RuntimeException::class);app(RevenueSettlement::class)->approve('2026-09',$r['fingerprint'],7);
    }
    public function test_suspended_artists_are_excluded_and_current_month_cannot_settle(): void {
        DB::table('tbl_artist')->where('id',2)->update(['is_suspended'=>1]);$r=app(RevenueSettlement::class)->calculate('2026-09');$this->assertSame([1=>1],$r['streams']);
        $this->expectException(RuntimeException::class);app(RevenueSettlement::class)->prepare('2026-10');
    }
    public function test_schedule_only_prepares_review_and_force_is_disabled(): void {
        $this->artisan('earnings:settle',['--month'=>'2026-09'])->assertSuccessful();$this->assertSame(0.0,(float)DB::table('tbl_artist')->sum('wallet_balance'));
        $this->artisan('earnings:settle',['--month'=>'2026-09','--force'=>true])->assertFailed();
    }
    public function test_largest_remainder_allocations_are_deterministic(): void {
        $this->assertSame([1=>1,2=>1,3=>0],RevenueSettlement::allocate(2,[1=>1,2=>1,3=>1]));
        $this->assertSame(101,array_sum(RevenueSettlement::allocate(101,[4=>3,7=>2])));
    }
    public function test_a_manual_reconciliation_is_not_double_counted_when_order_later_matches(): void {
        DB::table('tbl_revenue_entries')->insert(['month'=>'2026-09','kind'=>'subscription_income','amount_cents'=>10001,'reference'=>'transaction:1','note'=>'Bank receipt confirmed','created_by'=>7]);
        $r=app(RevenueSettlement::class)->calculate('2026-09');$this->assertSame(10001,$r['net_cents']);$this->assertSame(0,$r['subscription_adjustment_cents']);
    }
    public function test_finance_role_cannot_approve_or_add_revenue_even_with_route_access(): void {
        $admin=new \App\Models\Admin(['id'=>9,'role'=>'finance']);$this->actingAs($admin,'admin');
        $this->post('/admin/earnings/settlement/approve',['month'=>'2026-09'])->assertForbidden();
        $this->post('/admin/earnings/settlement/entry',['month'=>'2026-09'])->assertForbidden();
        $this->assertSame(0,DB::table('tbl_revenue_entries')->count());
    }
    public function test_identity_rollout_blocks_settlement_until_secure_login_is_active(): void {
        config(['services.revenue.identity_enforced'=>false]);$r=app(RevenueSettlement::class)->prepare('2026-09');
        $this->assertStringContainsString('login enforcement',implode(' ',$r['blockers']));
        $this->expectException(RuntimeException::class);app(RevenueSettlement::class)->approve('2026-09',$r['fingerprint'],7);
    }

}
