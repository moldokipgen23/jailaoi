<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_section') && !Schema::hasColumn('tbl_section', 'sub_title')) {
            Schema::table('tbl_section', function (Blueprint $table) {
                $table->string('sub_title')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_section') && Schema::hasColumn('tbl_section', 'sub_title')) {
            Schema::table('tbl_section', function (Blueprint $table) {
                $table->dropColumn('sub_title');
            });
        }
    }
};
