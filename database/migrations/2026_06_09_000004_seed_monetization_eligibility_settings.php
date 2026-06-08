<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = [
            ['key' => 'eligibility_min_plays', 'value' => '100'],
            ['key' => 'eligibility_min_followers', 'value' => '50'],
            ['key' => 'eligibility_min_monthly_plays', 'value' => '30'],
            ['key' => 'eligibility_min_tracks', 'value' => '1'],
            ['key' => 'eligibility_min_account_days', 'value' => '30'],
        ];

        foreach ($keys as $k) {
            $existing = DB::table('tbl_general_setting')->where('key', $k['key'])->first();
            if (!$existing) {
                DB::table('tbl_general_setting')->insert($k);
            }
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->whereIn('key', [
            'eligibility_min_plays',
            'eligibility_min_followers',
            'eligibility_min_monthly_plays',
            'eligibility_min_tracks',
            'eligibility_min_account_days',
        ])->delete();
    }
};
