<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MigrateAudioToR2 extends Command
{
    protected $signature = 'jailaoi:migrate-audio-to-r2
        {--folder=all : Specific folder to migrate (song, music, podcast, content). Default: all}
        {--dry-run : Only list files that would be migrated, do not upload}';
    protected $description = 'Copy existing local audio files to Cloudflare R2 and optionally update DB paths';

    protected $audioFolders = ['song', 'music', 'podcast', 'content'];

    public function handle()
    {
        $this->info('Starting audio migration to R2...');
        $this->warn('Make sure R2 credentials are configured in .env and the r2 disk works.');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $folderOpt = $this->option('folder');

        $folders = $folderOpt === 'all' ? $this->audioFolders : [$folderOpt];

        foreach ($folders as $folder) {
            $this->migrateFolder($folder, $dryRun);
        }

        $this->newLine();
        $this->info('Migration complete!');
        $this->warn('To activate R2, set audio_storage_driver to "r2" in tbl_general_setting.');
        $this->warn('You can do this via php artisan jailaoi:activate-r2 or manually in the DB.');
    }

    protected function migrateFolder(string $folder, bool $dryRun): void
    {
        $localPath = storage_path('app/public/' . $folder);

        if (!is_dir($localPath)) {
            $this->warn("Folder '{$folder}' does not exist at {$localPath}. Skipping.");
            return;
        }

        $files = glob($localPath . '/*');

        if (empty($files)) {
            $this->warn("No files found in '{$folder}'. Skipping.");
            return;
        }

        $this->info("Processing '{$folder}' folder (" . count($files) . ' files)...');
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $uploaded = 0;
        $skipped = 0;

        foreach ($files as $filePath) {
            if (is_dir($filePath)) {
                $bar->advance();
                continue;
            }

            $filename = basename($filePath);
            $r2Path = $folder . '/' . $filename;

            // Skip if already exists on R2
            if (Storage::disk('r2')->exists($r2Path)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $this->line(" [DRY-RUN] Would upload: {$folder}/{$filename}");
                $bar->advance();
                continue;
            }

            try {
                Storage::disk('r2')->put($r2Path, file_get_contents($filePath));
                $uploaded++;
            } catch (Exception $e) {
                $this->error("Failed to upload {$filename}: " . $e->getMessage());
                Log::error("R2 migration failed for {$folder}/{$filename}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$folder}: {$uploaded} uploaded, {$skipped} already on R2.");
    }
}
