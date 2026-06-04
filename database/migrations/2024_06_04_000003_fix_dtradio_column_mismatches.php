<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_artist')) {
            if (!Schema::hasColumn('tbl_artist', 'type')) {
                Schema::table('tbl_artist', function (Blueprint $table) {
                    $table->integer('type')->default(0)->comment('1=Song, 2=Podcast, 3=Music');
                });
            }
            if (!Schema::hasColumn('tbl_artist', 'sort_order')) {
                Schema::table('tbl_artist', function (Blueprint $table) {
                    $table->integer('sort_order')->default(0);
                });
            }
        }

        $fixes = [
            'tbl_banner' => [
                ['type', 'integer', 0],
                ['content_id', 'integer', 0],
            ],
            'tbl_live_event' => [
                ['date', 'date', null, true],
                ['start_time', 'time', null, true],
                ['end_time', 'time', null, true],
                ['link', 'text', null, true],
            ],
            'tbl_section' => [
                ['user_id', 'integer', 0],
                ['section_type', 'integer', 0],
                ['is_premium', 'integer', 0],
                ['order_by_upload', 'integer', 0],
                ['order_by_play', 'integer', 0],
                ['is_paid', 'integer', 0],
                ['is_title', 'integer', 0],
                ['is_category', 'integer', 0],
                ['is_artist_name', 'integer', 0],
                ['no_of_content', 'integer', 10],
                ['view_all', 'integer', 0],
                ['sortable', 'integer', 0],
            ],
            'tbl_user' => [
                ['country_code', 'string', ''],
                ['country_name', 'string', ''],
                ['user_name', 'string', ''],
                ['type', 'integer', 0],
                ['device_type', 'integer', 0],
                ['device_token', 'string', ''],
            ],
        ];

        foreach ($fixes as $table => $columns) {
            if (!Schema::hasTable($table)) continue;
            foreach ($columns as $col) {
                $colName = $col[0];
                $colType = $col[1];
                $default = $col[2];
                $nullable = $col[3] ?? false;
                if (!Schema::hasColumn($table, $colName)) {
                    Schema::table($table, function (Blueprint $table) use ($colName, $colType, $default, $nullable) {
                        if ($colType === 'integer') {
                            $col = $table->integer($colName)->default($default);
                        } elseif ($colType === 'string') {
                            $col = $table->string($colName)->default($default);
                        } elseif ($colType === 'text') {
                            $col = $table->text($colName)->nullable();
                            $nullable = false;
                        } elseif ($colType === 'date') {
                            $col = $table->date($colName)->nullable();
                            $nullable = false;
                        } elseif ($colType === 'time') {
                            $col = $table->time($colName)->nullable();
                            $nullable = false;
                        }
                        if ($nullable) {
                            $col->nullable()->change();
                        }
                    });
                }
            }
        }
    }

    public function down(): void
    {
    }
};
