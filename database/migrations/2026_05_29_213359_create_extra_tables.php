<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_playlist')) {
            Schema::create('tbl_playlist', function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->string('name');
                $t->integer('privacy')->default(0);
                $t->string('image')->nullable();
                $t->integer('plays')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('tbl_playlist_song')) {
            Schema::create('tbl_playlist_song', function (Blueprint $t) {
                $t->id();
                $t->integer('playlist_id');
                $t->integer('song_id');
                $t->integer('user_id')->nullable();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('tbl_album')) {
            Schema::create('tbl_album', function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->string('title');
                $t->text('description')->nullable();
                $t->integer('category_id')->default(0);
                $t->string('image')->nullable();
                $t->float('price')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('tbl_blog')) {
            Schema::create('tbl_blog', function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->text('content')->nullable();
                $t->text('description')->nullable();
                $t->string('image')->nullable();
                $t->integer('category')->default(0);
                $t->integer('view')->default(0);
                $t->string('tags')->nullable();
                $t->integer('created_by')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_blog');
        Schema::dropIfExists('tbl_album');
        Schema::dropIfExists('tbl_playlist_song');
        Schema::dropIfExists('tbl_playlist');
    }
};
