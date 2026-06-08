<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToTypeFolders extends Command
{
    protected $signature = 'migrate:storage-folders
        {--pretend : Dry-run without moving files}
        {--force : Skip confirmation prompt}';

    protected $description = 'Move all files from content/ to music/radio/podcast folders and rename song/ → radio/';

    public function handle(): int
    {
        $pretend = $this->option('pretend');
        $force = $this->option('force');
        $public = Storage::disk('public');
        $base = storage_path('app/public');

        if (!$force && !$pretend && !$this->confirm('This will move files from content/ to music/radio/podcast and rename song/ → radio/. Continue?')) {
            return Command::FAILURE;
        }

        $moved = 0;
        $skipped = 0;
        $errors = 0;

        // ── Step 1: Rename song/ → radio/ ──
        if (is_dir("{$base}/song")) {
            if ($pretend) {
                $this->warn('[PRETEND] Would rename song/ → radio/');
            } else {
                if (is_dir("{$base}/radio")) {
                    $this->warn('radio/ already exists — merging song/ into radio/');
                    $this->mergeDirectories("{$base}/song", "{$base}/radio");
                } else {
                    rename("{$base}/song", "{$base}/radio");
                    $this->info('Renamed song/ → radio/');
                }
            }
        } else {
            $this->line('song/ does not exist — skipping rename');
        }

        // ── Step 2: Move audio from content/ → music/ ──
        // All DeepSound audio files (mp3, wav, ogg, aac, flac, m4a, wma)
        $audioExts = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'wma'];
        $contentDir = "{$base}/content";

        if (is_dir($contentDir)) {
            $files = $this->getAllFiles($contentDir);
            $audioCount = 0;
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $audioExts)) {
                    $audioCount++;
                }
            }
            $this->info("Found {$audioCount} audio files in content/");

            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, $audioExts)) continue;

                $relative = substr($file, strlen($contentDir) + 1);
                $target = "{$base}/music/{$relative}";
                $targetDir = dirname($target);

                if ($pretend) {
                    $this->line("  [PRETEND] Move: content/{$relative} → music/{$relative}");
                    $moved++;
                } else {
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }
                    if (file_exists($target)) {
                        $skipped++;
                        continue;
                    }
                    if (@rename($file, $target)) {
                        $moved++;
                    } else {
                        $this->error("  Failed to move: {$relative}");
                        $errors++;
                    }
                }
            }
        } else {
            $this->line('content/ does not exist — skipping audio move');
        }

        // ── Step 3: Move images from content/ → music/ ──
        // All DeepSound image files belong to music-type content
        $imgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        if (is_dir($contentDir)) {
            $files = $this->getAllFiles($contentDir);
            $imgMoved = 0;
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, $imgExts)) continue;

                $relative = substr($file, strlen($contentDir) + 1);
                $target = "{$base}/music/{$relative}";
                $targetDir = dirname($target);

                if ($pretend) {
                    $this->line("  [PRETEND] Move: content/{$relative} → music/{$relative}");
                    $moved++;
                } else {
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }
                    if (file_exists($target)) {
                        $skipped++;
                        continue;
                    }
                    if (@rename($file, $target)) {
                        $imgMoved++;
                        $moved++;
                    } else {
                        $this->error("  Failed to move image: {$relative}");
                        $errors++;
                    }
                }
            }
            if ($imgMoved > 0) {
                $this->info("Moved {$imgMoved} images from content/ → music/");
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

            // Check if content/ is now empty and suggest removal
            if (is_dir($contentDir)) {
                $remaining = $this->getAllFiles($contentDir);
                if (empty($remaining)) {
                    $this->info('content/ is empty — you may delete it: rm -rf ' . $contentDir);
                } else {
                    $this->warn(count($remaining) . ' files remain in content/ (non-audio/image files)');
                }
            }
        }

        return Command::SUCCESS;
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

    private function mergeDirectories(string $src, string $dst): void
    {
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
            }
        }
    }
}
