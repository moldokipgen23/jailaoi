<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seeds = [
            ['key' => 'startio_enabled',              'value' => '0'],
            ['key' => 'startio_app_id_android',        'value' => ''],
            ['key' => 'startio_app_id_ios',            'value' => ''],
            ['key' => 'startio_banner_enabled',        'value' => '1'],
            ['key' => 'startio_interstitial_enabled',  'value' => '1'],
            ['key' => 'startio_rewarded_enabled',      'value' => '0'],
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
            'startio_enabled', 'startio_app_id_android', 'startio_app_id_ios',
            'startio_banner_enabled', 'startio_interstitial_enabled', 'startio_rewarded_enabled',
        ])->delete();
    }
};
