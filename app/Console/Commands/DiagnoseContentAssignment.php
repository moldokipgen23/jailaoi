<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseContentAssignment extends Command
{
    protected $signature = 'diagnose:content
        {--fix : Auto-assign default category/language to unassigned songs}
        {--category= : Default category_id to assign (used with --fix)}
        {--language= : Default language_id to assign (used with --fix)}';

    protected $description = 'Diagnose and optionally fix songs/music missing category or language assignments';

    public function handle(): int
    {
        $this->info('');
        $this->info('=== CONTENT ASSIGNMENT DIAGNOSTIC ===');
        $this->info('');

        // ── Songs (tbl_song / Radio) ─────────────────────────────────────────
        $totalSongs = DB::table('tbl_song')->count();
        $noCategory = DB::table('tbl_song')
            ->where(fn($q) => $q->whereNull('category_id')->orWhere('category_id', 0))
            ->count();
        $noLanguage = DB::table('tbl_song')
            ->where(fn($q) => $q->whereNull('language_id')->orWhere('language_id', 0))
            ->count();
        $noCity = DB::table('tbl_song')
            ->where(fn($q) => $q->whereNull('city_id')->orWhere('city_id', 0))
            ->count();
        $inactive = DB::table('tbl_song')->where('status', '!=', 1)->count();

        $this->info("SONGS (tbl_song)  Total: $totalSongs  Inactive: $inactive");
        $this->table(
            ['Issue', 'Count', '% of total'],
            [
                ['Missing category_id', $noCategory, $totalSongs ? round($noCategory / $totalSongs * 100) . '%' : '-'],
                ['Missing language_id', $noLanguage, $totalSongs ? round($noLanguage / $totalSongs * 100) . '%' : '-'],
                ['Missing city_id',     $noCity,     $totalSongs ? round($noCity     / $totalSongs * 100) . '%' : '-'],
            ]
        );

        // Which category IDs are used in songs vs which exist
        $songCatIds = DB::table('tbl_song')
            ->whereNotNull('category_id')
            ->where('category_id', '!=', 0)
            ->selectRaw('category_id, count(*) as cnt')
            ->groupBy('category_id')
            ->orderByDesc('cnt')
            ->get();

        $existingCats = DB::table('tbl_category')
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $this->info('');
        $this->info('Category ID usage in songs:');
        $rows = [];
        foreach ($songCatIds as $row) {
            $name = $existingCats->get($row->category_id)?->name ?? '⚠️  NOT FOUND in tbl_category';
            $rows[] = [$row->category_id, $name, $row->cnt];
        }
        if (empty($rows)) {
            $this->warn('  → No songs have category_id set!');
        } else {
            $this->table(['category_id', 'Category name', '# songs'], $rows);
        }

        // Which language IDs are used in songs vs which exist
        $songLangIds = DB::table('tbl_song')
            ->whereNotNull('language_id')
            ->where('language_id', '!=', 0)
            ->selectRaw('language_id, count(*) as cnt')
            ->groupBy('language_id')
            ->orderByDesc('cnt')
            ->get();

        $existingLangs = DB::table('tbl_language')
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $this->info('');
        $this->info('Language ID usage in songs:');
        $rows = [];
        foreach ($songLangIds as $row) {
            $name = $existingLangs->get($row->language_id)?->name ?? '⚠️  NOT FOUND in tbl_language';
            $rows[] = [$row->language_id, $name, $row->cnt];
        }
        if (empty($rows)) {
            $this->warn('  → No songs have language_id set!');
        } else {
            $this->table(['language_id', 'Language name', '# songs'], $rows);
        }

        // ── Music (tbl_music) ────────────────────────────────────────────────
        $this->info('');
        $totalMusic = DB::table('tbl_music')->count();
        $mNoCategory = DB::table('tbl_music')
            ->where(fn($q) => $q->whereNull('category_id')->orWhere('category_id', 0))
            ->count();
        $mNoLanguage = DB::table('tbl_music')
            ->where(fn($q) => $q->whereNull('language_id')->orWhere('language_id', 0))
            ->count();
        $mInactive = DB::table('tbl_music')->where('status', '!=', 1)->count();

        $this->info("MUSIC (tbl_music)  Total: $totalMusic  Inactive: $mInactive");
        $this->table(
            ['Issue', 'Count', '% of total'],
            [
                ['Missing category_id', $mNoCategory, $totalMusic ? round($mNoCategory / $totalMusic * 100) . '%' : '-'],
                ['Missing language_id', $mNoLanguage, $totalMusic ? round($mNoLanguage / $totalMusic * 100) . '%' : '-'],
            ]
        );

        // Available categories and languages for reference
        $this->info('');
        $this->info('Available categories in tbl_category:');
        $cats = DB::table('tbl_category')->where('status', 1)->select('id', 'name')->orderBy('id')->get()->toArray();
        $this->table(['id', 'name'], array_map(fn($c) => [(array)$c]['id'] ?? [$c->id, $c->name], $cats));
        // cleaner
        $catRows = DB::table('tbl_category')->where('status', 1)->select('id', 'name')->orderBy('id')->get();
        $langRows = DB::table('tbl_language')->where('status', 1)->select('id', 'name')->orderBy('id')->get();
        $this->table(['id', 'name'], $catRows->map(fn($r) => [$r->id, $r->name])->toArray());

        $this->info('');
        $this->info('Available languages in tbl_language:');
        $this->table(['id', 'name'], $langRows->map(fn($r) => [$r->id, $r->name])->toArray());

        // ── Auto-fix mode ────────────────────────────────────────────────────
        if ($this->option('fix')) {
            $defaultCat  = (int) $this->option('category');
            $defaultLang = (int) $this->option('language');

            if (!$defaultCat || !$defaultLang) {
                $this->error('--fix requires --category=ID and --language=ID');
                $this->info('Example: php artisan diagnose:content --fix --category=1 --language=1');
                return 1;
            }

            if (!$catRows->firstWhere('id', $defaultCat)) {
                $this->error("category_id=$defaultCat not found in tbl_category");
                return 1;
            }
            if (!$langRows->firstWhere('id', $defaultLang)) {
                $this->error("language_id=$defaultLang not found in tbl_language");
                return 1;
            }

            $this->info('');
            if (!$this->confirm("Assign category_id=$defaultCat + language_id=$defaultLang to all unassigned songs & music?")) {
                $this->info('Aborted.');
                return 0;
            }

            $fixedSongCat = DB::table('tbl_song')
                ->where(fn($q) => $q->whereNull('category_id')->orWhere('category_id', 0))
                ->update(['category_id' => $defaultCat]);

            $fixedSongLang = DB::table('tbl_song')
                ->where(fn($q) => $q->whereNull('language_id')->orWhere('language_id', 0))
                ->update(['language_id' => $defaultLang]);

            $fixedMusicCat = DB::table('tbl_music')
                ->where(fn($q) => $q->whereNull('category_id')->orWhere('category_id', 0))
                ->update(['category_id' => $defaultCat]);

            $fixedMusicLang = DB::table('tbl_music')
                ->where(fn($q) => $q->whereNull('language_id')->orWhere('language_id', 0))
                ->update(['language_id' => $defaultLang]);

            $this->info('');
            $this->info('✅  Fixed:');
            $this->table(['Table', 'Rows updated (category)', 'Rows updated (language)'], [
                ['tbl_song',  $fixedSongCat,  $fixedSongLang],
                ['tbl_music', $fixedMusicCat, $fixedMusicLang],
            ]);
        }

        return 0;
    }
}
