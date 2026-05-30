<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tbl_language')) {
            Schema::create('tbl_language', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->integer('storage_type')->default(0);
                $table->string('image', 255);
                $table->integer('sort_order')->default(0);
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tbl_language');
    }
};
