<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add wallet_balance to tbl_artist
        if (Schema::hasTable('tbl_artist') && !Schema::hasColumn('tbl_artist', 'wallet_balance')) {
            Schema::table('tbl_artist', function (Blueprint $table) {
                $table->decimal('wallet_balance', 12, 4)->default(0)->after('status')
                    ->comment('Current withdrawable balance. Updated by settlement + withdrawal hold/release');
            });
        }

        // Update default min withdrawal to ₹200 (INR)
        $exists = DB::table('tbl_general_setting')->where('key', 'min_withdrawal_amount')->exists();
        if ($exists) {
            $current = DB::table('tbl_general_setting')->where('key', 'min_withdrawal_amount')->value('value');
            // Only update if it's still the old $10 default
            if ($current == '10') {
                DB::table('tbl_general_setting')->where('key', 'min_withdrawal_amount')->update([
                    'value' => '200',
                    'updated_at' => now(),
                ]);
            }
        } else {
            DB::table('tbl_general_setting')->insert([
                'key' => 'min_withdrawal_amount',
                'value' => '200',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_artist', 'wallet_balance')) {
            Schema::table('tbl_artist', function (Blueprint $table) {
                $table->dropColumn('wallet_balance');
            });
        }
    }
};
