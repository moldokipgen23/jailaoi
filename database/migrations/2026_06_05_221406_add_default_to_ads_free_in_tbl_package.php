<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tbl_package', 'ads_free')) {
            DB::statement("ALTER TABLE `tbl_package` MODIFY `ads_free` INT NOT NULL DEFAULT 0");
        } else {
            Schema::table('tbl_package', function (Blueprint $table) {
                $table->integer('ads_free')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_package', 'ads_free')) {
            DB::statement("ALTER TABLE `tbl_package` MODIFY `ads_free` INT");
        }
    }
};
