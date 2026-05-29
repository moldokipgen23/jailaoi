<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblArtistRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_artist_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('artist_name');
            $table->text('bio')->nullable();
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_artist_requests');
    }
}
