<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_section', function (Blueprint $table) {
            $table->tinyInteger('is_fixed')->default(0)->comment('1 = pinned to top, sorted by sort_order among pinned');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_section', function (Blueprint $table) {
            $table->dropColumn('is_fixed');
        });
    }
};
