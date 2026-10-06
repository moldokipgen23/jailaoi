<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Services\AiSectionImporter;

class PlatformSyncRepairTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        Http::preventStrayRequests(); Mail::fake();
        Schema::create('tbl_general_setting',fn(Blueprint $t)=>[$t->id(),$t->string('key'),$t->text('value'),$t->timestamps()]);
        Schema::create('tbl_section',function(Blueprint $t){
            $t->id();foreach(['user_id','section_type','type','artist_id','category_id','language_id','city_id','no_of_content','is_premium','order_by_upload','order_by_play','is_paid','is_title','is_category','is_artist_name','view_all','sortable','status'] as $c)$t->integer($c)->default(0);
            foreach(['title','sub_title','screen_layout'] as $c)$t->string($c)->default('');$t->timestamps();
        });
        Schema::create('tbl_batch',function(Blueprint $t){$t->id();foreach(['input_file_id','batch_id','output_file_id','error_file_id','status'] as $c)$t->string($c)->default('');$t->timestamps();});
        Schema::create('tbl_user_summary',function(Blueprint $t){$t->id();$t->integer('user_id');$t->text('score_json')->default('{}');$t->integer('status')->default(1);$t->timestamps();});
    }
    private function payload(array $override=[]): string
    {
        return json_encode([['uid'=>42,'sections'=>[array_merge(['t'=>'Fresh Music','st'=>'Discover more','tp'=>8,'aid'=>0,'cid'=>0,'lid'=>0,'cty'=>0,'noc'=>8,'sl'=>'square'],$override)]]]);
    }
    public function test_invalid_ai_output_preserves_previous_sections(): void
    {
        DB::table('tbl_section')->insert(['user_id'=>42,'section_type'=>1,'title'=>'Existing']);
        foreach(['bad json',$this->payload(['tp'=>7]),$this->payload(['noc'=>100]),$this->payload(['cty'=>1])] as $input)$this->assertFalse((new AiSectionImporter)->replace(42,$input,1));
        $this->assertSame('Existing',DB::table('tbl_section')->value('title'));
    }
    public function test_valid_output_is_scoped_and_legacy_music_type_is_normalized(): void
    {
        DB::table('tbl_section')->insert([['user_id'=>42,'section_type'=>1,'title'=>'Old'],['user_id'=>99,'section_type'=>1,'title'=>'Other']]);
        $this->assertTrue((new AiSectionImporter)->replace(42,$this->payload(['tp'=>3]),1));
        $this->assertSame(8,DB::table('tbl_section')->where('user_id',42)->value('type'));
        $this->assertSame('Other',DB::table('tbl_section')->where('user_id',99)->value('title'));
        $this->assertFalse((new AiSectionImporter)->replace(99,$this->payload(),1));
    }
    private function batch(): void
    {
        DB::table('tbl_general_setting')->insert([['key'=>'ai_api_key','value'=>'test'],['key'=>'ai_section','value'=>'1'],['key'=>'ai_section_count','value'=>'1']]);
        \App\Models\User_Summary::create(['user_id'=>42]);
        \App\Models\Batch::create(['status'=>'completed','output_file_id'=>'output']);
        DB::table('tbl_section')->insert(['user_id'=>42,'section_type'=>1,'title'=>'Old']);
    }
    public function test_result_download_failure_preserves_batch_and_summary(): void
    {
        $this->batch();Http::fake(['*'=>Http::response([],503)]);
        $this->artisan('app:create-sections')->assertExitCode(1);
        $this->assertSame('completed',\App\Models\Batch::first()->status);
        $this->assertSame(1,\App\Models\User_Summary::count());
    }
    public function test_invalid_result_preserves_summary_for_retry_and_valid_result_completes(): void
    {
        $this->batch();$line=['custom_id'=>'42','response'=>['status_code'=>200,'body'=>['choices'=>[['message'=>['content'=>'invalid']]]]]];
        Http::fake(['*'=>Http::response(json_encode($line),200)]);
        $this->artisan('app:create-sections')->assertExitCode(0);
        $this->assertSame(1,\App\Models\User_Summary::count());$this->assertSame('Old',DB::table('tbl_section')->value('title'));
        \App\Models\Batch::first()->update(['status'=>'completed']);
        $line['response']['body']['choices'][0]['message']['content']=$this->payload();Http::swap(new \Illuminate\Http\Client\Factory);Http::preventStrayRequests();Http::fake(['*'=>Http::response(json_encode($line),200)]);
        $this->artisan('app:create-sections')->assertExitCode(0);
        $this->assertSame(0,\App\Models\User_Summary::count());$this->assertSame('processed',\App\Models\Batch::first()->status);
    }
    public function test_ad_settings_cannot_overwrite_unrelated_keys(): void
    {
        DB::table('tbl_general_setting')->insert([['key'=>'banner_ad','value'=>'0'],['key'=>'ai_api_key','value'=>'keep']]);
        $result=(new \App\Http\Controllers\Admin\AdmobSettingController)->admobAndroid(Request::create('/','POST',['banner_ad'=>'1','ai_api_key'=>'overwrite']));$this->assertSame(200,$result->getData(true)['status'],json_encode($result->getData(true)));
        $this->assertSame('keep',DB::table('tbl_general_setting')->where('key','ai_api_key')->value('value'));
        $this->assertSame('1',DB::table('tbl_general_setting')->where('key','banner_ad')->value('value'));
    }
    public function test_artist_cannot_view_or_delete_another_artists_ad(): void
    {
        Schema::create('tbl_ads',fn(Blueprint $t)=>[$t->id(),$t->integer('user_id')]);DB::table('tbl_ads')->insert(['id'=>7,'user_id'=>99]);
        $this->actingAs(new \App\Models\User(['id'=>42]),'user');$c=new \App\Http\Controllers\User\AdsController;
        $this->assertSame(400,$c->edit(7)->getData(true)['status']);
        $this->assertSame(400,$c->destroy(7)->getData(true)['status']);$this->assertSame(1,DB::table('tbl_ads')->count());
        $c->show(7);$this->assertSame(1,DB::table('tbl_ads')->count());
    }
    public function test_only_approved_withdrawal_can_be_marked_paid_once(): void
    {
        Schema::create('tbl_withdrawal_requests',function(Blueprint $t){$t->id();$t->integer('user_id')->default(0);$t->string('status');$t->string('payment_note')->nullable();$t->timestamp('paid_at')->nullable();$t->timestamp('processed_at')->nullable();$t->timestamps();});
        $c=new \App\Http\Controllers\Admin\WithdrawalController;
        foreach(['pending','rejected','paid'] as $status){DB::table('tbl_withdrawal_requests')->insert(['id'=>1,'status'=>$status]);$this->assertSame(400,$c->markPaid(Request::create('/','POST',['request_id'=>1]))->getData(true)['status']);DB::table('tbl_withdrawal_requests')->delete();}
        DB::table('tbl_withdrawal_requests')->insert(['id'=>1,'status'=>'approved']);$this->assertSame(200,$c->markPaid(Request::create('/','POST',['request_id'=>1]))->getData(true)['status']);
        $this->assertSame(400,$c->markPaid(Request::create('/','POST',['request_id'=>1]))->getData(true)['status']);Mail::assertNothingSent();
    }
    public function test_native_and_web_artist_dashboard_counts_match_live_schema(): void
    {
        $pdo=DB::connection()->getPdo();
        $pdo->sqliteCreateFunction('FIND_IN_SET',fn($id,$csv)=>in_array((string)$id,explode(',',(string)$csv),true)?1:0,2);
        $pdo->sqliteCreateFunction('DATE_FORMAT',fn($date,$format)=>date('Y-m',strtotime($date)),2);
        Schema::create('tbl_user',fn(Blueprint $t)=>[$t->id(),$t->string('full_name')->default('')]);
        Schema::create('tbl_artist',fn(Blueprint $t)=>[$t->id(),$t->integer('user_id'),$t->string('image')->default(''),$t->decimal('wallet_balance')->default(0)]);
        Schema::create('tbl_song',fn(Blueprint $t)=>[$t->id(),$t->integer('artist_id'),$t->integer('status'),$t->string('name'),$t->string('image')->default(''),$t->integer('total_play')]);
        foreach(['tbl_music','tbl_podcast'] as $table)Schema::create($table,fn(Blueprint $t)=>[$t->id(),$t->string('artist_id'),$t->integer('status'),$t->integer('total_play')]);
        Schema::create('tbl_subscriber',fn(Blueprint $t)=>[$t->id(),$t->integer('user_id'),$t->integer('to_user_id'),$t->integer('status')]);
        Schema::create('tbl_user_action',function(Blueprint $t){$t->id();$t->integer('user_id');$t->string('artist_id');$t->integer('action');$t->integer('status');$t->timestamps();});
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->decimal('amount')->default(0);$t->string('settled_month')->nullable();$t->timestamps();});
        foreach(['tbl_artist_kyc','tbl_monetization_applications'] as $table)Schema::create($table,function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('status');$t->timestamps();});
        Schema::create('tbl_withdrawal_requests',fn(Blueprint $t)=>[$t->id(),$t->integer('artist_id'),$t->string('status'),$t->decimal('amount')]);
        DB::table('tbl_user')->insert(['id'=>42]);DB::table('tbl_artist')->insert(['id'=>7,'user_id'=>42]);
        DB::table('tbl_song')->insert(['id'=>1,'artist_id'=>7,'status'=>1,'name'=>'Radio title','total_play'=>2]);
        DB::table('tbl_subscriber')->insert([['user_id'=>10,'to_user_id'=>42,'status'=>1],['user_id'=>10,'to_user_id'=>7,'status'=>1],['user_id'=>11,'to_user_id'=>7,'status'=>0]]);
        foreach([['user_id'=>10,'artist_id'=>'7,8','action'=>1],['user_id'=>11,'artist_id'=>'70','action'=>1],['user_id'=>12,'artist_id'=>'7','action'=>2],['user_id'=>0,'artist_id'=>'7','action'=>1]] as $row)DB::table('tbl_user_action')->insert($row+['status'=>1,'created_at'=>now()]);
        $api=(new \App\Http\Controllers\Api\ArtistController)->get_artist_dashboard(Request::create('/','POST',['user_id'=>42]));
        $this->assertIsArray($api);$this->assertSame(200,$api['status']);$this->assertSame('Radio title',$api['result'][0]['recent_tracks'][0]['title']);
        $this->actingAs(new \App\Models\User(['id'=>42]),'user');
        $web=(new \App\Http\Controllers\User\DashboardController)->index()->getData();
        $this->assertSame(1,$api['result'][0]['monthly_listeners']);$this->assertSame($api['result'][0]['monthly_listeners'],$web['monthlyListeners']);
        $this->assertSame(1,$web['totalFollowers']);$this->assertSame($api['result'][0]['total_followers'],$web['totalFollowers']);
    }
}
