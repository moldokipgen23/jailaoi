<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
class YouTubeMusicImport
{
    public function videoId(string $url): string
    {
        $parts = parse_url(trim($url));
        $host = strtolower($parts['host'] ?? '');
        if (!$parts || !in_array($parts['scheme'] ?? '', ['https', 'http'], true) || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) $this->invalid();
        $path = $parts['path'] ?? '';
        if ($host === 'youtu.be') $id = trim($path, '/');
        elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com'], true)) {
            if ($path === '/watch') { parse_str($parts['query'] ?? '', $query); $id = $query['v'] ?? ''; }
            elseif (preg_match('~^/(?:shorts|embed|live)/([a-zA-Z0-9_-]{11})/?$~', $path, $match)) $id = $match[1];
            else $this->invalid();
        } else $this->invalid();
        if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{11}$/', $id)) $this->invalid();
        return $id;
    }
    private function invalid(): never
    {
        throw ValidationException::withMessages(['youtube_url'=>'Enter a valid YouTube video link.']);
    }
    public function preview(string $url): array
    {
        $id = $this->videoId($url);
        $canonical = 'https://www.youtube.com/watch?v=' . $id;
        $response = Http::acceptJson()->connectTimeout(5)->timeout(12)->withOptions(['allow_redirects'=>false])->get('https://www.youtube.com/oembed', ['url'=>$canonical, 'format'=>'json']);
        if (!$response->successful()) throw ValidationException::withMessages(['youtube_url'=>'This video is unavailable for import. Use a public video link or enter details manually.']);
        $data = $response->json();
        if (!is_array($data) || !is_string($data['title'] ?? null) || trim($data['title']) === '') throw new \RuntimeException('Invalid YouTube metadata.');
        $thumbnail = is_string($data['thumbnail_url'] ?? null) ? $data['thumbnail_url'] : '';
        $image = parse_url($thumbnail);
        if (($image['scheme'] ?? '') !== 'https' || ($image['host'] ?? '') !== 'i.ytimg.com' || isset($image['port']) || isset($image['user'])) $thumbnail = '';
        return ['video_id'=>$id, 'youtube_url'=>$canonical, 'title'=>mb_substr($data['title'], 0, 255), 'channel'=>mb_substr((string)($data['author_name'] ?? ''), 0, 255), 'thumbnail_url'=>$thumbnail];
    }
}
