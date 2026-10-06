<?php
namespace App\Jobs;
use App\Models\YouTubeAudioImport;
use App\Services\YouTubeAudioAccess;
use App\Services\YouTubeAudioExtractor;
use App\Services\YouTubeMusicImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class ImportYouTubeAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 1;
    public int $timeout = 600;
    public bool $failOnTimeout = true;
    public function __construct(public string $importId)
    {
        $this->onConnection('youtube_audio')->onQueue('youtube-audio');
    }
    public function handle(YouTubeAudioExtractor $extractor, YouTubeMusicImport $metadata, YouTubeAudioAccess $access): void
    {
        $import = YouTubeAudioImport::find($this->importId);
        if (!$import || $import->status !== 'queued') return;
        if (!YouTubeAudioImport::whereKey($import->id)->where('status', 'queued')->update(['status'=>'processing', 'phase'=>'checking'])) return;
        $directory = storage_path('app/private/youtube-imports/'.$import->id);
        try {
            if (!$access->allows((int)$import->user_id) || $import->expires_at->isPast()) throw new \RuntimeException('Audio import access is unavailable. Use manual upload.');
            $user = \App\Models\User::find($import->user_id);
            if (!$user || $user->role !== 'artist' || (int)$user->status !== 1) throw new \RuntimeException('Artist access is unavailable.');
            $data = $metadata->preview('https://www.youtube.com/watch?v='.$import->video_id);
            if (($data['duration_seconds'] ?? 0) > config('youtube_audio.max_seconds')) throw new \RuntimeException('Use a video under 15 minutes.');
            File::makeDirectory($directory, 0700, true, true);
            $import->update(['title'=>$data['title']]);
            $result = $extractor->extract($import->video_id, $directory, fn($phase)=>$import->update(['phase'=>$phase]));
            if (!$access->allows((int)$import->user_id)) throw new \RuntimeException('Audio import access was disabled.');
            $import->update(['phase'=>'saving']);
            $artist = \App\Models\Artist::where('user_id', $import->user_id)->firstOrFail();
            chmod($result['path'], 0644); // Published local audio must be readable by the web server.
            $upload = new \Illuminate\Http\UploadedFile($result['path'], 'youtube-audio.mp3', 'audio/mpeg', null, true);
            $artistSlug = Str::slug($artist->name) ?: 'artist';
            $publicDirectory = storage_path('app/public/music/'.$artistSlug);
            $publicDirectoryExisted = is_dir($publicDirectory);
            $filename = (new \App\Models\Common)->saveAudioFile($upload, 'music', 'yt_'.str_replace('-', '', $import->id).'_', $artistSlug);
            // Only a newly created PUBLIC playback directory needs web-server read access.
            // Existing folders, extraction files and the worker's restrictive umask remain untouched.
            if (getAudioStorageDriver() === 'local' && !$publicDirectoryExisted && is_dir($publicDirectory)) chmod($publicDirectory, 0755);
            if (!is_string($filename) || $filename === '' || str_contains($filename, '..')) throw new \RuntimeException('Audio storage failed. Use manual upload or contact support.');
            if (getAudioStorageDriver() === 'r2' && !\Illuminate\Support\Facades\Storage::disk('r2')->exists('music/'.$filename)) throw new \RuntimeException('Audio storage failed. Use manual upload or contact support.');
            if (getAudioStorageDriver() === 'local' && !is_file(storage_path('app/public/music/'.$filename))) throw new \RuntimeException('Audio storage failed. Use manual upload or contact support.');
            if (getAudioStorageDriver() === 'local') chmod(storage_path('app/public/music/'.$filename), 0644);
            Cache::put('artist-upload:'.$import->user_id.':'.hash('sha256', $filename), true, $import->expires_at);
            Cache::put('artist-youtube-upload:'.$import->user_id.':'.hash('sha256', $filename), $import->id, $import->expires_at);
            $import->update(['status'=>'ready', 'phase'=>'ready', 'filename'=>$filename, 'storage_driver'=>getAudioStorageDriver(), 'duration_seconds'=>$result['duration_seconds'], 'message'=>null]);
        } catch (\Throwable $e) {
            $safe = $e instanceof \RuntimeException && !($e instanceof \Symfony\Component\Process\Exception\ProcessTimedOutException) ? $e->getMessage() : 'Audio import failed. Try later or upload your original audio.';
            // Never expose raw provider output, credentials or filesystem paths.
            $allowed = ['YouTube blocked this download. Upload your original audio instead.', 'YouTube audio could not be imported. Try later or upload your original audio.', 'Audio import is temporarily unavailable. Use manual upload.', 'The video is unavailable or exceeds the 15-minute / 100 MB import limit.', 'The video does not contain valid audio within the 15-minute limit.', 'Audio preparation failed. Use manual upload.', 'Audio import access is unavailable. Use manual upload.', 'Artist access is unavailable.', 'Use a video under 15 minutes.', 'Audio import access was disabled.', 'Audio storage failed. Use manual upload or contact support.'];
            $import->update(['status'=>'failed', 'phase'=>'failed', 'message'=>in_array($safe, $allowed, true) ? $safe : 'Audio import failed. Try later or upload your original audio.']);
        } finally {
            File::deleteDirectory($directory);
        }
    }
    public function failed(?\Throwable $exception): void
    {
        YouTubeAudioImport::whereKey($this->importId)->whereIn('status', ['queued', 'processing'])->update(['status'=>'failed', 'phase'=>'failed', 'message'=>'Audio import timed out or the worker stopped. Upload manually or try later.']);
        File::deleteDirectory(storage_path('app/private/youtube-imports/'.$this->importId));
    }
}
