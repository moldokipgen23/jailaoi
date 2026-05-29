<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_song', function (Blueprint $t) {
            $t->string('duration')->nullable()->after('song_url');
            $t->text('waveform')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_song', function (Blueprint $t) {
            $t->dropColumn(['duration', 'waveform']);
        });
    }
};
