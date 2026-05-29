<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersAndArtistLink extends Migration
{
    public function up()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->string('role', 20)->default('user')->after('status')->comment('user, artist, admin');
            $table->text('bio')->nullable()->after('role');
        });

        Schema::table('tbl_artist', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->unsigned()->after('id');
            $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->dropColumn(['role', 'bio']);
        });

        Schema::table('tbl_artist', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
