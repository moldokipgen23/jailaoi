<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_batch', function (Blueprint $table) {
            $table->string('input_file_id', 255)->default('')->change();
            $table->string('batch_id', 255)->default('')->change();
            $table->string('output_file_id', 255)->default('')->change();
            $table->string('error_file_id', 255)->default('')->change();
            $table->string('status', 50)->default('')->change();
        });
    }

    public function down(): void
    {
        // intentionally not reverting — INT was incorrect
    }
};
