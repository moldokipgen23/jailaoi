<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_music', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_music', 'release_year')) {
                $table->smallInteger('release_year')->unsigned()->nullable()->after('description');
            }
            if (!Schema::hasColumn('tbl_music', 'release_date')) {
                $table->date('release_date')->nullable()->after('release_year');
            }
            if (!Schema::hasColumn('tbl_music', 'tags')) {
                $table->string('tags', 500)->nullable()->after('release_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_music', function (Blueprint $table) {
            $table->dropColumn(['release_year', 'release_date', 'tags']);
        });
    }
};
