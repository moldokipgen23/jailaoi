<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_artist_kyc', function (Blueprint $table) {
            $table->id();
            $table->integer('artist_id');
            $table->integer('user_id');
            $table->string('legal_first_name');
            $table->string('legal_last_name');
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('id_type');
            $table->string('id_number');
            $table->string('id_front_img');
            $table->string('id_back_img');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('payment_method');
            $table->text('payment_details');
            $table->string('status', 20)->default('not_started');
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_artist_kyc');
    }
};
