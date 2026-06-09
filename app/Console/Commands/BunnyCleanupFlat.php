<?php

namespace App\Console\Commands;

// JAILAOI: Cleans up orphaned flat files on Bunny CDN that were uploaded before
// the proper folder structure was implemented.
//
// Bad (flat, orphaned):
//   music/track.mp3
//   music/cover.jpg
//   radio/stream.mp3
//   radio/banner.jpg
//   podcast/episode.mp3
//
// Good (kept):
//   music/{artist-slug}/track.mp3
//   images/music/cover.jpg
//   music/2023/...  music/2024/...
//
// Use --pretend first to see what would be deleted. Then run for real.

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BunnyCleanupFlat extends Command
{
    protected $signature = 'bunny:cleanup-flat
        {--pretend       : Dry-run — list files that would be deleted}
        {--keep=2023,2024 : Comma-separated subfolder names to KEEP (besides artist-slug subdirs)}';

    protected $description = 'Delete orphaned flat files at the root of music/, radio/, podcast/ on Bunny CDN.';

    private array $rootFolders = ['music', 'radio', 'podcast'];

    public function handle(): int
    {
        $pretend = $this->option('pretend');
        $keepList = array_map('trim', explode(',', $this->option('keep')));

        // Bunny config
        $cfg = DB::table('tbl_general_setting')
            ->whereIn('key', ['bunny_storage_zone', 'bunny_storage_api_key', 'bunny_storage_endpoint'])
            ->pluck('value', 'key');

        $zone     = $cfg['bunny_storage_zone']     ?? '';
        $apiKey   = $cfg['bunny_storage_api_key']  ?? '';
        $endpoint = rtrim($cfg['bunny_storage_endpoint'] ?? 'https://storage.bunnycdn.com', '/');

        if (!$zone || !$apiKey) {
            $this->error('Bunny CDN not configured. Set Storage Zone and API Key in Admin → Settings.');
            return Command::FAILURE;
        }

        $totalDeleted = 0;
        $totalKept    = 0;
        $errors       = 0;

        foreach ($this->rootFolders as $folder) {
            $this->info("\n── Scanning {$folder}/ ──");

            $entries = $this->listBunnyFolder($endpoint, $zone, $apiKey, $folder);
            if ($entries === null) {
                $this->warn("  Could not list {$folder}/ — skipping.");
                continue;
            }

            $this->line("  Found " . count($entries) . " entries.");

            foreach ($entries as $entry) {
                $name      = $entry['ObjectName'] ?? '';
                $isDir     = (bool)($entry['IsDirectory'] ?? false);
                if (!$name) continue;

                if ($isDir) {
                    // Keep all directories — they're either artist-slug or year (2023/2024)
                    $this->line("  KEEP dir : {$folder}/{$name}/");
                    $totalKept++;
                    continue;
                }

                // It's a flat file at the root → orphan
                $path = "{$folder}/{$name}";

                if ($pretend) {
                    $this->line("  [PRETEND] DELETE {$path}");
                    $totalDeleted++;
                    continue;
                }

                try {
                    $this->deleteFromBunny($endpoint, $zone, $apiKey, $path);
                    $this->line("  DELETED  {$path}");
                    $totalDeleted++;
                } catch (\Throwable $e) {
                    $this->error("  FAILED   {$path} — {$e->getMessage()}");
                    $errors++;
                }
            }
        }

        $this->newLine();
        $this->line('=====================================');
        $this->line("  Flat files deleted : {$totalDeleted}");
        $this->line("  Directories kept   : {$totalKept}");
        $this->line("  Errors             : {$errors}");
        $this->line('=====================================');

        if ($pretend) {
            $this->warn('Dry-run complete — nothing deleted. Run without --pretend to actually delete.');
        } elseif ($errors === 0) {
            $this->info('Cleanup complete!');
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function listBunnyFolder(string $endpoint, string $zone, string $apiKey, string $folder): ?array
    {
        $url = $endpoint . '/' . $zone . '/' . trim($folder, '/') . '/';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'AccessKey: ' . $apiKey,
                'Accept: application/json',
            ],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) {
            $this->warn("  HTTP {$code} listing {$folder}/");
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function deleteFromBunny(string $endpoint, string $zone, string $apiKey, string $path): void
    {
        $url = $endpoint . '/' . $zone . '/' . ltrim($path, '/');
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => ['AccessKey: ' . $apiKey],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            throw new \Exception("HTTP {$code}: {$body}");
        }
    }
}
