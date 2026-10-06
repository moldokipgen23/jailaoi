<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\YouTubeMusicImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
class YouTubeMusicImportTest extends TestCase
{
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
}
