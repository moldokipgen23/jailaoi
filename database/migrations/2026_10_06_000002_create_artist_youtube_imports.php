<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('artist_youtube_imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('video_id', 11);
            $table->string('status', 20)->default('queued');
            $table->string('phase', 30)->default('queued');
            $table->string('title')->default('');
            $table->string('filename')->nullable();
            $table->string('storage_driver', 20)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('message', 255)->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->timestamp('rights_confirmed_at');
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'updated_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('artist_youtube_imports'); }
};
