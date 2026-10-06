<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\YouTubeAudioImport;
use App\Services\YouTubeAudioAccess;
use App\Services\YouTubeAudioExtractor;
use App\Services\YouTubeMusicImport;
use App\Http\Controllers\User\YouTubeImportController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\Request;
class YouTubeAudioImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        Schema::create('tbl_general_setting', function(Blueprint $t){$t->id();$t->string('key');$t->text('value');$t->timestamps();});
        Schema::create('tbl_artist', function(Blueprint $t){$t->id();$t->integer('user_id');$t->string('name');$t->integer('is_suspended')->default(0);$t->timestamps();});
        Schema::create('tbl_user', function(Blueprint $t){$t->id();$t->string('role');$t->integer('status')->default(1);});
        (require base_path('database/migrations/2026_10_06_000002_create_artist_youtube_imports.php'))->up();
        \App\Models\User::insert(['id'=>7,'role'=>'artist','status'=>1]);
        auth()->guard('user')->setUser(\App\Models\User::find(7));
        \App\Models\Artist::insert(['user_id'=>7,'name'=>'Test artist','is_suspended'=>0]);
        Bus::fake();
    }
    private function grant(): void
    {
        \App\Models\General_Setting::create(['key'=>'youtube_audio_access','value'=>json_encode(['enabled'=>true,'user_ids'=>[7]])]);
    }
    private function start(array $input=[])
    {
        $extractor=\Mockery::mock(YouTubeAudioExtractor::class);$extractor->shouldReceive('available')->andReturn(true);
        return (new YouTubeImportController)->startAudio(Request::create('/', 'POST', array_merge(['youtube_url'=>'https://youtu.be/abcdefghijk','rights_confirmed'=>true],$input)), new YouTubeAudioAccess, $extractor, new YouTubeMusicImport);
    }
    private function record(array $data=[]): YouTubeAudioImport
    {
        return YouTubeAudioImport::create(array_merge(['id'=>(string)\Illuminate\Support\Str::uuid(),'user_id'=>7,'video_id'=>'abcdefghijk','status'=>'queued','phase'=>'queued','rights_confirmed_at'=>now(),'expires_at'=>now()->addDay()],$data));
    }
    public function test_disabled_feature_and_unlisted_artist_cannot_start(): void
    {
        $this->assertFalse((new YouTubeAudioAccess)->allows(7));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->start();
    }
    public function test_grant_does_not_allow_suspended_artists(): void
    {
        $this->grant();$this->assertTrue((new YouTubeAudioAccess)->allows(7));
        \App\Models\Artist::where('user_id',7)->update(['is_suspended'=>1]);
        $this->assertFalse((new YouTubeAudioAccess)->allows(7));
    }
    public function test_start_requires_audio_rights_confirmation(): void
    {
        $this->grant();
        try {$this->start(['rights_confirmed'=>false]);$this->fail('Missing rights accepted');}catch(\Illuminate\Validation\ValidationException $e){$this->assertArrayHasKey('rights_confirmed',$e->errors());}
        $this->assertSame(0,YouTubeAudioImport::count());Bus::assertNothingDispatched();
    }
    public function test_start_queues_one_canonical_job_and_deduplicates_active_attempts(): void
    {
        $this->grant();$first=$this->start();$second=$this->start();
        $this->assertSame(202,$first->status());$this->assertSame(202,$second->status());
        $this->assertSame(1,YouTubeAudioImport::count());
        $this->assertSame('abcdefghijk',YouTubeAudioImport::first()->video_id);
        Bus::assertDispatched(\App\Jobs\ImportYouTubeAudio::class, fn($job)=>$job->connection==='youtube_audio' && $job->queue==='youtube-audio');
        Bus::assertDispatchedTimes(\App\Jobs\ImportYouTubeAudio::class,1);
    }
    public function test_failed_attempts_count_toward_daily_limit(): void
    {
        $this->grant();for($i=0;$i<3;$i++)$this->record(['status'=>'failed']);
        $this->assertSame(429,$this->start()->status());Bus::assertNothingDispatched();
    }
    public function test_status_is_scoped_to_the_signed_in_artist(): void
    {
        $other=$this->record(['user_id'=>8]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new YouTubeImportController)->audioStatus($other->id);
    }
    public function test_expired_result_does_not_expose_a_publishable_filename(): void
    {
        $import=$this->record(['status'=>'ready','phase'=>'ready','filename'=>'private.mp3','duration_seconds'=>12,'expires_at'=>now()->subMinute()]);
        $data=(new YouTubeImportController)->audioStatus($import->id)->getData(true)['data'];
        $this->assertSame('expired',$data['status']);$this->assertNull($data['filename']);$this->assertNull($data['duration']);
    }
    public function test_worker_stops_when_access_is_revoked(): void
    {
        $import=$this->record();$extractor=\Mockery::mock(YouTubeAudioExtractor::class);$extractor->shouldNotReceive('extract');
        (new \App\Jobs\ImportYouTubeAudio($import->id))->handle($extractor,new YouTubeMusicImport,new YouTubeAudioAccess);
        $this->assertSame('failed',$import->fresh()->status);$this->assertNull($import->fresh()->filename);
    }
    public function test_extractor_rejects_shell_input_before_starting_processes(): void
    {
        $extractor=new YouTubeAudioExtractor;
        $this->expectException(\RuntimeException::class);
        $extractor->extract('abc; touch /tmp/injected',sys_get_temp_dir(),fn($phase)=>null);
    }
    public function test_stalled_attempts_expire_without_altering_catalogue(): void
    {
        $import=$this->record(['status'=>'processing','updated_at'=>now()->subMinutes(20)]);
        $this->artisan('youtube:clean-imports')->assertSuccessful();
        $this->assertSame('failed',$import->fresh()->status);
    }
    public function test_failed_worker_does_not_leak_raw_exception(): void
    {
        $import=$this->record();(new \App\Jobs\ImportYouTubeAudio($import->id))->failed(new \RuntimeException('secret password /home/file'));
        $this->assertStringNotContainsString('secret',$import->fresh()->message);$this->assertSame('failed',$import->fresh()->status);
    }
    public function test_non_super_admin_cannot_grant_audio_import_access(): void
    {
        auth()->guard('admin')->setUser(new \App\Models\Admin(['role'=>'finance']));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new \App\Http\Controllers\Admin\YouTubeSettingsController)->saveAudio(Request::create('/','POST',['enabled'=>true,'user_ids'=>[7]]));
    }
    public function test_monthly_limit_blocks_imports_even_when_today_is_unused(): void
    {
        $this->grant();
        for($i=0;$i<20;$i++)$this->record(['status'=>'failed','created_at'=>now()->subDay(),'updated_at'=>now()->subDay()]);
        $this->assertSame(429,$this->start()->status());Bus::assertNothingDispatched();
    }
    public function test_worker_surfaces_bot_block_and_cleans_temporary_files(): void
    {
        $this->grant();$import=$this->record();
        $extractor=\Mockery::mock(YouTubeAudioExtractor::class);
        $extractor->shouldReceive('extract')->once()->andThrow(new \RuntimeException('YouTube blocked this download. Upload your original audio instead.'));
        $metadata=\Mockery::mock(YouTubeMusicImport::class);$metadata->shouldReceive('preview')->andReturn(['title'=>'My original song','duration_seconds'=>30]);
        (new \App\Jobs\ImportYouTubeAudio($import->id))->handle($extractor,$metadata,new YouTubeAudioAccess);
        $this->assertSame('failed',$import->fresh()->status);$this->assertStringContainsString('blocked',$import->fresh()->message);
        $this->assertDirectoryDoesNotExist(storage_path('app/private/youtube-imports/'.$import->id));
    }
    public function test_ready_audio_is_owned_and_publishing_uses_server_duration(): void
    {
        $this->grant();$import=$this->record();
        $extractor=\Mockery::mock(YouTubeAudioExtractor::class);
        $extractor->shouldReceive('extract')->once()->andReturnUsing(function($id,$directory,$phase){
            $phase('converting');$path=$directory.'/audio.mp3';file_put_contents($path,str_repeat('a',512));return ['path'=>$path,'duration_seconds'=>34];
        });
        $metadata=\Mockery::mock(YouTubeMusicImport::class);$metadata->shouldReceive('preview')->andReturn(['title'=>'My original song','duration_seconds'=>35]);
        \App\Models\Artist::where('user_id',7)->update(['name'=>'Test artist '.$import->id]);
        $previousMask=umask(0077);
        try { (new \App\Jobs\ImportYouTubeAudio($import->id))->handle($extractor,$metadata,new YouTubeAudioAccess); } finally { umask($previousMask); }
        $ready=$import->fresh();$this->assertSame('ready',$ready->status);$this->assertSame(34,$ready->duration_seconds);
        $this->assertTrue(\Illuminate\Support\Facades\Cache::get('artist-upload:7:'.hash('sha256',$ready->filename)));
        $data=(new YouTubeImportController)->audioStatus($import->id)->getData(true)['data'];$this->assertSame('00:00:34',$data['duration']);
        // No catalogue publication or revenue credit happens on import.
        $this->assertNull($ready->content_id);
        $request = Request::create('/','POST',['content_upload_type'=>'server_video','youtube_audio_import_id'=>$ready->id,'music'=>$ready->filename,'content_duration'=>'01:00:00']);
        (new \App\Http\Controllers\User\MusicController)->store($request);
        $this->assertSame('00:00:34',$request->content_duration);
        $ready->update(['content_id'=>12]);
        $this->assertSame(422,(new \App\Http\Controllers\User\MusicController)->store(Request::create('/','POST',['music'=>$ready->filename]))->status());
        $publicPath=storage_path('app/public/music/'.$ready->filename);
        clearstatcache();
        $this->assertSame(0755,fileperms(dirname($publicPath)) & 0777);
        $this->assertSame(0644,fileperms($publicPath) & 0777);
        @unlink($publicPath);@rmdir(dirname($publicPath));
    }
    public function test_other_artists_import_cannot_be_published(): void
    {
        $other=$this->record(['user_id'=>8,'status'=>'ready','filename'=>'other.mp3','storage_driver'=>'local','duration_seconds'=>30]);
        $response=(new \App\Http\Controllers\User\MusicController)->store(Request::create('/','POST',['youtube_audio_import_id'=>$other->id,'music'=>'other.mp3']));
        $this->assertSame(422,$response->status());
    }

    public function test_published_import_is_not_returned_as_reusable_audio(): void
    {
        $import=$this->record(['status'=>'ready','content_id'=>10,'filename'=>'published.mp3','duration_seconds'=>30]);
        $data=(new YouTubeImportController)->audioStatus($import->id)->getData(true)['data'];
        $this->assertSame('published',$data['status']);$this->assertNull($data['filename']);
    }

}
