<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_artist_earnings')) {
            Schema::create('tbl_artist_earnings', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('artist_id');
                $table->unsignedBigInteger('user_id')->comment('listener who played');
                $table->unsignedBigInteger('content_id');
                $table->tinyInteger('content_type')->comment('1=Song, 3=Music');
                $table->decimal('amount', 10, 6)->default(0);
                $table->timestamps();
                $table->index('artist_id');
                $table->index(['artist_id', 'created_at']);
            });
        }

        if (!Schema::hasTable('tbl_withdrawal_requests')) {
            Schema::create('tbl_withdrawal_requests', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('artist_id');
                $table->unsignedBigInteger('user_id')->comment('artist owner user');
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 50)->comment('paypal, bank, mobile_money, etc');
                $table->text('payment_details')->comment('account info / paypal email');
                $table->string('status', 20)->default('pending')->comment('pending, approved, rejected, paid');
                $table->text('admin_note')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->index('artist_id');
                $table->index('status');
            });
        }

        // Add per-stream payout rate to general settings
        $exists = DB::table('tbl_general_setting')->where('key', 'payout_rate_per_stream')->exists();
        if (!$exists) {
            DB::table('tbl_general_setting')->insert([
                'key' => 'payout_rate_per_stream',
                'value' => '0.001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $currencyExists = DB::table('tbl_general_setting')->where('key', 'payout_currency')->exists();
        if (!$currencyExists) {
            DB::table('tbl_general_setting')->insert([
                'key' => 'payout_currency',
                'value' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $minExists = DB::table('tbl_general_setting')->where('key', 'min_withdrawal_amount')->exists();
        if (!$minExists) {
            DB::table('tbl_general_setting')->insert([
                'key' => 'min_withdrawal_amount',
                'value' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_withdrawal_requests');
        Schema::dropIfExists('tbl_artist_earnings');
        DB::table('tbl_general_setting')->whereIn('key', [
            'payout_rate_per_stream',
            'payout_currency',
            'min_withdrawal_amount',
        ])->delete();
    }
};
