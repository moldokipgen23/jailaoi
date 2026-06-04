<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MigrateContentToDtradio extends Command
{
    protected $signature = 'migrate:content-to-dtradio';
    protected $description = 'Migrate tbl_content data to dtradio-native tables (tbl_music, tbl_song, tbl_podcast)';

    public function handle()
    {
        $this->info('Starting migration from tbl_content to dtradio tables...');
        $this->newLine();

        if (!Schema::hasTable('tbl_content')) {
            $this->warn('No tbl_content found. Nothing to migrate.');
            return 0;
        }

        $this->createDtradioTables();

        $this->migrateMusic();
        $this->newLine();

        $this->info('Migration complete. tbl_content data preserved as backup.');
        return 0;
    }

    private function createDtradioTables()
    {
        $this->info('Creating dtradio tables if missing...');

        if (!Schema::hasTable('tbl_music')) {
            Schema::create('tbl_music', function ($table) {
                $table->integer('id', true)->unsigned();
                $table->string('title');
                $table->string('artist_id')->default('');
                $table->string('album_name')->default('');
                $table->integer('category_id');
                $table->integer('language_id');
                $table->integer('is_premium')->default(0);
                $table->integer('duration')->default(0);
                $table->integer('upload_type')->comment('1=Server, 2=URL, 3=Youtube');
                $table->string('music');
                $table->text('description');
                $table->string('portrait_img');
                $table->string('landscape_img');
                $table->string('ogtag_img');
                $table->integer('total_play')->default(0);
                $table->integer('status')->default(1);
                $table->timestamps();
            });
            $this->info('Created tbl_music');
        }

        if (!Schema::hasTable('tbl_song')) {
            Schema::create('tbl_song', function ($table) {
                $table->integer('id', true)->unsigned();
                $table->integer('category_id');
                $table->integer('language_id');
                $table->integer('city_id');
                $table->string('name');
                $table->string('image');
                $table->integer('upload_type')->comment('1=Server, 2=URL');
                $table->text('song_url');
                $table->integer('duration')->default(0);
                $table->integer('is_premium')->default(0);
                $table->integer('total_play')->default(0);
                $table->integer('status')->default(1);
                $table->integer('artist_id');
                $table->timestamps();
            });
            $this->info('Created tbl_song');
        }

        if (!Schema::hasTable('tbl_podcast')) {
            Schema::create('tbl_podcast', function ($table) {
                $table->integer('id', true)->unsigned();
                $table->string('title');
                $table->integer('artist_id');
                $table->integer('category_id');
                $table->integer('language_id');
                $table->string('portrait_img');
                $table->string('landscape_img');
                $table->text('description');
                $table->integer('trailer_upload_type')->comment('1=Server, 2=URL');
                $table->string('trailer_audio');
                $table->integer('duration')->default(0);
                $table->integer('is_premium')->default(0);
                $table->integer('total_play')->default(0);
                $table->integer('status')->default(1);
                $table->timestamps();
            });
            $this->info('Created tbl_podcast');
        }
    }

    private function migrateMusic()
    {
        $this->info('Migrating music (content_type=2)...');

        DB::table('tbl_music')->truncate();

        $records = DB::table('tbl_content')
            ->where('content_type', 2)
            ->where('status', 1)
            ->get();

        if ($records->isEmpty()) {
            $this->warn('No music records found in tbl_content');
            return;
        }

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        $inserted = 0;
        $skipped = 0;

        foreach ($records as $row) {
            $artistId = $this->resolveArtistId($row->channel_id);

            DB::table('tbl_music')->insert([
                'title' => $row->title ?? '',
                'artist_id' => $artistId ? (string) $artistId : '',
                'album_name' => '',
                'category_id' => $row->category_id ?? 0,
                'language_id' => $row->language_id ?? 0,
                'is_premium' => $row->is_rent ?? 0,
                'duration' => $row->content_duration ?? 0,
                'upload_type' => $this->mapUploadType($row->content_upload_type),
                'music' => $this->migrateFile($row->content, 'content', 'music'),
                'description' => $row->description ?? '',
                'portrait_img' => $this->migrateFile($row->portrait_img, 'content', 'music'),
                'landscape_img' => $this->migrateFile($row->landscape_img, 'content', 'music'),
                'ogtag_img' => '',
                'total_play' => $row->total_view ?? 0,
                'status' => $row->status ?? 1,
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);

            $inserted++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Inserted {$inserted} records into tbl_music");
        if ($skipped) {
            $this->warn("Skipped {$skipped} records (no audio file)");
        }
    }

    private function resolveArtistId($channelId)
    {
        if (!$channelId) return null;

        $user = DB::table('tbl_user')->where('channel_id', $channelId)->first();
        if (!$user) return null;

        $artist = DB::table('tbl_artist')->where('user_id', $user->id)->first();
        return $artist ? $artist->id : null;
    }

    private function mapUploadType($type)
    {
        if (!$type || $type === '') return 1;
        $type = strtolower($type);
        if (str_contains($type, 'url') || str_contains($type, 'external')) return 2;
        if (str_contains($type, 'youtube') || str_contains($type, 'youtu')) return 3;
        return 1;
    }

    private function migrateFile($filename, $sourceFolder, $destFolder)
    {
        if (!$filename) return '';

        $filename = basename($filename);
        $sourcePath = $sourceFolder . '/' . $filename;
        $destPath = $destFolder . '/' . $filename;

        if (Storage::disk('public')->exists($sourcePath)) {
            if (!Storage::disk('public')->exists($destPath)) {
                Storage::disk('public')->copy($sourcePath, $destPath);
            }
            return $filename;
        }

        $files = Storage::disk('public')->allFiles($sourceFolder);
        foreach ($files as $file) {
            if (basename($file) === $filename) {
                if (!Storage::disk('public')->exists($destPath)) {
                    Storage::disk('public')->copy($file, $destPath);
                }
                return $filename;
            }
        }

        return '';
    }
}
