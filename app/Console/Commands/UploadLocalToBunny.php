<?php

namespace App\Console\Commands;

// JAILAOI: Uploads local audio/image files to Bunny CDN with proper folder structure.
//
// Audio → music/{artist-slug}/filename.mp3
//          radio/{artist-slug}/filename.mp3
//          podcast/{artist-slug}/filename.mp3
// Images → images/music/filename.jpg
//           images/radio/filename.jpg
//           images/podcast/filename.jpg
//
// DB audio columns are updated to store "{artist-slug}/filename.mp3" after upload.
// DB image columns are NOT changed — Get_Image('images/music', filename) builds the URL.
//
// Safe to re-run — checks Bunny HEAD before uploading (unless --skip-check is set).

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Common;

class UploadLocalToBunny extends Command
{
    protected $signature = 'bunny:upload-local
        {--type=all   : Which table to process: all, music, radio, podcast}
        {--pretend    : Dry-run — show what would be uploaded without doing it}
        {--skip-check : Skip Bunny HEAD check (faster, re-uploads even if already there)}';

    protected $description = 'Upload local audio + image files to Bunny CDN with correct folder structure.';

    private Common $common;

    // table → [audio_col, artist_fk, image_cols[]]
    private array $sources = [
        'music'   => ['tbl_music',   'music',         'artist_id', ['portrait_img', 'landscape_img', 'ogtag_img']],
        'radio'   => ['tbl_song',    'song_url',      'artist_id', ['image']],
        'podcast' => ['tbl_podcast', 'trailer_audio', 'artist_id', ['portrait_img', 'landscape_img']],
    ];

