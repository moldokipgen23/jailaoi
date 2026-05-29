<?php

namespace App\Services;

use getID3;
use Illuminate\Support\Facades\Log;

class AudioProcessor
{
    private getID3 $getID3;
    private bool $ffmpegAvailable = false;
    private string $ffmpegPath = '';

    public function __construct()
    {
        $this->getID3 = new getID3;
        $this->detectFfmpeg();
    }

    private function detectFfmpeg(): void
    {
        $paths = ['ffmpeg', '/usr/bin/ffmpeg', '/usr/local/bin/ffmpeg', '/opt/cpanel/ea-php84/root/usr/bin/ffmpeg'];
        foreach ($paths as $path) {
            $output = shell_exec("which {$path} 2>/dev/null") ?: shell_exec("{$path} -version 2>/dev/null");
            if ($output) {
                $this->ffmpegAvailable = true;
                $this->ffmpegPath = $path;
                break;
            }
        }
    }

    public function isFfmpegAvailable(): bool
    {
        return $this->ffmpegAvailable;
    }

    public function process(string $filePath): array
    {
        $result = [
            'duration' => '0',
            'waveform' => null,
            'compressed' => false,
        ];

        if (!file_exists($filePath)) {
            return $result;
        }

        // 1. Extract duration + metadata via getID3
        $fileInfo = $this->getID3->analyze($filePath);
        $result['duration'] = $this->formatDuration($fileInfo['playtime_seconds'] ?? 0);

        // 2. Compress audio + generate waveform if FFmpeg available
        if ($this->ffmpegAvailable) {
            $this->compressAudio($filePath);
            $result['compressed'] = true;
            $result['waveform'] = $this->generateWaveform($filePath);
        }

        return $result;
    }

    private function compressAudio(string $filePath): void
    {
        $tmpPath = $filePath . '.tmp.' . pathinfo($filePath, PATHINFO_EXTENSION);
        $cmd = sprintf(
            '%s -y -i %s -b:a 128k -ar 44100 -ac 2 %s 2>/dev/null',
            escapeshellcmd($this->ffmpegPath),
            escapeshellarg($filePath),
            escapeshellarg($tmpPath)
        );
        exec($cmd, $output, $exitCode);

        if ($exitCode === 0 && file_exists($tmpPath)) {
            $originalSize = filesize($filePath);
            $newSize = filesize($tmpPath);
            // Only replace if actually smaller
            if ($newSize < $originalSize) {
                unlink($filePath);
                rename($tmpPath, $filePath);
            } else {
                unlink($tmpPath);
            }
        } elseif (file_exists($tmpPath)) {
            unlink($tmpPath);
        }
    }

    private function generateWaveform(string $filePath): ?string
    {
        $waveformPath = $filePath . '.waveform.json';
        // Extract 200 amplitude samples using astats filter
        $cmd = sprintf(
            '%s -y -i %s -af "astats=metadata=1:reset=1,ametadata=print:key=lavfi.astats.Overall.RMS_level:file=%s" -f null - 2>/dev/null',
            escapeshellcmd($this->ffmpegPath),
            escapeshellarg($filePath),
            escapeshellarg($waveformPath)
        );
        exec($cmd, $output, $exitCode);

        if ($exitCode === 0 && file_exists($waveformPath)) {
            $content = file_get_contents($waveformPath);
            unlink($waveformPath);

            // Parse RMS levels into waveform samples
            preg_match_all('/lavfi\.astats\.Overall\.RMS_level=([-\d.]+)/', $content, $matches);
            $levels = $matches[1] ?? [];

            if (!empty($levels)) {
                // Downsample to 200 points
                $samples = $this->downsample($levels, 200);
                // Normalize to 0-100 range
                $max = max(array_map('abs', $samples));
                $normalized = array_map(function ($v) use ($max) {
                    return $max > 0 ? round((abs($v) / $max) * 100) : 0;
                }, $samples);

                return json_encode($normalized);
            }
        }

        // Fallback: generate simple waveform from raw PCM samples
        $pcmCmd = sprintf(
            '%s -y -i %s -ac 1 -ar 8000 -f s16le - 2>/dev/null | dd bs=2 count=4000 2>/dev/null | od -An -d | tr -s " \n" ","',
            escapeshellcmd($this->ffmpegPath),
            escapeshellarg($filePath)
        );
        $raw = shell_exec($pcmCmd);
        if ($raw) {
            $values = array_filter(array_map('trim', explode(',', $raw)));
            $values = array_map(function ($v) { return (int)$v; }, $values);
            if (!empty($values)) {
                $samples = $this->downsample($values, 200);
                $max = max($samples);
                $normalized = array_map(function ($v) use ($max) {
                    return $max > 0 ? round(($v / $max) * 100) : 0;
                }, $samples);
                return json_encode($normalized);
            }
        }

        return null;
    }

    private function downsample(array $data, int $targetCount): array
    {
        $count = count($data);
        if ($count <= $targetCount) {
            return $data;
        }
        $result = [];
        $ratio = $count / $targetCount;
        for ($i = 0; $i < $targetCount; $i++) {
            $start = round($i * $ratio);
            $end = round(($i + 1) * $ratio);
            $slice = array_slice($data, $start, $end - $start);
            $result[] = !empty($slice) ? array_sum($slice) / count($slice) : 0;
        }
        return $result;
    }

    private function formatDuration(float $seconds): string
    {
        $mins = floor($seconds / 60);
        $secs = floor($seconds % 60);
        return sprintf('%d:%02d', $mins, $secs);
    }
}
