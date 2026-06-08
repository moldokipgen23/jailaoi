<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// JAILAOI: Adds missing DB indexes that are critical for API performance.
// Without these, every section_query() and time-window query does a full table scan.
return new class extends Migration
{
    public function up(): void
    {
        // tbl_section — queried on every app open for every tab
        if (Schema::hasTable('tbl_section')) {
            Schema::table('tbl_section', function (Blueprint $table) {
                if (!$this->indexExists('tbl_section', 'idx_section_type_status_sortable')) {
                    $table->index(['section_type', 'status', 'sortable'], 'idx_section_type_status_sortable');
                }
            });
        }

        // tbl_song — queried by section_query() for Song sections
        if (Schema::hasTable('tbl_song')) {
            Schema::table('tbl_song', function (Blueprint $table) {
                if (!$this->indexExists('tbl_song', 'idx_song_status_category')) {
                    $table->index(['status', 'category_id'], 'idx_song_status_category');
                }
                if (!$this->indexExists('tbl_song', 'idx_song_status_language')) {
                    $table->index(['status', 'language_id'], 'idx_song_status_language');
                }
                if (!$this->indexExists('tbl_song', 'idx_song_total_play')) {
                    $table->index(['status', 'total_play'], 'idx_song_total_play');
                }
            });
        }

        // tbl_music — queried by section_query() for Music sections (most common)
        if (Schema::hasTable('tbl_music')) {
            Schema::table('tbl_music', function (Blueprint $table) {
                if (!$this->indexExists('tbl_music', 'idx_music_status_category')) {
                    $table->index(['status', 'category_id'], 'idx_music_status_category');
                }
                if (!$this->indexExists('tbl_music', 'idx_music_status_language')) {
                    $table->index(['status', 'language_id'], 'idx_music_status_language');
                }
                if (!$this->indexExists('tbl_music', 'idx_music_total_play')) {
                    $table->index(['status', 'total_play'], 'idx_music_total_play');
                }
            });
        }

        // tbl_user_action — the MOST critical index.
        // The time-window "Top This Week" query does:
        //   WHERE action=1 AND content_type=X AND created_at >= ?
        // Without this index it scans the entire user_action table every time.
        if (Schema::hasTable('tbl_user_action')) {
            Schema::table('tbl_user_action', function (Blueprint $table) {
                if (!$this->indexExists('tbl_user_action', 'idx_ua_action_type_date')) {
                    $table->index(['action', 'content_type', 'created_at'], 'idx_ua_action_type_date');
                }
                if (!$this->indexExists('tbl_user_action', 'idx_ua_content')) {
                    $table->index(['content_id', 'content_type'], 'idx_ua_content');
                }
            });
        }

        // tbl_artist_earnings — queried for deduplication check on every play
        if (Schema::hasTable('tbl_artist_earnings')) {
            Schema::table('tbl_artist_earnings', function (Blueprint $table) {
                if (!$this->indexExists('tbl_artist_earnings', 'idx_ae_dedup')) {
                    $table->index(['user_id', 'content_id', 'content_type'], 'idx_ae_dedup');
                }
            });
        }
    }

    public function down(): void
    {
        $indexes = [
            'tbl_section'         => 'idx_section_type_status_sortable',
            'tbl_song'            => ['idx_song_status_category', 'idx_song_status_language', 'idx_song_total_play'],
            'tbl_music'           => ['idx_music_status_category', 'idx_music_status_language', 'idx_music_total_play'],
            'tbl_user_action'     => ['idx_ua_action_type_date', 'idx_ua_content'],
            'tbl_artist_earnings' => 'idx_ae_dedup',
        ];

        foreach ($indexes as $table => $names) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($names) {
                    foreach ((array) $names as $name) {
                        try { $t->dropIndex($name); } catch (\Exception $e) {}
                    }
                });
            }
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
