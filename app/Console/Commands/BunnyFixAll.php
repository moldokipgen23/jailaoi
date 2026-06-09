<?php

namespace App\Console\Commands;

// JAILAOI: ONE command to fix everything.
// 1. Resets polluted DB paths (various/file.mp3 → file.mp3)
// 2. Deletes flat orphan files + various/ subfolders on Bunny
// 3. Re-uploads audio to music/{artist-slug}/file.mp3
// 4. Uploads cover images to images/music/file.jpg
// 5. Updates DB audio paths to {artist-slug}/file.mp3

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BunnyFixAll extends Command
{
    protected $signature = 'bunny:fix-all {--pretend : Dry-run, show what would happen}';
    protected $description = 'Fix Bunny CDN folder structure end-to-end. Does cleanup + upload in one shot.';

    private array $sources = [
        // table → [audio_col, artist_fk, image_cols[], folder]
        'tbl_music'   => ['music',         'artist_id', ['portrait_img', 'landscape_img', 'ogtag_img'], 'music'],
        'tbl_song'    => ['song_url',      'artist_id', ['image'],                                       'radio'],
        'tbl_podcast' => ['trailer_audio', 'artist_id', ['portrait_img', 'landscape_img'],               'podcast'],
    ];

    private string $zone;
    private string $apiKey;
    private string $cdnUrl;
    private string $endpoint;

    public function handle(): int
    {
        $pretend = $this->option('pretend');

        // ── Load Bunny config ─────────────────────────────────────────────
        $cfg = DB::table('tbl_general_setting')
            ->whereIn('key', ['bunny_storage_zone', 'bunny_storage_api_key', 'bunny_cdn_url', 'bunny_storage_endpoint'])
            ->pluck('value', 'key');

        $this->zone     = $cfg['bunny_storage_zone']     ?? '';
        $this->apiKey   = $cfg['bunny_storage_api_key']  ?? '';
        $this->cdnUrl   = rtrim($cfg['bunny_cdn_url']    ?? '', '/');
        $this->endpoint = rtrim($cfg['bunny_storage_endpoint'] ?? 'https://storage.bunnycdn.com', '/');

        if (!$this->zone || !$this->apiKey || !$this->cdnUrl) {
            $this->error('Bunny CDN not configured. Set Storage Zone, API Key, and CDN URL in Admin → Settings.');
            return Command::FAILURE;
        }

        $modeLabel = $pretend ? ' [PRETEND MODE — NOTHING WILL CHANGE]' : '';
        $this->info("══════════════════════════════════════════════{$modeLabel}");
        $this->info("  Bunny CDN End-to-End Fix");
        $this->info("══════════════════════════════════════════════");

        // ── STEP 1: Reset polluted DB paths ──────────────────────────────
        $this->info("\n[1/4] Resetting DB paths starting with 'various/' ...");
        $dbReset = 0;
        foreach ($this->sources as $table => [$audioCol, , , ]) {
            $rows = DB::table($table)->where($audioCol, 'LIKE', 'various/%')->select('id', $audioCol)->get();
            foreach ($rows as $row) {
                $filename = basename($row->{$audioCol});
                if (!$pretend) {
                    DB::table($table)->where('id', $row->id)->update([$audioCol => $filename]);
                }
                $dbReset++;
            }
            $this->line("  {$table}: " . $rows->count() . " row(s) reset");
        }
        $this->line("  Total DB rows reset: {$dbReset}");

        // ── STEP 2: Clean Bunny — delete flat files + various/ subdirs ───
        $this->info("\n[2/4] Cleaning Bunny CDN (flat files + various/ subdirs) ...");
        $deleted = 0;
        foreach (['music', 'radio', 'podcast'] as $folder) {
            $entries = $this->listBunny($folder);
            if ($entries === null) { $this->warn("  Can't list {$folder}/"); continue; }

            foreach ($entries as $entry) {
                $name  = $entry['ObjectName'] ?? '';
                $isDir = (bool)($entry['IsDirectory'] ?? false);
                if (!$name) continue;

                if ($isDir && $name === 'various') {
                    $deleted += $this->deleteDirRecursive("{$folder}/various", $pretend);
                    continue;
                }
                if ($isDir) continue;  // keep all other dirs (artist-slug, 2023, 2024)

                $path = "{$folder}/{$name}";
                if ($pretend) { $this->line("    [PRETEND] DELETE {$path}"); $deleted++; continue; }
                try { $this->deleteBunny($path); $deleted++; }
                catch (\Throwable $e) { $this->error("    FAILED {$path}: {$e->getMessage()}"); }
            }
        }
        $this->line("  Total deleted from Bunny: {$deleted}");

        // ── STEP 3 & 4: Upload audio + images with correct structure ────
        $this->info("\n[3/4] Uploading audio with artist-slug subfolders ...");
        $this->info("[4/4] Uploading images to images/{type}/ ...");

        $audioUp = 0; $imgUp = 0; $dbUpd = 0; $missing = 0; $skipped = 0; $errors = 0;

        foreach ($this->sources as $table => [$audioCol, $artistFk, $imageCols, $folder]) {
            $this->line("\n  ── {$table} → {$folder}/ ──");
            $artistMap = $this->buildArtistMap($table, $artistFk);

            $records = DB::table($table)->select(array_merge(['id', $audioCol, $artistFk], $imageCols))->get();
            $this->line("    {$records->count()} records");

            foreach ($records as $rec) {
                // ── AUDIO ──
                $storedAudio = $rec->{$audioCol} ?? '';
                if (!empty($storedAudio) && !str_contains($storedAudio, '/')) {
                    $artistId   = $rec->{$artistFk} ?? 0;
                    $artistSlug = $artistMap[$artistId] ?? 'various';
                    $localPath  = storage_path("app/public/{$folder}/{$storedAudio}");
                    $remotePath = "{$folder}/{$artistSlug}/{$storedAudio}";

                    if (!file_exists($localPath)) {
                        $missing++;
                    } elseif ($this->existsOnBunny($remotePath)) {
                        $skipped++;
                        // still update DB even if file already there
                        if (!$pretend) {
                            DB::table($table)->where('id', $rec->id)->update([$audioCol => "{$artistSlug}/{$storedAudio}"]);
                            $dbUpd++;
                        }
                    } else {
                        if ($pretend) {
                            $audioUp++;
                        } else {
                            try {
                                $this->uploadBunny($localPath, $remotePath);
                                DB::table($table)->where('id', $rec->id)->update([$audioCol => "{$artistSlug}/{$storedAudio}"]);
                                $audioUp++; $dbUpd++;
                            } catch (\Throwable $e) {
                                $this->error("    FAILED audio id={$rec->id}: {$e->getMessage()}");
                                $errors++;
                            }
                        }
                    }
                } elseif (str_contains($storedAudio, '/')) {
                    $skipped++;
                }

                // ── IMAGES ──
                foreach ($imageCols as $col) {
                    $storedImg = $rec->{$col} ?? '';
                    if (empty($storedImg)) continue;

                    $localPath  = storage_path("app/public/{$folder}/{$storedImg}");
                    $remotePath = "images/{$folder}/{$storedImg}";

                    if (!file_exists($localPath)) { $missing++; continue; }
                    if ($this->existsOnBunny($remotePath)) { $skipped++; continue; }

                    if ($pretend) { $imgUp++; continue; }
                    try { $this->uploadBunny($localPath, $remotePath); $imgUp++; }
                    catch (\Throwable $e) { $this->error("    FAILED img id={$rec->id} {$col}: {$e->getMessage()}"); $errors++; }
                }
            }
        }

        // ── SUMMARY ──
        $this->newLine();
        $this->info("══════════════════════════════════════════════");
        $this->info("  DONE{$modeLabel}");
        $this->info("══════════════════════════════════════════════");
        $this->line("  DB paths reset (various/ → filename) : {$dbReset}");
        $this->line("  Bunny files/dirs deleted             : {$deleted}");
        $this->line("  Audio uploaded                       : {$audioUp}");
        $this->line("  Images uploaded                      : {$imgUp}");
        $this->line("  DB audio paths updated to artist/    : {$dbUpd}");
        $this->line("  Already on Bunny (skipped)           : {$skipped}");
        $this->line("  Local file missing                   : {$missing}");
        $this->line("  Errors                               : {$errors}");
        $this->newLine();

        if ($pretend) {
            $this->warn("This was a DRY-RUN. Re-run without --pretend to apply.");
        } elseif ($errors === 0) {
            $this->info("All done. Your Bunny CDN is now organized correctly.");
        } else {
            $this->warn("{$errors} errors. Re-run to retry.");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function buildArtistMap(string $table, string $artistFk): array
    {
        $ids = DB::table($table)->whereNotNull($artistFk)->pluck($artistFk)->unique()->filter();
        $map = [];
        foreach ($ids as $id) {
            $firstId = (int) explode(',', (string)$id)[0];
            if (!$firstId) continue;
            $artist = DB::table('tbl_artist')->where('id', $firstId)->first();
            if ($artist) {
                $name = $artist->name ?? '';
                $slug = !empty($artist->slug ?? null) ? $artist->slug : Str::slug($name, '-');
                $map[$id] = $slug ?: 'various';
            } else {
                $map[$id] = 'various';
            }
        }
        return $map;
    }

    private function listBunny(string $folder): ?array
    {
        $url = $this->endpoint . '/' . $this->zone . '/' . trim($folder, '/') . '/';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['AccessKey: ' . $this->apiKey, 'Accept: application/json'],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code !== 200) return null;
        $d = json_decode($body, true);
        return is_array($d) ? $d : [];
    }

    private function deleteDirRecursive(string $dir, bool $pretend): int
    {
        $entries = $this->listBunny($dir);
        if ($entries === null) return 0;
        $n = 0;
        foreach ($entries as $entry) {
            $name  = $entry['ObjectName'] ?? '';
            $isDir = (bool)($entry['IsDirectory'] ?? false);
            if (!$name) continue;
            if ($isDir) { $n += $this->deleteDirRecursive("{$dir}/{$name}", $pretend); continue; }
            $path = "{$dir}/{$name}";
            if ($pretend) { $this->line("    [PRETEND] DELETE {$path}"); $n++; continue; }
            try { $this->deleteBunny($path); $n++; }
            catch (\Throwable $e) { $this->error("    FAILED {$path}: {$e->getMessage()}"); }
        }
        if (!$pretend) { try { $this->deleteBunny($dir . '/'); } catch (\Throwable) {} }
        return $n;
    }

    private function existsOnBunny(string $remotePath): bool
    {
        $url = $this->cdnUrl . '/' . ltrim($remotePath, '/');
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5, CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code === 200;
    }

    private function uploadBunny(string $localPath, string $remotePath): void
    {
        $url  = $this->endpoint . '/' . $this->zone . '/' . ltrim($remotePath, '/');
        $fp   = fopen($localPath, 'rb');
        $size = filesize($localPath);
        $ch   = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_PUT => true, CURLOPT_INFILE => $fp, CURLOPT_INFILESIZE => $size,
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => ['AccessKey: ' . $this->apiKey, 'Content-Type: application/octet-stream'],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if ($code < 200 || $code >= 300) throw new \Exception("HTTP {$code}: {$body}");
    }

    private function deleteBunny(string $path): void
    {
        $url = $this->endpoint . '/' . $this->zone . '/' . ltrim($path, '/');
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'DELETE', CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['AccessKey: ' . $this->apiKey],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300) throw new \Exception("HTTP {$code}: {$body}");
    }
}
