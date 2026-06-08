<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('tbl_general_setting')->where('key', 'monetization_strict_eligibility')->first();
        if (!$existing) {
            DB::table('tbl_general_setting')->insert([
                'key' => 'monetization_strict_eligibility',
                'value' => '1',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->where('key', 'monetization_strict_eligibility')->delete();
    }
};
