<?php
namespace Tests\Feature;

use App\Http\Controllers\Admin\NotificationController;
use App\Models\Notification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationRepairTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        Schema::create('tbl_notification',function(Blueprint $t){
            $t->id(); $t->integer('type');$t->string('title');$t->text('message');$t->string('image');
            $t->integer('storage_type')->default(0);$t->integer('user_id');$t->integer('from_user_id');$t->integer('content_id');$t->integer('status')->default(1);$t->timestamps();
        });
        Schema::create('tbl_general_setting',function(Blueprint $t){$t->id();$t->string('key');$t->string('value');});
        Http::preventStrayRequests();
    }
    private function submit(): array
    {
        $request=Request::create('/admin/notification','POST',['id'=>'','_token'=>'form-only','title'=>'Test','description'=>'Inbox message','user_id'=>999]);
        return (new NotificationController)->store($request)->getData(true);
    }
    private function configure(): void
    {
        DB::table('tbl_general_setting')->insert([['key'=>'onesignal_apid','value'=>'test-app'],['key'=>'onesignal_rest_key','value'=>'test-key']]);
    }
    public function test_form_fields_are_excluded_and_actual_live_message_column_is_used(): void
    {
        $result=$this->submit();$this->assertSame(200,$result['status']);
        $row=Notification::first();$this->assertSame('Inbox message',$row->message);$this->assertSame(0,$row->user_id);
        $this->assertSame('Inbox message',$row->toArray()['description']);
        $this->assertFalse($result['push_sent']);$this->assertSame('not_configured',$result['push_status']);Http::assertNothingSent();
    }
    public function test_existing_notifications_expose_description_to_app_and_admin(): void
    {
        Notification::create(['title'=>'Existing','message'=>'Previously saved']);
        $this->assertSame('Previously saved',Notification::first()->toArray()['description']);
    }
    public function test_provider_failure_is_not_reported_as_sent(): void
    {
        $this->configure();Http::fake(['*'=>Http::response(['errors'=>['invalid key']],401)]);
        $result=$this->submit();$this->assertFalse($result['push_sent']);$this->assertSame('provider_rejected',$result['push_status']);
        $this->assertSame(1,Notification::count());
    }
    public function test_provider_acceptance_is_checked_with_verified_transport(): void
    {
        $this->configure();Http::fake(['*'=>Http::response(['id'=>'provider-message-id'],200)]);
        $result=$this->submit();$this->assertTrue($result['push_sent']);
        Http::assertSent(fn($request)=>$request->url()==='https://api.onesignal.com/notifications' && $request->hasHeader('Authorization','Key test-key') && $request['contents']['en']==='Inbox message');
    }
    public function test_missing_provider_message_id_is_not_reported_as_sent(): void
    {
        $this->configure();Http::fake(['*'=>Http::response(['id'=>'','errors'=>['No subscriptions']],200)]);
        $this->assertFalse($this->submit()['push_sent']);
    }
    public function test_artist_eligibility_counts_csv_tracks_and_distinct_legacy_followers(): void
    {
        DB::connection()->getPdo()->sqliteCreateFunction('FIND_IN_SET',function($needle,$csv){$index=array_search((string)$needle,explode(',',(string)$csv),true);return $index===false?0:$index+1;},2);
        Schema::create('tbl_music',function(Blueprint $t){$t->id();$t->string('artist_id');$t->integer('status');});
        Schema::create('tbl_subscriber',function(Blueprint $t){$t->id();$t->integer('user_id');$t->integer('to_user_id');$t->integer('status');});
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->decimal('amount')->default(0);$t->string('settled_month')->nullable();$t->timestamps();});
        DB::table('tbl_music')->insert([['artist_id'=>'7,8','status'=>1],['artist_id'=>'70','status'=>1]]);
        DB::table('tbl_subscriber')->insert([['user_id'=>99,'to_user_id'=>42,'status'=>1],['user_id'=>99,'to_user_id'=>7,'status'=>1]]);
        $controller=new \App\Http\Controllers\User\MonetizationController;
        $method=new \ReflectionMethod($controller,'getArtistStats');
        $stats=$method->invoke($controller,new \App\Models\Artist(['id'=>7,'user_id'=>42]),new \App\Models\User(['id'=>42]));
        $this->assertSame(1,$stats['tracks']);$this->assertSame(1,$stats['followers']);
    }
    public function test_automatic_content_notification_uses_same_database_mapping(): void
    {
        $result=(new \App\Models\Common)->sendNotification(['title'=>'New music','description'=>'Listen now','image'=>'','image_url'=>'','created_at'=>'unused']);
        $this->assertTrue($result['saved']);$this->assertFalse($result['push_sent']);
        $this->assertSame('Listen now',Notification::first()->message);Http::assertNothingSent();
    }
}
