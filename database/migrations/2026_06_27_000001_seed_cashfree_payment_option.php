<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only insert if not already present
        if (!DB::table('tbl_payment_option')->where('name', 'cashfree')->exists()) {
            DB::table('tbl_payment_option')->insert([
                'name'       => 'cashfree',
                'visibility' => '0',   // admin enables it from the panel
                'is_live'    => '0',   // sandbox by default
                'key_1'      => '',    // Cashfree App ID
                'key_2'      => '',    // Cashfree Secret Key
                'key_3'      => '',    // reserved
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_payment_option')->where('name', 'cashfree')->delete();
    }
};
