<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_album')) {
            Schema::create('tbl_album', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('channel_id', 255);
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('cover_image', 255)->default('');
                $table->integer('cover_image_storage_type')->default(1);
                $table->date('release_date')->nullable();
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }

        $musicTable = Schema::hasTable('tbl_music') ? 'tbl_music' : 'tbl_content';
        if (Schema::hasTable($musicTable)) {
            Schema::table($musicTable, function (Blueprint $table) use ($musicTable) {
                if (!Schema::hasColumn($musicTable, 'album_id')) {
                    $table->unsignedBigInteger('album_id')->nullable()->after('language_id');
                }
                if (!Schema::hasColumn($musicTable, 'lyrics')) {
                    $table->text('lyrics')->nullable()->after('description');
                }
                if (!Schema::hasColumn($musicTable, 'waveform_data')) {
                    $table->string('waveform_data', 255)->default('')->after('content');
                }
            });
        }
    }

    public function down(): void
    {
        $musicTable = Schema::hasTable('tbl_music') ? 'tbl_music' : 'tbl_content';
        if (Schema::hasTable($musicTable)) {
            Schema::table($musicTable, function (Blueprint $table) use ($musicTable) {
                if (Schema::hasColumn($musicTable, 'album_id')) {
                    $table->dropColumn('album_id');
                }
                if (Schema::hasColumn($musicTable, 'lyrics')) {
                    $table->dropColumn('lyrics');
                }
                if (Schema::hasColumn($musicTable, 'waveform_data')) {
                    $table->dropColumn('waveform_data');
                }
            });
        }
        Schema::dropIfExists('tbl_album');
    }
};
