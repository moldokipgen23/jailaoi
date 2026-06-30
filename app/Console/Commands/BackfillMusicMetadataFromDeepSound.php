<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillMusicMetadataFromDeepSound extends Command
{
    protected $signature = 'backfill:music-metadata
        {--old-db=jailaoi_old_ref : Scratch DB holding the imported old DeepSound dump}
        {--apply : Actually write changes. Without this flag, runs as a dry-run report only.}';

    protected $description = 'Backfill tbl_music.category_id (fixing the old migration off-by-one bug), release_year, release_date, and tags from the old DeepSound dump';

    public function handle(): int
    {
        $oldDb = $this->option('old-db');
        $apply = $this->option('apply');

        // 1. Build old_category_id -> new_category_id map by NAME (robust against any ID-arithmetic assumptions)
        $oldCats = DB::connection('mysql')->select("SELECT id, cateogry_name AS name FROM `{$oldDb}`.categories");
        $newCats = DB::table('tbl_category')->select('id', 'name')->get();

        $newCatByName = [];
        foreach ($newCats as $c) {
            $newCatByName[$this->norm($c->name)] = $c->id;
        }

        $categoryMap = [];
        $unmappedCats = [];
        foreach ($oldCats as $c) {
            $key = $this->norm($c->name);
            if (isset($newCatByName[$key])) {
                $categoryMap[$c->id] = $newCatByName[$key];
            } else {
                $unmappedCats[] = $c->name;
            }
        }

        $this->info('Category map (old id => new id):');
        $rows = [];
        foreach ($oldCats as $c) {
            $rows[] = [$c->id, $c->name, $categoryMap[$c->id] ?? '⚠️ UNMAPPED'];
        }
        $this->table(['old_id', 'name', 'new_id'], $rows);
        if ($unmappedCats) {
            $this->warn('Unmapped old categories (no name match in new tbl_category): ' . implode(', ', $unmappedCats));
        }

        // 2. Load old songs keyed by normalized title for matching
        $oldSongs = DB::connection('mysql')->select("SELECT id, title, duration, registered, tags, category_id FROM `{$oldDb}`.songs");

        $byTitle = [];
        foreach ($oldSongs as $s) {
            $byTitle[$this->norm($s->title)][] = $s;
        }

        // 3. Walk every tbl_music row, find its old match
        $music = DB::table('tbl_music')->select('id', 'title', 'duration', 'category_id')->orderBy('id')->get();

        $matched = 0;
        $unmatched = 0;
        $ambiguous = 0;
        $categoryChanges = 0;
        $sample = [];
        $unmatchedTitles = [];
        $updates = [];

        foreach ($music as $m) {
            $candidates = $byTitle[$this->norm($m->title)] ?? [];

            if (empty($candidates)) {
                $unmatched++;
                $unmatchedTitles[] = $m->title;
                continue;
            }

            $chosen = $candidates[0];
            if (count($candidates) > 1) {
                $ambiguous++;
                // Disambiguate by duration: new tbl_music.duration is in milliseconds,
                // old songs.duration is "m:ss" or "h:mm:ss"
                $best = null;
                $bestDiff = null;
                foreach ($candidates as $cand) {
                    $oldSeconds = $this->parseDuration($cand->duration);
                    $newSeconds = (int) round($m->duration / 1000);
                    $diff = abs($oldSeconds - $newSeconds);
                    if ($bestDiff === null || $diff < $bestDiff) {
                        $bestDiff = $diff;
                        $best = $cand;
                    }
                }
                if ($best !== null) {
                    $chosen = $best;
                }
            }

            $matched++;

            $oldCategoryId = (int) $chosen->category_id;
            $newCategoryId = $categoryMap[$oldCategoryId] ?? null;

            $releaseYear = null;
            $releaseDate = null;
            if (!empty($chosen->registered) && preg_match('/^(\d{4})\/(\d{1,2})$/', trim($chosen->registered), $mm)) {
                $releaseYear = (int) $mm[1];
                $releaseDate = sprintf('%04d-%02d-01', $mm[1], $mm[2]);
            }

            $tags = trim((string) ($chosen->tags ?? ''));
            $tags = ($tags === '' || $tags === '.') ? null : $tags;

            if ($newCategoryId !== null && $newCategoryId != $m->category_id) {
                $categoryChanges++;
            }

            if (count($sample) < 15) {
                $sample[] = [
                    $m->id,
                    $m->title,
                    $m->category_id,
                    $newCategoryId ?? '(no change)',
                    $releaseYear ?? '-',
                    $tags ?? '-',
                ];
            }

            $updates[] = [
                'id' => $m->id,
                'category_id' => $newCategoryId,
                'release_year' => $releaseYear,
                'release_date' => $releaseDate,
                'tags' => $tags,
            ];
        }

        $this->newLine();
        $this->info('=== MATCH SUMMARY ===');
        $this->table(['Metric', 'Count'], [
            ['Total tbl_music rows', $music->count()],
            ['Matched to old song', $matched],
            ['  of which ambiguous (resolved by duration)', $ambiguous],
            ['Unmatched (no title match)', $unmatched],
            ['Category will change', $categoryChanges],
        ]);

        if ($unmatchedTitles) {
            $this->newLine();
            $this->warn('Unmatched titles (left untouched):');
            foreach (array_slice($unmatchedTitles, 0, 20) as $t) {
                $this->line("  - $t");
            }
            if (count($unmatchedTitles) > 20) {
                $this->line('  ... and ' . (count($unmatchedTitles) - 20) . ' more');
            }
        }

        $this->newLine();
        $this->info('Sample of changes (first 15):');
        $this->table(['music.id', 'title', 'old_category_id', 'new_category_id', 'release_year', 'tags'], $sample);

        if (!$apply) {
            $this->newLine();
            $this->warn('DRY RUN — no changes written. Re-run with --apply to write these changes.');
            return 0;
        }

        $this->newLine();
        if (!$this->confirm('Apply ' . count($updates) . ' updates to tbl_music now?')) {
            $this->info('Aborted.');
            return 0;
        }

        DB::transaction(function () use ($updates) {
            foreach ($updates as $u) {
                $data = [
                    'release_year' => $u['release_year'],
                    'release_date' => $u['release_date'],
                    'tags' => $u['tags'],
                ];
                if ($u['category_id'] !== null) {
                    $data['category_id'] = $u['category_id'];
                }
                DB::table('tbl_music')->where('id', $u['id'])->update($data);
            }
        });

        $this->info('✅ Applied ' . count($updates) . ' updates to tbl_music.');

        return 0;
    }

    protected function norm(?string $s): string
    {
        return trim(mb_strtolower($s ?? ''));
    }

    protected function parseDuration(?string $duration): int
    {
        if (!$duration || $duration === '' || $duration === '0:0') {
            return 0;
        }
        $parts = explode(':', $duration);
        if (count($parts) === 2) {
            return (int) $parts[0] * 60 + (int) $parts[1];
        }
        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        }
        return (int) $duration;
    }
}
