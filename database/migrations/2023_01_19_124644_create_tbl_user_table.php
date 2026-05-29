<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->id();
            $table->text('firebase_id');
            $table->string('full_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_name');
            $table->string('email');
            $table->string('password');
            $table->string('image');
            $table->integer('email_verified_at')->default(0);
            $table->integer('mobile_verified_at')->default(0);
            $table->text('instagram_url');
            $table->text('facebook_url');
            $table->text('twitter_url');
            $table->text('biodata');
            $table->integer('type')->comment('1 = OTP, 2 = Social, 3 = Normal');
            $table->string('mobile_number');
            $table->text('location');
            $table->string('reference_code');
            $table->string('parent_reference_code');
            $table->string('device_token');
            $table->integer('total_points')->default(0);
            $table->integer('is_updated')->default(0);
            $table->integer('status')->default('1');           
            $table->date('date')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_user');
    }
}