    public function handle(): int
    {
        $this->common = new Common;
        $pretend   = $this->option('pretend');
        $skipCheck = $this->option('skip-check');
        $type      = $this->option('type');

        // Load Bunny config
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

        $audioUploaded = 0;
        $imgUploaded   = 0;
        $skipped       = 0;
        $missing       = 0;
        $dbUpdated     = 0;
        $errors        = 0;

        foreach ($sources as $folder => [$table, $audioCol, $artistFk, $imageCols]) {
            $this->info("\n── {$table} ──");

            // Build artist slug lookup: artist_id → slug
            $artistMap = $this->buildArtistMap($table, $artistFk);

            $records = DB::table($table)->select(
                array_merge(['id', $audioCol, $artistFk], $imageCols)
            )->get();

            $this->line("  {$records->count()} records found.");

            // ── AUDIO ──────────────────────────────────────────────────────────
            $this->info("  [Audio → {$folder}/{artist-slug}/file]");
            foreach ($records as $record) {
                $storedAudio = $record->{$audioCol} ?? '';
                if (empty($storedAudio)) continue;

                // Skip if already has a slash (already in artist subfolder)
                if (str_contains($storedAudio, '/')) {
                    $this->line("  SKIP audio (already has path): {$storedAudio}");
                    $skipped++;
                    continue;
                }

                $artistId   = $record->{$artistFk} ?? 0;
                $artistSlug = $artistMap[$artistId] ?? 'various';
                $localPath  = storage_path("app/public/{$folder}/{$storedAudio}");
                $remotePath = "{$folder}/{$artistSlug}/{$storedAudio}";

                if (!file_exists($localPath)) {
                    $this->line("  NOT FOUND (audio): {$localPath}");
                    $missing++;
                    continue;
                }

                if (!$skipCheck && $this->existsOnBunny($cdnUrl, $remotePath)) {
                    $this->line("  SKIP audio (already on Bunny): {$remotePath}");
                    $skipped++;
                    // Still update DB if needed
                    if ($storedAudio !== "{$artistSlug}/{$storedAudio}") {
                        if (!$pretend) {
                            DB::table($table)->where('id', $record->id)->update([$audioCol => "{$artistSlug}/{$storedAudio}"]);
                            $dbUpdated++;
                        }
                    }
                    continue;
                }

                if ($pretend) {
                    $this->line("  [PRETEND] upload {$localPath} → Bunny:{$remotePath}");
                    $this->line("            DB update id={$record->id} {$audioCol} = {$artistSlug}/{$storedAudio}");
                    $audioUploaded++;
                    continue;
                }

                try {
                    $this->uploadToBunny($localPath, $remotePath, $zone, $apiKey, $endpoint);
                    DB::table($table)->where('id', $record->id)->update([$audioCol => "{$artistSlug}/{$storedAudio}"]);
                    $audioUploaded++;
                    $dbUpdated++;
                    $this->line("  OK audio: {$remotePath}");
                } catch (\Throwable $e) {
                    $this->error("  FAILED audio id={$record->id}: {$e->getMessage()}");
                    $errors++;
                }
            }

            // ── IMAGES ─────────────────────────────────────────────────────────
            $this->info("  [Images → images/{$folder}/file]");
            foreach ($records as $record) {
                foreach ($imageCols as $col) {
                    $storedImg = $record->{$col} ?? '';
                    if (empty($storedImg)) continue;

                    $localPath  = storage_path("app/public/{$folder}/{$storedImg}");
                    $remotePath = "images/{$folder}/{$storedImg}";

                    if (!file_exists($localPath)) {
                        $this->line("  NOT FOUND (img): {$localPath}");
                        $missing++;
                        continue;
                    }

                    if (!$skipCheck && $this->existsOnBunny($cdnUrl, $remotePath)) {
                        $this->line("  SKIP img (already on Bunny): {$remotePath}");
                        $skipped++;
                        continue;
                    }

                    if ($pretend) {
                        $this->line("  [PRETEND] upload {$localPath} → Bunny:{$remotePath}");
                        $imgUploaded++;
                        continue;
                    }

                    try {
                        $this->uploadToBunny($localPath, $remotePath, $zone, $apiKey, $endpoint);
                        $imgUploaded++;
                        $this->line("  OK img: {$remotePath}");
                    } catch (\Throwable $e) {
                        $this->error("  FAILED img id={$record->id} {$col}: {$e->getMessage()}");
                        $errors++;
                    }
                }
            }
        }

        // Summary
        $this->newLine();
        $this->line('=====================================');
        $this->line("  Audio uploaded  : {$audioUploaded}");
        $this->line("  Images uploaded : {$imgUploaded}");
        $this->line("  DB rows updated : {$dbUpdated}");
        $this->line("  Skipped         : {$skipped}");
        $this->line("  Not found local : {$missing}");
        $this->line("  Errors          : {$errors}");
        $this->line('=====================================');

        if ($pretend) {
            $this->warn('Dry-run complete — nothing uploaded or changed.');
        } elseif ($errors === 0) {
            $this->info('Done! Files uploaded to Bunny CDN with correct folder structure.');
        } else {
            $this->warn("{$errors} errors. Re-run to retry failed files.");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Build artist_id → slug map for all artists referenced in a table.
     */
    private function buildArtistMap(string $table, string $artistFk): array
    {
        $ids = DB::table($table)->whereNotNull($artistFk)->pluck($artistFk)->unique()->filter();

        $map = [];
        foreach ($ids as $id) {
            // tbl_music uses comma-separated artist_ids — take the first
            $firstId = (int) explode(',', (string)$id)[0];
            if (!$firstId) continue;

            $artist = DB::table('tbl_artist')->where('id', $firstId)->first();
            if ($artist) {
                // JAILAOI: tbl_artist column is `name` (not artist_name). Slug column may not exist.
                $name = $artist->name ?? '';
                $slug = !empty($artist->slug ?? null) ? $artist->slug : Str::slug($name, '-');
                $map[$id] = $slug ?: 'various';
            } else {
                $map[$id] = 'various';
            }
        }
        return $map;
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
        $url  = $endpoint . '/' . $zone . '/' . ltrim($remotePath, '/');
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
