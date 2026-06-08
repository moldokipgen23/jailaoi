<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix 1: Set upload_type = 1 for tbl_music rows where it's NULL or 0
        DB::table('tbl_music')
            ->whereNull('upload_type')
            ->orWhere('upload_type', 0)
            ->update(['upload_type' => 1]);

        // Fix 2: Set upload_type = 1 for tbl_song rows where it's NULL or 0
        if (DB::getSchemaBuilder()->hasColumn('tbl_song', 'upload_type')) {
            DB::table('tbl_song')
                ->whereNull('upload_type')
                ->orWhere('upload_type', 0)
                ->update(['upload_type' => 1]);
        }

        // Fix 3: Strip upload/audio/ prefix from tbl_music.music
        DB::table('tbl_music')
            ->where('music', 'LIKE', 'upload/audio/%')
            ->update([
                'music' => DB::raw("REPLACE(music, 'upload/audio/', '')"),
            ]);

        // Fix 4: Strip upload/audio/ prefix from tbl_song.song_url
        if (DB::getSchemaBuilder()->hasColumn('tbl_song', 'song_url')) {
            DB::table('tbl_song')
                ->where('song_url', 'LIKE', 'upload/audio/%')
                ->update([
                    'song_url' => DB::raw("REPLACE(song_url, 'upload/audio/', '')"),
                ]);
        }

        // Fix 5: Strip upload/photos/ prefix from images
        $imageTables = [
            'tbl_music'    => ['portrait_img', 'landscape_img', 'ogtag_img'],
            'tbl_song'     => ['image'],
        ];
        foreach ($imageTables as $table => $columns) {
            if (!DB::getSchemaBuilder()->hasTable($table)) continue;
            foreach ($columns as $col) {
                if (!DB::getSchemaBuilder()->hasColumn($table, $col)) continue;
                DB::table($table)
                    ->where($col, 'LIKE', 'upload/photos/%')
                    ->update([$col => DB::raw("REPLACE($col, 'upload/photos/', '')")]);
            }
        }

        // Fix 6: Strip upload/audio/ prefix from tbl_content.content
        if (DB::getSchemaBuilder()->hasTable('tbl_content')) {
            DB::table('tbl_content')
                ->where('content', 'LIKE', 'upload/audio/%')
                ->update([
                    'content' => DB::raw("REPLACE(content, 'upload/audio/', '')"),
                ]);
        }
    }

    public function down(): void
    {
        // No rollback — data fix only
    }
};
