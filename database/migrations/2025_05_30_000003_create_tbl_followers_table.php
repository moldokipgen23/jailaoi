<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblFollowersTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_followers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->comment('The user who follows');
            $table->bigInteger('artist_id')->unsigned()->comment('The artist being followed');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('cascade');
            $table->foreign('artist_id')->references('id')->on('tbl_artist')->onDelete('cascade');
            $table->unique(['user_id', 'artist_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_followers');
    }
}
