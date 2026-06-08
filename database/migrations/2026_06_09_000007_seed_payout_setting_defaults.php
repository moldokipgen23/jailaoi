<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = [
            ['key' => 'allowed_payment_methods', 'value' => 'paypal,bank,mobile_money'],
            ['key' => 'kyc_required_for_withdrawal', 'value' => '1'],
            ['key' => 'allowed_id_types', 'value' => 'passport,national_id,drivers_license'],
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
            'allowed_payment_methods',
            'kyc_required_for_withdrawal',
            'allowed_id_types',
        ])->delete();
    }
};
