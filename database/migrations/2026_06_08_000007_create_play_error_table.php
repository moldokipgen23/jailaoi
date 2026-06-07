<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_play_error', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->integer('content_type')->nullable();
            $table->string('url', 1000)->nullable();
            $table->text('error_message')->nullable();
            $table->integer('http_status')->nullable();
            $table->timestamps();
            $table->index('content_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_play_error');
    }
};
