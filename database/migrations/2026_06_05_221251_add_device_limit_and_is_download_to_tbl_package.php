<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_package', function (Blueprint $table) {
            $table->integer('device_limit')->nullable()->after('color');
            $table->integer('is_download')->nullable()->after('device_limit');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_package', function (Blueprint $table) {
            $table->dropColumn(['device_limit', 'is_download']);
        });
    }
};
