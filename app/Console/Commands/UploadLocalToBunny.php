<?php

namespace App\Console\Commands;

// JAILAOI: Uploads local audio/image files to Bunny CDN.
// Use after any local-only file migration to make files available on CDN.
// Safe to re-run — skips files already on Bunny (HEAD request check).

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Common;

class UploadLocalToBunny extends Command
{
    protected $signature = 'bunny:upload-local
        {--type=all : Which table to process: all, music, radio, podcast}
        {--pretend  : Dry-run — show what would be uploaded without doing it}
        {--skip-check : Skip Bunny HEAD check (faster, re-uploads even if already there)}';

    protected $description = 'Upload local audio/image files to Bunny CDN. Fixes "track unavailable" for mirrored content.';

    private Common $common;

    // folder → [table, columns_with_files]
    private array $sources = [
        'music'   => ['tbl_music',   ['music', 'portrait_img', 'landscape_img', 'ogtag_img']],
        'radio'   => ['tbl_song',    ['song_url', 'image']],
        'podcast' => ['tbl_podcast', ['trailer_audio', 'portrait_img', 'landscape_img']],
    ];

    public function handle(): int
    {
        $this->common = new Common;
        $pretend      = $this->option('pretend');
        $skipCheck    = $this->option('skip-check');
        $type         = $this->option('type');

        // Verify Bunny is configured
        $cfg = DB::table('tbl_general_setting')
            ->whereIn('key', ['bunny_storage_zone', 'bunny_storage_api_key', 'bunny_cdn_url', 'bunny_storage_endpoint'])
            ->pluck('value', 'key');

        $zone     = $cfg['bunny_storage_zone']     ?? '';
        $apiKey   = $cfg['bunny_storage_api_key']  ?? '';
        $cdnUrl   = rtrim($cfg['bunny_cdn_url']    ?? '', '/');
        $endpoint = rtrim($cfg['bunny_storage_endpoint'] ?? 'https://storage.bunnycdn.com', '/');

        if (!$zone || !$apiKey || !$cdnUrl) {
            $this->error('Bunny CDN not configured. Set Storage Zone, API Key, and CDN URL in Admin → Settings.');
            return Command::FAILURE;
        }

        $sources = $type === 'all' ? $this->sources : array_intersect_key($this->sources, [$type => true]);

        if (empty($sources)) {
            $this->error("Unknown type '{$type}'. Use: all, music, radio, podcast");
            return Command::FAILURE;
        }

        $uploaded = 0;
        $skipped  = 0;
        $missing  = 0;
        $errors   = 0;

        foreach ($sources as $folder => [$table, $columns]) {
            $this->info("\n── {$table} → Bunny:{$folder}/ ──");

            $records = DB::table($table)->select(array_merge(['id'], $columns))->get();
            $bar = $this->output->createProgressBar($records->count());
            $bar->start();

            foreach ($records as $record) {
                foreach ($columns as $col) {
                    $storedPath = $record->{$col} ?? '';
                    if (empty($storedPath)) { $bar->advance(); continue; }

                    $localPath = storage_path("app/public/{$folder}/{$storedPath}");
                    $remotePath = "{$folder}/{$storedPath}";

                    // Skip if file doesn't exist locally
                    if (!file_exists($localPath)) {
                        $missing++;
                        $bar->advance();
                        continue;
                    }

                    // Check if already on Bunny (unless --skip-check)
                    if (!$skipCheck && $this->existsOnBunny($cdnUrl, $remotePath)) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    if ($pretend) {
                        $this->newLine();
                        $this->line("  [PRETEND] {$folder}/{$storedPath}");
                        $uploaded++;
                        $bar->advance();
                        continue;
                    }

                    // Upload to Bunny
                    try {
                        $this->uploadToBunny($localPath, $remotePath, $zone, $apiKey, $endpoint);
                        $uploaded++;
                    } catch (\Throwable $e) {
                        $this->newLine();
                        $this->error("  FAILED: {$remotePath} — {$e->getMessage()}");
                        $errors++;
                    }

                    $bar->advance();
                }
            }

            $bar->finish();
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            [
                ['Uploaded to Bunny',          $uploaded],
                ['Skipped (already on Bunny)', $skipped],
                ['Missing locally (not found)', $missing],
                ['Errors',                      $errors],
            ]
        );

        if ($pretend) {
            $this->warn('Dry-run complete — nothing was uploaded.');
        } elseif ($errors === 0) {
            $this->info('All done! Audio should now be playable via Bunny CDN.');
        } else {
            $this->warn("{$errors} file(s) failed. Re-run the command to retry.");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function existsOnBunny(string $cdnUrl, string $remotePath): bool
    {
        try {
            $url = $cdnUrl . '/' . ltrim($remotePath, '/');
            $ch  = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY         => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $code === 200;
        } catch (\Throwable) {
            return false;
        }
    }

    private function uploadToBunny(string $localPath, string $remotePath, string $zone, string $apiKey, string $endpoint): void
    {
        $url = $endpoint . '/' . $zone . '/' . ltrim($remotePath, '/');

        $fp   = fopen($localPath, 'rb');
        $size = filesize($localPath);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_PUT            => true,
            CURLOPT_INFILE         => $fp,
            CURLOPT_INFILESIZE     => $size,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 300,
            CURLOPT_HTTPHEADER     => [
                'AccessKey: ' . $apiKey,
                'Content-Type: application/octet-stream',
            ],
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if ($code < 200 || $code >= 300) {
            throw new \Exception("HTTP {$code}: {$body}");
        }
    }
}
