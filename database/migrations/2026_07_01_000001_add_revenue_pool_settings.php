<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add revenue pool settings to tbl_general_setting
        $seeds = [
            ['key' => 'earnings_model',     'value' => 'pool'],
            ['key' => 'platform_cut_pct',   'value' => '45'],
            ['key' => 'settlement_day',     'value' => '5'],
        ];

        foreach ($seeds as $seed) {
            $exists = DB::table('tbl_general_setting')->where('key', $seed['key'])->exists();
            if (!$exists) {
                DB::table('tbl_general_setting')->insert([
                    'key'         => $seed['key'],
                    'value'       => $seed['value'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // Add settled_month column to tbl_artist_earnings
        if (Schema::hasTable('tbl_artist_earnings') && !Schema::hasColumn('tbl_artist_earnings', 'settled_month')) {
            Schema::table('tbl_artist_earnings', function (Blueprint $table) {
                $table->string('settled_month', 7)->nullable()->after('amount')
                    ->comment('YYYY-MM of settlement batch. NULL = unsettled');
                $table->index('settled_month');
            });
        }

        // Create earnings settlements audit table
        if (!Schema::hasTable('tbl_earnings_settlements')) {
            Schema::create('tbl_earnings_settlements', function (Blueprint $table) {
                $table->id();
                $table->string('month', 7)->unique()->comment('YYYY-MM');
                $table->decimal('total_revenue', 12, 2)->default(0);
                $table->decimal('platform_cut', 12, 2)->default(0);
                $table->decimal('pool_amount', 12, 2)->default(0);
                $table->bigInteger('total_streams')->default(0);
                $table->decimal('rate_per_stream', 12, 6)->default(0);
                $table->decimal('additional_revenue', 12, 2)->default(0)->comment('ad revenue etc, added manually');
                $table->timestamp('settled_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->whereIn('key', [
            'earnings_model', 'platform_cut_pct', 'settlement_day',
        ])->delete();

        if (Schema::hasColumn('tbl_artist_earnings', 'settled_month')) {
            Schema::table('tbl_artist_earnings', function (Blueprint $table) {
                $table->dropIndex(['settled_month']);
                $table->dropColumn('settled_month');
            });
        }

        Schema::dropIfExists('tbl_earnings_settlements');
    }
};
