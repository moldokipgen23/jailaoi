<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\YouTubeMusicImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
class YouTubeMusicImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        \Illuminate\Support\Facades\Schema::create('tbl_general_setting',function(\Illuminate\Database\Schema\Blueprint $t){$t->id();$t->string('key');$t->text('value');$t->timestamps();});
    }

    public function test_video_link_formats_are_normalized(): void
    {
        $service=new YouTubeMusicImport;
        foreach(['https://youtu.be/abcdefghijk?t=4','https://www.youtube.com/watch?v=abcdefghijk&list=foo','https://youtube.com/shorts/abcdefghijk','https://music.youtube.com/watch?v=abcdefghijk','https://youtube.com/embed/abcdefghijk'] as $url) $this->assertSame('abcdefghijk',$service->videoId($url));
    }
    public function test_arbitrary_hosts_credentials_ports_and_bad_ids_are_rejected_without_requests(): void
    {
        Http::preventStrayRequests();
        foreach(['http://127.0.0.1/x','https://youtube.com.evil.test/watch?v=abcdefghijk','https://youtube.com@evil.test/watch?v=abcdefghijk','https://youtube.com:443/watch?v=abcdefghijk','https://youtube.com/watch?v[]=abcdefghijk','https://youtu.be/short','file:///etc/passwd'] as $url) {
            try {(new YouTubeMusicImport)->preview($url);$this->fail('Invalid URL accepted');} catch(ValidationException $e){$this->assertNotEmpty($e->errors());}
        }
        Http::assertNothingSent();
    }
    public function test_preview_fetches_only_canonical_metadata_and_excludes_embed_html(): void
    {
        Http::preventStrayRequests();Http::fake(['www.youtube.com/oembed*'=>Http::response(['title'=>'My song','author_name'=>'Artist','thumbnail_url'=>'https://i.ytimg.com/vi/abcdefghijk/hqdefault.jpg','html'=>'<script>bad</script>'],200)]);
        $result=(new YouTubeMusicImport)->preview('https://youtu.be/abcdefghijk');
        $this->assertSame('My song',$result['title']);$this->assertArrayNotHasKey('html',$result);
        Http::assertSent(fn($request)=>str_starts_with($request->url(),'https://www.youtube.com/oembed?')&&$request['url']==='https://www.youtube.com/watch?v=abcdefghijk');
    }
    public function test_provider_failure_is_readable_and_untrusted_thumbnail_is_ignored(): void
    {
        Http::fake(['*'=>Http::response(['title'=>'Song','thumbnail_url'=>'http://127.0.0.1/private'],200)]);
        $this->assertSame('',(new YouTubeMusicImport)->preview('https://youtu.be/abcdefghijk')['thumbnail_url']);
    }
    public function test_unavailable_video_returns_validation_error(): void
    {
        Http::fake(['*'=>Http::response([],404)]);
        $this->expectException(ValidationException::class);(new YouTubeMusicImport)->preview('https://youtu.be/abcdefghijk');
    }
    public function test_full_api_import_returns_description_duration_and_best_thumbnail(): void
    {
        config(['services.youtube.api_key'=>'test-key']);
        Http::preventStrayRequests();Http::fake(['www.googleapis.com/youtube/v3/videos*'=>Http::response(['items'=>[['id'=>'abcdefghijk','status'=>['privacyStatus'=>'public'],'snippet'=>['title'=>'Original song','description'=>'Song credits','channelTitle'=>'Artist','channelId'=>'channel-id','thumbnails'=>['high'=>['url'=>'https://i.ytimg.com/vi/abcdefghijk/hqdefault.jpg']]],'contentDetails'=>['duration'=>'PT3M42S']]]],200)]);
        $data=(new YouTubeMusicImport)->preview('https://youtu.be/abcdefghijk');
        $this->assertSame('full',$data['mode']);$this->assertSame('Song credits',$data['description']);
        $this->assertSame('00:03:42',$data['duration']);$this->assertSame(222,$data['duration_seconds']);
        Http::assertSent(fn($request)=>$request->hasHeader('X-Goog-Api-Key','test-key')&&!str_contains($request->url(),'test-key'));
        $this->assertArrayNotHasKey('api_key',$data);
    }
    public function test_encrypted_key_can_be_used_without_exposing_it(): void
    {
        \App\Models\General_Setting::create(['key'=>'youtube_api_key_encrypted','value'=>\Illuminate\Support\Facades\Crypt::encryptString('secret-test-key')]);
        $this->assertSame('secret-test-key',(new YouTubeMusicImport)->apiKey());
        $this->assertStringNotContainsString('secret-test-key',\App\Models\General_Setting::first()->value);
    }
    public function test_bad_api_credentials_return_readable_error_without_silent_partial_import(): void
    {
        config(['services.youtube.api_key'=>'invalid-key']);Http::fake(['*'=>Http::response(['error'=>['message'=>'private-provider-detail']],403)]);
        try {(new YouTubeMusicImport)->preview('https://youtu.be/abcdefghijk');$this->fail('Should fail');}
        catch(ValidationException $e){$this->assertStringNotContainsString('private-provider-detail',$e->getMessage());}
        Http::assertSentCount(1);
    }

    public function test_artwork_requires_rights_confirmation_without_network_calls(): void
    {
        Http::preventStrayRequests();
        $request=\Illuminate\Http\Request::create('/user/music/import-youtube-artwork','POST',['youtube_url'=>'https://youtu.be/abcdefghijk']);
        try {(new \App\Http\Controllers\User\YouTubeImportController)->artwork($request,new YouTubeMusicImport);$this->fail('Rights confirmation required');}
        catch(ValidationException $e){$this->assertArrayHasKey('rights_confirmed',$e->errors());}
        Http::assertNothingSent();
    }
    public function test_artwork_rejects_non_images_from_fixed_thumbnail_endpoint(): void
    {
        Http::fake(['i.ytimg.com/*'=>Http::response('<script>bad</script>',200)]);
        $request=\Illuminate\Http\Request::create('/user/music/import-youtube-artwork','POST',['youtube_url'=>'https://youtu.be/abcdefghijk','rights_confirmed'=>true]);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new \App\Http\Controllers\User\YouTubeImportController)->artwork($request,new YouTubeMusicImport);
    }
    public function test_only_super_admin_can_open_key_setup(): void
    {
        $admin=new \App\Models\Admin(['role'=>'finance']);auth()->guard('admin')->setUser($admin);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new \App\Http\Controllers\Admin\YouTubeSettingsController)->index(new YouTubeMusicImport);
    }

}
