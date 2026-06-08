<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_monetization_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status', 20)->default('pending');
            $table->text('admin_note')->nullable();
            $table->integer('snapshot_plays')->default(0);
            $table->integer('snapshot_followers')->default(0);
            $table->integer('snapshot_monthly_plays')->default(0);
            $table->integer('snapshot_tracks')->default(0);
            $table->decimal('snapshot_earnings', 10, 4)->default(0);
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_monetization_applications');
    }
};
