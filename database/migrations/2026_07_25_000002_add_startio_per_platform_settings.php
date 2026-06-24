<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seeds = [
            // Per-platform iOS keys (Android keys already exist from original seeding)
            ['key' => 'ios_startio_enabled',              'value' => '0'],
            ['key' => 'ios_startio_banner_enabled',       'value' => '1'],
            ['key' => 'ios_startio_interstitial_enabled', 'value' => '1'],
            ['key' => 'ios_startio_rewarded_enabled',     'value' => '0'],
            // App ID placeholders (admin fills these in)
            ['key' => 'startio_app_id_android',           'value' => ''],
            ['key' => 'startio_app_id_ios',               'value' => ''],
        ];

        foreach ($seeds as $seed) {
            $exists = DB::table('tbl_general_setting')->where('key', $seed['key'])->exists();
            if (!$exists) {
                DB::table('tbl_general_setting')->insert([
                    'key'        => $seed['key'],
                    'value'      => $seed['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->whereIn('key', [
            'ios_startio_enabled',
            'ios_startio_banner_enabled',
            'ios_startio_interstitial_enabled',
            'ios_startio_rewarded_enabled',
            'startio_app_id_android',
            'startio_app_id_ios',
        ])->delete();
    }
};
