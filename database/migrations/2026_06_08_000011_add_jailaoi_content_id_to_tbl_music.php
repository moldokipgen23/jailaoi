<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// JAILAOI: Links tbl_music mirror records back to their source tbl_content record.
// When an artist uploads via the artist portal, a mirror is created in tbl_music
// so the Flutter app can play it. jailaoi_content_id tracks which Content record
// it came from so edits and deletes stay in sync.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_music', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_music', 'jailaoi_content_id')) {
                $table->unsignedBigInteger('jailaoi_content_id')->nullable()->default(null)->after('id');
                $table->index('jailaoi_content_id', 'idx_music_content_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_music', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_music', 'jailaoi_content_id')) {
                $table->dropIndex('idx_music_content_id');
                $table->dropColumn('jailaoi_content_id');
            }
        });
    }
};
