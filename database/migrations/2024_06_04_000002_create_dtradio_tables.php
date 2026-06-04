<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createTableIfMissing('tbl_banner', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('type')->comment('1=Song, 2=Podcast, 3=Music');
            $table->integer('content_id');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_batch', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('input_file_id');
            $table->string('batch_id');
            $table->integer('output_file_id');
            $table->integer('error_file_id');
            $table->string('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_city', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('name');
            $table->string('image');
            $table->integer('sort_order');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_event_join_user', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->integer('live_event_id');
            $table->integer('type')->comment('0=Free, 1=Paid');
            $table->string('transaction_id');
            $table->string('price');
            $table->text('description');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_favorite', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('type')->comment('1=Song, 2=Podcast, 3=Music');
            $table->integer('content_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_follow', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->integer('artist_id');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_live_event', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('title');
            $table->string('portrait_img');
            $table->string('landscape_img');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('is_paid');
            $table->string('price');
            $table->integer('type')->comment('1=Audio, 2=Video');
            $table->text('link');
            $table->text('description');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_notification_configuration', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('type');
            $table->integer('send_mail');
            $table->integer('send_notification');
            $table->integer('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_play', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->integer('type')->comment('1=Song, 2=Podcast');
            $table->integer('content_id');
            $table->integer('episode_id');
            $table->integer('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_smtp_setting', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('protocol');
            $table->string('host');
            $table->string('port');
            $table->string('user');
            $table->string('pass');
            $table->string('from_name');
            $table->string('from_email');
            $table->integer('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_social_link', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('name');
            $table->string('image');
            $table->text('url');
            $table->integer('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_user_action', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->integer('content_type');
            $table->integer('content_id');
            $table->integer('category_id');
            $table->integer('language_id');
            $table->integer('city_id');
            $table->integer('artist_id');
            $table->string('action');
            $table->string('time_spend');
            $table->string('content_duration');
            $table->integer('status');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_user_notification_tracking', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->integer('notification_id');
            $table->timestamps();
        });

        $this->createTableIfMissing('tbl_user_summary', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->integer('user_id');
            $table->text('score_json');
            $table->integer('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'tbl_banner', 'tbl_batch', 'tbl_city', 'tbl_event_join_user',
            'tbl_favorite', 'tbl_follow', 'tbl_live_event',
            'tbl_notification_configuration', 'tbl_play', 'tbl_smtp_setting',
            'tbl_social_link', 'tbl_user_action', 'tbl_user_notification_tracking',
            'tbl_user_summary',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function createTableIfMissing($tableName, $callback)
    {
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, $callback);
            $this->command?->info("Created {$tableName}");
        } else {
            $this->command?->warn("{$tableName} already exists, skipped");
        }
    }
};
