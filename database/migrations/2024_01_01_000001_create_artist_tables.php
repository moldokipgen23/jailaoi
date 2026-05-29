<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_user', 'role')) {
                $table->string('role', 20)->default('user')->after('status');
            }
            if (!Schema::hasColumn('tbl_user', 'bio')) {
                $table->text('bio')->nullable()->after('description');
            }
        });

        if (!Schema::hasTable('tbl_artist')) {
            Schema::create('tbl_artist', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('image')->default('');
                $table->text('bio');
                $table->integer('status')->default(1);
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('tbl_artist_requests')) {
            Schema::create('tbl_artist_requests', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('artist_name');
                $table->text('bio')->nullable();
                $table->string('status', 20)->default('pending')->comment('pending, approved, rejected');
                $table->text('admin_note')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_artist_requests');
        Schema::dropIfExists('tbl_artist');
        if (Schema::hasColumn('tbl_user', 'role')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        if (Schema::hasColumn('tbl_user', 'bio')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->dropColumn('bio');
            });
        }
    }
};
