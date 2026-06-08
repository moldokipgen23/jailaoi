<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Music;
use App\Models\Podcast;
use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToTypeFolders extends Command
{
    protected $signature = 'migrate:storage-folders
        {--pretend : Dry-run without moving files}
        {--force : Skip confirmation prompt}';

    protected $description = 'Move files from content/ to type folders (music/radio/podcast) based on DB references, rename song/ → radio/';

    private string $base;

    public function handle(): int
    {
        $pretend = $this->option('pretend');
        $force = $this->option('force');

        if (!$force && !$pretend && !$this->confirm('This will move files from content/ to music/radio/podcast and rename song/ → radio/. Continue?')) {
            return Command::FAILURE;
        }

        $this->base = storage_path('app/public');

        $moved = 0;
        $skipped = 0;
        $errors = 0;

        // ── Step 1: Rename song/ → radio/ ──
        $result = $this->renameSongToRadio($pretend);
        $moved += $result['moved'];
        $skipped += $result['skipped'];
        $errors += $result['errors'];

        // ── Step 2: Move files based on DB references ──
        $this->newLine();
        $this->info('Step 2: Moving files from content/ to type folders based on DB references...');

        // Map: [table_display, folder, [column, ...]]
        $sources = [
            ['tbl_song (radio)',  'radio',   ['song_url', 'image']],
            ['tbl_music (music)', 'music',   ['music', 'portrait_img', 'landscape_img', 'ogtag_img']],
            ['tbl_podcast',       'podcast', ['trailer_audio', 'portrait_img', 'landscape_img']],
            ['tbl_episode',       'podcast', ['episode_audio', 'portrait_img', 'landscape_img']],
        ];

        foreach ($sources as [$label, $folder, $columns]) {
            try {
                $records = $this->getRecordsWithPaths($label, $columns);
            } catch (\Throwable $e) {
                $this->warn("  {$label}: table not found, skipping ({$e->getMessage()})");
                continue;
            }
            foreach ($records as $record) {
                foreach ($columns as $col) {
                    $path = $record[$col] ?? '';
                    if (empty($path)) continue;

                    $src = "{$this->base}/content/{$path}";
                    $dst = "{$this->base}/{$folder}/{$path}";

                    if (!file_exists($src)) continue;

                    if ($pretend) {
                        $this->line("  [PRETEND] content/{$path} → {$folder}/{$path}");
                        $moved++;
                    } else {
                        $dstDir = dirname($dst);
                        if (!is_dir($dstDir)) {
                            @mkdir($dstDir, 0755, true);
                        }
                        if (file_exists($dst)) {
                            $skipped++;
                            continue;
                        }
                        if (@rename($src, $dst)) {
                            $moved++;
                        } else {
                            $this->error("  Failed: content/{$path} → {$folder}/{$path}");
                            $errors++;
                        }
                    }
                }
            }
        }

        // ── Summary ──
        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            [
                ['Moved / would move', $moved],
                ['Skipped (already exists)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($pretend) {
            $this->warn('Dry-run complete — no files were moved.');
        } else {
            $this->info('File migration complete!');
            $this->suggestCleanup();
        }

        return Command::SUCCESS;
    }

    private function renameSongToRadio(bool $pretend): array
    {
        $this->info('Step 1: Renaming song/ → radio/...');
        $songDir = "{$this->base}/song";
        $radioDir = "{$this->base}/radio";

        if (!is_dir($songDir)) {
            $this->line('  song/ does not exist — skipping rename');
            return ['moved' => 0, 'skipped' => 0, 'errors' => 0];
        }

        if ($pretend) {
            $this->warn('  [PRETEND] Would rename song/ → radio/');
            return ['moved' => 1, 'skipped' => 0, 'errors' => 0];
        }

        if (is_dir($radioDir)) {
            $this->warn('  radio/ already exists — merging song/ into radio/');
            $count = $this->mergeDirectories($songDir, $radioDir);
            $this->info("  Merged {$count} files from song/ into radio/");
            $this->removeEmptyDir($songDir);
            return ['moved' => $count, 'skipped' => 0, 'errors' => 0];
        }

        rename($songDir, $radioDir);
        $this->info('  Renamed song/ → radio/');
        return ['moved' => 1, 'skipped' => 0, 'errors' => 0];
    }

    private function getRecordsWithPaths(string $label, array $columns): array
    {
        $model = match ($label) {
            'tbl_song (radio)'  => Song::query(),
            'tbl_music (music)' => Music::query(),
            'tbl_podcast'       => Podcast::query(),
            'tbl_episode'       => Episode::query(),
            default             => throw new \InvalidArgumentException("Unknown source: {$label}"),
        };

        $select = array_merge(['id'], $columns);
        $records = $model->select($select)->get()->toArray();

        // Filter to records that have at least one non-empty path
        $records = array_filter($records, function ($r) use ($columns) {
            foreach ($columns as $col) {
                $val = $r[$col] ?? '';
                if ($val !== null && $val !== '') return true;
            }
            return false;
        });

        $count = count($records);
        $this->line("  {$label}: {$count} records with non-empty paths");

        return array_values($records);
    }

    private function suggestCleanup(): void
    {
        $contentDir = "{$this->base}/content";
        if (!is_dir($contentDir)) return;

        $remaining = $this->getAllFiles($contentDir);
        if (empty($remaining)) {
            $this->info('  content/ is empty — you may delete it: rm -rf ' . $contentDir);
        } else {
            $totalSize = 0;
            foreach ($remaining as $f) {
                $totalSize += filesize($f);
            }
            $this->warn('  ' . count($remaining) . ' files (' . round($totalSize / 1024 / 1024, 1) . ' MB) remain in content/ (not referenced by any DB table)');
        }
    }

    private function mergeDirectories(string $src, string $dst): int
    {
        $count = 0;
        $files = $this->getAllFiles($src);
        foreach ($files as $file) {
            $relative = substr($file, strlen($src) + 1);
            $target = "{$dst}/{$relative}";
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            if (!file_exists($target)) {
                @rename($file, $target);
                $count++;
            }
        }
        return $count;
    }

    private function removeEmptyDir(string $dir): void
    {
        // Remove empty subdirectories bottom-up
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            }
        }
        @rmdir($dir);
    }

    private function getAllFiles(string $dir): array
    {
        $files = [];
        if (!is_dir($dir)) return $files;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getRealPath();
            }
        }
        return $files;
    }
}
