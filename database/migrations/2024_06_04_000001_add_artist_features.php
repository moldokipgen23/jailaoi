<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_subscriber')) {
            Schema::create('tbl_subscriber', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('to_user_id');
                $table->integer('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tbl_artist_requests')) {
            Schema::create('tbl_artist_requests', function (Blueprint $table) {
                $table->id()->unsigned();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('artist_name');
                $table->text('bio')->nullable();
                $table->string('status', 20)->default('pending');
                $table->text('admin_note')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('tbl_artist', 'user_id')) {
            Schema::table('tbl_artist', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('tbl_user')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('tbl_user', 'role')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->string('role', 20)->default('user')->after('status');
            });
        }
        if (!Schema::hasColumn('tbl_user', 'bio')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->text('bio')->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('tbl_user', 'cover_img')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->string('cover_img')->nullable()->after('image');
            });
        }
        if (!Schema::hasColumn('tbl_user', 'channel_id')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->string('channel_id')->nullable()->after('bio');
            });
        }
        if (!Schema::hasColumn('tbl_user', 'channel_name')) {
            Schema::table('tbl_user', function (Blueprint $table) {
                $table->string('channel_name')->nullable()->after('channel_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_subscriber');
        Schema::dropIfExists('tbl_artist_requests');

        if (Schema::hasColumn('tbl_artist', 'user_id')) {
            Schema::table('tbl_artist', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        $columns = ['role', 'bio', 'cover_img', 'channel_id', 'channel_name'];
        foreach ($columns as $col) {
            if (Schema::hasColumn('tbl_user', $col)) {
                Schema::table('tbl_user', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
