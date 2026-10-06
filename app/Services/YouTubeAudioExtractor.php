<?php
namespace App\Services;
use Symfony\Component\Process\Process;
class YouTubeAudioExtractor
{
    public function available(): bool
    {
        foreach (['python', 'downloader', 'ffmpeg', 'ffprobe'] as $binary) {
            $path = config('youtube_audio.'.$binary);
            if (!is_string($path) || !is_file($path) || ($binary !== 'downloader' && !is_executable($path))) return false;
        }
        return true;
    }
    protected function run(array $command, int $timeout): string
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();
        if (!$process->isSuccessful()) {
            $error = strtolower($process->getErrorOutput());
            if (str_contains($error, 'not a bot') || str_contains($error, 'sign in')) throw new \RuntimeException('YouTube blocked this download. Upload your original audio instead.');
            throw new \RuntimeException('YouTube audio could not be imported. Try later or upload your original audio.');
        }
        return $process->getOutput();
    }
    public function extract(string $videoId, string $directory, callable $phase): array
    {
        if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) || !is_dir($directory) || !$this->available()) throw new \RuntimeException('Audio import is temporarily unavailable. Use manual upload.');
        $phase('downloading');
        $this->run([config('youtube_audio.python'), config('youtube_audio.downloader'), '--ignore-config', '--no-playlist', '--no-cache-dir', '--no-progress', '--no-warnings', '--socket-timeout', '15', '--retries', '0', '--fragment-retries', '0', '--concurrent-fragments', '1', '--max-filesize', (string)config('youtube_audio.max_bytes'), '--match-filter', 'duration <= '.config('youtube_audio.max_seconds').' & !is_live', '--js-runtimes', 'node:/usr/bin/node', '-f', 'bestaudio', '-o', $directory.'/source.%(ext)s', '--', 'https://www.youtube.com/watch?v='.$videoId], 150);
        $sources = array_values(array_filter(glob($directory.'/source.*') ?: [], fn($path)=>is_file($path) && !is_link($path) && !str_ends_with($path, '.part') && !str_ends_with($path, '.ytdl')));
        if (count($sources) !== 1 || filesize($sources[0]) <= 0 || filesize($sources[0]) > config('youtube_audio.max_bytes')) throw new \RuntimeException('The video is unavailable or exceeds the 15-minute / 100 MB import limit.');
        $source = $sources[0];
        $probe = json_decode($this->run([config('youtube_audio.ffprobe'), '-v', 'error', '-protocol_whitelist', 'file,pipe', '-show_entries', 'format=duration:stream=codec_type', '-of', 'json', $source], 15), true);
        $duration = (float)($probe['format']['duration'] ?? 0);
        $audio = array_filter($probe['streams'] ?? [], fn($stream)=>($stream['codec_type'] ?? '') === 'audio');
        if (!$audio || !is_finite($duration) || $duration < 1 || $duration > config('youtube_audio.max_seconds')) throw new \RuntimeException('The video does not contain valid audio within the 15-minute limit.');
        $phase('converting');
        $output = $directory.'/audio.mp3';
        $this->run([config('youtube_audio.ffmpeg'), '-nostdin', '-v', 'error', '-protocol_whitelist', 'file,pipe', '-i', $source, '-map', '0:a:0', '-vn', '-map_metadata', '-1', '-c:a', 'libmp3lame', '-b:a', '192k', '-threads', '1', '-y', $output], 90);
        if (!is_file($output) || filesize($output) < 128 || filesize($output) > config('youtube_audio.max_bytes')) throw new \RuntimeException('Audio preparation failed. Use manual upload.');
        return ['path'=>$output, 'duration_seconds'=>(int)floor($duration)];
    }
}
