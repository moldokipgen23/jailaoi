<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_general_setting')) {
            $exists = DB::table('tbl_general_setting')
                ->where('key', 'audio_storage_driver')
                ->exists();

            if (!$exists) {
                DB::table('tbl_general_setting')->insert([
                    'key' => 'audio_storage_driver',
                    'value' => 'local',
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')
            ->where('key', 'audio_storage_driver')
            ->delete();
    }
};
