<?php

namespace App\Console\Commands;

// JAILAOI: Cleans up orphaned files on Bunny CDN AND resets polluted DB paths.
//
// What gets deleted from Bunny:
//   - Flat files at root of music/, radio/, podcast/  (e.g. music/track.mp3)
//   - The entire various/ subfolder under music/, radio/, podcast/
//     (was created by a buggy earlier upload run that couldn't resolve artist names)
//
// What gets reset in DB:
//   - tbl_music.music, tbl_song.song_url, tbl_podcast.trailer_audio paths
//     starting with "various/" → stripped back to just the filename
//     so the next bunny:upload-local run uploads them under the correct artist slug.
//
// Use --pretend first to preview.

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BunnyCleanupFlat extends Command
{
    protected $signature = 'bunny:cleanup-flat
        {--pretend          : Dry-run — list files / DB rows that would change}
        {--skip-bunny       : Only reset DB paths, leave Bunny untouched}
        {--skip-db          : Only clean Bunny, leave DB untouched}';

    protected $description = 'Delete orphaned flat files + various/ subfolders on Bunny CDN, and reset polluted DB paths.';

    private array $rootFolders = ['music', 'radio', 'podcast'];

    // table → audio column to reset
    private array $audioCols = [
        'tbl_music'   => 'music',
        'tbl_song'    => 'song_url',
        'tbl_podcast' => 'trailer_audio',
    ];

    public function handle(): int
    {
        $pretend  = $this->option('pretend');
        $skipBunny = $this->option('skip-bunny');
        $skipDb    = $this->option('skip-db');

        $errors = 0;

        // ── PART 1: Reset DB paths ──────────────────────────────────────────
        if (!$skipDb) {
            $this->info("\n══ Resetting DB paths that contain 'various/' ══");
            foreach ($this->audioCols as $table => $col) {
                $rows = DB::table($table)
                    ->where($col, 'LIKE', 'various/%')
                    ->select('id', $col)
                    ->get();

                $this->line("  {$table}.{$col}: " . $rows->count() . " row(s) to reset.");

                foreach ($rows as $row) {
                    $filename = basename($row->{$col});  // strip "various/" prefix
                    if ($pretend) {
                        $this->line("    [PRETEND] id={$row->id}: {$row->{$col}} → {$filename}");
                        continue;
                    }
                    try {
                        DB::table($table)->where('id', $row->id)->update([$col => $filename]);
                        $this->line("    OK id={$row->id}: {$row->{$col}} → {$filename}");
                    } catch (\Throwable $e) {
                        $this->error("    FAILED id={$row->id}: {$e->getMessage()}");
                        $errors++;
                    }
                }
            }
        }

        // ── PART 2: Clean Bunny ─────────────────────────────────────────────
        if (!$skipBunny) {
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

            foreach ($this->rootFolders as $folder) {
                $this->info("\n══ Scanning Bunny: {$folder}/ ══");

                $entries = $this->listBunnyFolder($endpoint, $zone, $apiKey, $folder);
                if ($entries === null) {
                    $this->warn("  Could not list {$folder}/ — skipping.");
                    continue;
                }

                $this->line("  Found " . count($entries) . " entries.");

                foreach ($entries as $entry) {
                    $name  = $entry['ObjectName'] ?? '';
                    $isDir = (bool)($entry['IsDirectory'] ?? false);
                    if (!$name) continue;

                    if ($isDir) {
                        // Delete the buggy "various" subdir entirely
                        if ($name === 'various') {
                            $this->warn("  DELETING dir: {$folder}/various/ (was wrongly created by buggy upload)");
                            $deleted = $this->deleteBunnyDirRecursive($endpoint, $zone, $apiKey, "{$folder}/various", $pretend);
                            $totalDeleted += $deleted;
                            continue;
                        }
                        // Keep all other directories (artist-slug, 2023, 2024, etc)
                        $this->line("  KEEP dir : {$folder}/{$name}/");
                        $totalKept++;
                        continue;
                    }

                    // Flat file at root → orphan
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
            $this->line("  Bunny files deleted : {$totalDeleted}");
            $this->line("  Bunny dirs kept     : {$totalKept}");
        }

        $this->newLine();
        $this->line('=====================================');
        $this->line("  Errors : {$errors}");
        $this->line('=====================================');

        if ($pretend) {
            $this->warn('Dry-run complete — NOTHING changed. Run without --pretend to apply.');
        } elseif ($errors === 0) {
            $this->info('Cleanup complete! Now run: php artisan bunny:upload-local');
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Recursively delete every file inside a Bunny dir, then the dir itself.
     * Returns the number of files deleted (or that would be deleted in pretend mode).
     */
    private function deleteBunnyDirRecursive(string $endpoint, string $zone, string $apiKey, string $dir, bool $pretend): int
    {
        $entries = $this->listBunnyFolder($endpoint, $zone, $apiKey, $dir);
        if ($entries === null) return 0;

        $count = 0;
        foreach ($entries as $entry) {
            $name  = $entry['ObjectName'] ?? '';
            $isDir = (bool)($entry['IsDirectory'] ?? false);
            if (!$name) continue;

            if ($isDir) {
                $count += $this->deleteBunnyDirRecursive($endpoint, $zone, $apiKey, "{$dir}/{$name}", $pretend);
                continue;
            }

            $path = "{$dir}/{$name}";
            if ($pretend) {
                $this->line("    [PRETEND] DELETE {$path}");
                $count++;
                continue;
            }
            try {
                $this->deleteFromBunny($endpoint, $zone, $apiKey, $path);
                $this->line("    DELETED  {$path}");
                $count++;
            } catch (\Throwable $e) {
                $this->error("    FAILED   {$path} — {$e->getMessage()}");
            }
        }

        // Delete the directory itself (Bunny auto-removes empty dirs, but try anyway)
        if (!$pretend) {
            try { $this->deleteFromBunny($endpoint, $zone, $apiKey, $dir . '/'); } catch (\Throwable) {}
        }

        return $count;
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
