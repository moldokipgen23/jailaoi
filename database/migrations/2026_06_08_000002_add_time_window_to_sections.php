<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_section', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_section', 'time_window_days')) {
                $table->integer('time_window_days')->default(0)->after('order_by_play')
                    ->comment('0=all time, 7=last week, 30=last month, 90=last 90 days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_section', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_section', 'time_window_days')) {
                $table->dropColumn('time_window_days');
            }
        });
    }
};
