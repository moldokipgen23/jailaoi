<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllMissingTables extends Migration
{
    private array $tables = [];

    public function __construct()
    {
        $this->tables = [
            'tbl_song' => function (Blueprint $t) {
                $t->id();
                $t->integer('category_id')->nullable();
                $t->integer('language_id')->nullable();
                $t->integer('city_id')->nullable();
                $t->integer('artist_id')->nullable();
                $t->string('name');
                $t->string('image')->nullable();
                $t->string('song_upload_type')->default('server_video');
                $t->string('song_url')->nullable();
                $t->integer('is_premium')->default(0);
                $t->integer('total_play')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_category' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('image')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_language' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('image')->nullable();
                $t->timestamps();
            },
            'tbl_city' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('image')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_banner' => function (Blueprint $t) {
                $t->id();
                $t->integer('type')->nullable();
                $t->integer('content_id')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_section' => function (Blueprint $t) {
                $t->id();
                $t->string('title')->nullable();
                $t->string('sub_title')->nullable();
                $t->integer('type')->nullable();
                $t->integer('artist_id')->nullable();
                $t->integer('category_id')->nullable();
                $t->integer('language_id')->nullable();
                $t->integer('city_id')->nullable();
                $t->string('screen_layout')->nullable();
                $t->integer('is_premium')->default(0);
                $t->integer('order_by_upload')->default(0);
                $t->integer('order_by_play')->default(0);
                $t->integer('is_paid')->default(0);
                $t->integer('no_of_content')->default(0);
                $t->integer('view_all')->default(0);
                $t->integer('sortable')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_comment' => function (Blueprint $t) {
                $t->id();
                $t->integer('type')->nullable();
                $t->integer('user_id')->nullable();
                $t->integer('content_id')->nullable();
                $t->integer('episode_id')->nullable();
                $t->text('comment')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_favorite' => function (Blueprint $t) {
                $t->id();
                $t->integer('type')->nullable();
                $t->integer('content_id')->nullable();
                $t->integer('user_id')->nullable();
                $t->timestamps();
            },
            'tbl_notification' => function (Blueprint $t) {
                $t->id();
                $t->string('title')->nullable();
                $t->text('description')->nullable();
                $t->string('image')->nullable();
                $t->timestamps();
            },
            'tbl_play' => function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->integer('type')->nullable();
                $t->integer('content_id')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_package' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('image')->nullable();
                $t->string('price')->nullable();
                $t->string('time')->nullable();
                $t->string('type')->nullable();
                $t->string('android_product_package')->nullable();
                $t->string('ios_product_package')->nullable();
                $t->string('web_product_package')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_transaction' => function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->integer('package_id')->nullable();
                $t->string('transaction_id')->nullable();
                $t->string('price')->nullable();
                $t->text('description')->nullable();
                $t->string('expiry_date')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_payment_option' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('visibility')->nullable();
                $t->string('is_live')->nullable();
                $t->text('key_1')->nullable();
                $t->text('key_2')->nullable();
                $t->text('key_3')->nullable();
                $t->timestamps();
            },
            'tbl_podcast' => function (Blueprint $t) {
                $t->id();
                $t->integer('category_id')->nullable();
                $t->string('language_id')->nullable();
                $t->string('title');
                $t->string('portrait_img')->nullable();
                $t->string('landscape_img')->nullable();
                $t->text('description')->nullable();
                $t->integer('is_premium')->default(0);
                $t->integer('total_play')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_podcast_section' => function (Blueprint $t) {
                $t->id();
                $t->string('title')->nullable();
                $t->string('sub_title')->nullable();
                $t->integer('category_id')->nullable();
                $t->integer('language_id')->nullable();
                $t->string('screen_layout')->nullable();
                $t->integer('is_premium')->default(0);
                $t->integer('order_by_upload')->default(0);
                $t->integer('order_by_play')->default(0);
                $t->integer('no_of_content')->default(0);
                $t->integer('view_all')->default(0);
                $t->integer('sortable')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_episode' => function (Blueprint $t) {
                $t->id();
                $t->integer('podcasts_id')->nullable();
                $t->string('name');
                $t->text('description')->nullable();
                $t->string('portrait_img')->nullable();
                $t->string('landscape_img')->nullable();
                $t->string('episode_upload_type')->default('server_video');
                $t->string('episode_audio')->nullable();
                $t->integer('duration')->default(0);
                $t->integer('total_play')->default(0);
                $t->integer('sortable')->default(0);
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_live_event' => function (Blueprint $t) {
                $t->id();
                $t->string('title');
                $t->string('portrait_img')->nullable();
                $t->string('landscape_img')->nullable();
                $t->string('date')->nullable();
                $t->string('start_time')->nullable();
                $t->string('end_time')->nullable();
                $t->integer('is_paid')->default(0);
                $t->integer('price')->default(0);
                $t->integer('type')->nullable();
                $t->string('link')->nullable();
                $t->text('description')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_event_join_user' => function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->integer('live_event_id')->nullable();
                $t->integer('type')->nullable();
                $t->string('transaction_id')->nullable();
                $t->integer('price')->default(0);
                $t->text('description')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_onboarding_screen' => function (Blueprint $t) {
                $t->id();
                $t->string('title')->nullable();
                $t->string('image')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_social_link' => function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('image')->nullable();
                $t->string('url')->nullable();
                $t->integer('status')->default(1);
                $t->timestamps();
            },
            'tbl_user_notification_tracking' => function (Blueprint $t) {
                $t->id();
                $t->integer('user_id')->nullable();
                $t->integer('notification_id')->nullable();
                $t->timestamps();
            },
        ];
    }

    public function up()
    {
        foreach ($this->tables as $table => $schema) {
            if (!Schema::hasTable($table)) {
                Schema::create($table, $schema);
            }
        }
    }

    public function down()
    {
        foreach (array_keys($this->tables) as $table) {
            Schema::dropIfExists($table);
        }
    }
}
