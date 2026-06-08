<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// JAILAOI: Remove PayPal + Mobile Money, replace with Bank Transfer + UPI
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_general_setting')
            ->where('key', 'allowed_payment_methods')
            ->update(['value' => 'bank,upi']);
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')
            ->where('key', 'allowed_payment_methods')
            ->update(['value' => 'paypal,bank,mobile_money']);
    }
};
