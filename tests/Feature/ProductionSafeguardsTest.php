<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ProductionSafeguardsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);Mail::fake();
    }
    public function test_approval_cannot_reopen_paid_or_rejected_withdrawals(): void
    {
        Schema::create('tbl_withdrawal_requests',function(Blueprint $t){$t->id();$t->string('status');$t->timestamp('processed_at')->nullable();$t->string('admin_note')->nullable();$t->timestamps();});
        $c=new \App\Http\Controllers\Admin\WithdrawalController;
        foreach(['paid','rejected','approved'] as $status){DB::table('tbl_withdrawal_requests')->insert(['id'=>1,'status'=>$status]);$this->assertSame(400,$c->approve(Request::create('/','POST',['request_id'=>1]))->getData(true)['status']);$this->assertSame($status,DB::table('tbl_withdrawal_requests')->value('status'));DB::table('tbl_withdrawal_requests')->delete();}
        DB::table('tbl_withdrawal_requests')->insert(['id'=>1,'status'=>'pending']);$this->assertSame(200,$c->approve(Request::create('/','POST',['request_id'=>1]))->getData(true)['status']);
    }
    public function test_daily_credits_dedupe_collaborators_and_legacy_music_type(): void
    {
        Schema::create('tbl_user',fn(Blueprint $t)=>[$t->id(),$t->integer('status')]);
        Schema::create('tbl_artist',fn(Blueprint $t)=>[$t->id(),$t->integer('is_suspended')->default(0),$t->decimal('wallet_balance')->default(0)]);
        Schema::create('tbl_music',fn(Blueprint $t)=>[$t->id(),$t->string('artist_id')]);
        Schema::create('tbl_general_setting',fn(Blueprint $t)=>[$t->id(),$t->string('key'),$t->string('value')]);
        Schema::create('tbl_monetization_applications',fn(Blueprint $t)=>[$t->id(),$t->integer('artist_id'),$t->string('status')]);
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();foreach(['artist_id','user_id','content_id','content_type'] as $c)$t->integer($c);$t->decimal('amount');$t->timestamps();});
        DB::table('tbl_user')->insert(['id'=>42,'status'=>1]);DB::table('tbl_artist')->insert([['id'=>7],['id'=>8]]);DB::table('tbl_music')->insert(['id'=>1,'artist_id'=>'7,8']);
        $c=new \App\Http\Controllers\Api\HomeController;$method=new \ReflectionMethod($c,'creditArtistEarning');
        $method->invoke($c,3,1,42);$method->invoke($c,8,1,42);$this->assertSame(2,DB::table('tbl_artist_earnings')->count());
        $this->assertSame([8],DB::table('tbl_artist_earnings')->distinct()->pluck('content_type')->toArray());
        $method->invoke($c,8,1,999);$this->assertSame(2,DB::table('tbl_artist_earnings')->count());
    }
    public function test_failed_database_dump_never_creates_a_successful_backup(): void
    {
        $storage=sys_get_temp_dir().'/jailaoi-backup-test-'.bin2hex(random_bytes(5));$this->app->useStoragePath($storage);
        mkdir($storage.'/app/private',0700,true);file_put_contents($storage.'/app/private/backup-public-certificate.pem','test certificate');
        $backup=new class extends \App\Services\EncryptedBackup {
            public array $calls=[];
            protected function run(array $command):void{$this->calls[]=$command[0];throw new \RuntimeException('Simulated dump failure');}
        };
        try{$backup->create();$this->fail('Dump failure must stop backup');}catch(\RuntimeException $e){$this->assertSame(['mysqldump'],$backup->calls);}
        $this->assertFileDoesNotExist($storage.'/app/private/backups/latest.json');
        $this->assertSame([],glob($storage.'/app/private/backups/work-*'));
        $this->assertSame([],glob($storage.'/app/private/backups/*.cms'));
        (new \Illuminate\Filesystem\Filesystem)->deleteDirectory($storage);
    }
    public function test_chunk_uploads_and_admin_maintenance_require_login(): void
    {
        $this->post('/admin/music/saveChunk')->assertRedirect();
        $this->post('/admin/artisan')->assertRedirect();
        $this->get('/admin/operations/health')->assertRedirect();
    }
}
