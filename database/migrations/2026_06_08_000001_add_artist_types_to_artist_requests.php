<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_artist_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_artist_requests', 'artist_types')) {
                $table->string('artist_types', 50)->default('music')->after('bio')
                    ->comment('Comma-separated: music, podcast');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_artist_requests', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_artist_requests', 'artist_types')) {
                $table->dropColumn('artist_types');
            }
        });
    }
};
