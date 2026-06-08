<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = [
            ['key' => 'min_streams_for_payout', 'value' => '50'],
            ['key' => 'min_earnings_for_payout', 'value' => '5.00'],
            ['key' => 'min_account_days_for_payout', 'value' => '30'],
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
            'min_streams_for_payout',
            'min_earnings_for_payout',
            'min_account_days_for_payout',
        ])->delete();
    }
};
