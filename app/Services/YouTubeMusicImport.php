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
    public function apiKey(): string
    {
        $key = (string) config('services.youtube.api_key', '');
        if ($key !== '') return $key;
        $encrypted = \App\Models\General_Setting::where('key', 'youtube_api_key_encrypted')->value('value');
        if (!$encrypted) return '';
        try { return \Illuminate\Support\Facades\Crypt::decryptString($encrypted); }
        catch (\Throwable $e) { return ''; }
    }
    private function apiPreview(string $id, string $key): array
    {
        $response = Http::acceptJson()->withHeaders(['X-Goog-Api-Key'=>$key])->connectTimeout(5)->timeout(12)->withOptions(['allow_redirects'=>false])->get('https://www.googleapis.com/youtube/v3/videos', ['id'=>$id, 'part'=>'snippet,contentDetails,status']);
        if (!$response->successful()) throw ValidationException::withMessages(['youtube_url'=>'Full YouTube import is unavailable. Ask the administrator to check the API key, restrictions and quota, or enter details manually.']);
        $item = $response->json('items.0');
        if (!is_array($item) || ($item['id'] ?? '') !== $id || ($item['status']['privacyStatus'] ?? '') !== 'public') throw ValidationException::withMessages(['youtube_url'=>'Use an available public YouTube video.']);
        $snippet = $item['snippet'] ?? [];
        if (!is_string($snippet['title'] ?? null)) throw new \RuntimeException('Invalid video title.');
        $thumbnail = '';
        foreach (['maxres', 'standard', 'high', 'medium', 'default'] as $size) {
            $candidate = $snippet['thumbnails'][$size]['url'] ?? '';
            $parts = is_string($candidate) ? parse_url($candidate) : [];
            if (($parts['scheme'] ?? '') === 'https' && ($parts['host'] ?? '') === 'i.ytimg.com' && !isset($parts['port']) && !isset($parts['user'])) { $thumbnail=$candidate; break; }
        }
        $seconds = null;
        if (preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/', $item['contentDetails']['duration'] ?? '', $m)) {
            $total = (int)($m[1] ?? 0)*3600 + (int)($m[2] ?? 0)*60 + (int)($m[3] ?? 0);
            if ($total > 0 && $total < 86400) $seconds = $total;
        }
        return ['video_id'=>$id, 'youtube_url'=>'https://www.youtube.com/watch?v='.$id, 'title'=>mb_substr($snippet['title'],0,255), 'channel'=>(string)($snippet['channelTitle'] ?? ''), 'channel_id'=>(string)($snippet['channelId'] ?? ''), 'description'=>mb_substr((string)($snippet['description'] ?? ''),0,5000), 'duration_seconds'=>$seconds, 'duration'=>$seconds === null ? null : sprintf('%02d:%02d:%02d', intdiv($seconds,3600), intdiv($seconds%3600,60), $seconds%60), 'thumbnail_url'=>$thumbnail, 'mode'=>'full'];
    }
    public function preview(string $url): array
    {
        $id = $this->videoId($url);
        $key = $this->apiKey();
        if ($key !== '') return $this->apiPreview($id, $key);
        $canonical = 'https://www.youtube.com/watch?v=' . $id;
        $response = Http::acceptJson()->connectTimeout(5)->timeout(12)->withOptions(['allow_redirects'=>false])->get('https://www.youtube.com/oembed', ['url'=>$canonical, 'format'=>'json']);
        if (!$response->successful()) throw ValidationException::withMessages(['youtube_url'=>'This video is unavailable for import. Use a public video link or enter details manually.']);
        $data = $response->json();
        if (!is_array($data) || !is_string($data['title'] ?? null) || trim($data['title']) === '') throw new \RuntimeException('Invalid YouTube metadata.');
        $thumbnail = is_string($data['thumbnail_url'] ?? null) ? $data['thumbnail_url'] : '';
        $image = parse_url($thumbnail);
        if (($image['scheme'] ?? '') !== 'https' || ($image['host'] ?? '') !== 'i.ytimg.com' || isset($image['port']) || isset($image['user'])) $thumbnail = '';
        return ['video_id'=>$id, 'youtube_url'=>$canonical, 'title'=>mb_substr($data['title'], 0, 255), 'channel'=>mb_substr((string)($data['author_name'] ?? ''), 0, 255), 'thumbnail_url'=>$thumbnail, 'mode'=>'basic', 'description'=>null, 'duration'=>null, 'duration_seconds'=>null];
    }
}
