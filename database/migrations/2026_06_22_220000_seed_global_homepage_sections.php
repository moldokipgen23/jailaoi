<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $sections = [
            // ── PINNED PER-USER SMART SECTIONS (types 9–13) ──────────────────────
            [
                'title'           => 'Continue Listening',
                'sub_title'       => 'Pick up where you left off',
                'type'            => 9,
                'screen_layout'   => 'landscape',
                'no_of_content'   => 20,
                'is_pinned'       => 1,
                'sortable'        => 1,
            ],
            [
                'title'           => 'Liked Songs',
                'sub_title'       => 'Your saved favourites',
                'type'            => 10,
                'screen_layout'   => 'landscape',
                'no_of_content'   => 20,
                'is_pinned'       => 1,
                'sortable'        => 2,
            ],
            [
                'title'           => 'From Your Artists',
                'sub_title'       => 'Latest from artists you follow',
                'type'            => 11,
                'screen_layout'   => 'landscape',
                'no_of_content'   => 20,
                'is_pinned'       => 1,
                'sortable'        => 3,
            ],
            [
                'title'           => 'Because You Play',
                'sub_title'       => 'More from your favourite genre',
                'type'            => 12,
                'screen_layout'   => 'square',
                'no_of_content'   => 20,
                'is_pinned'       => 1,
                'sortable'        => 4,
            ],
            [
                'title'           => 'New in Your Language',
                'sub_title'       => 'Fresh tracks in your language',
                'type'            => 13,
                'screen_layout'   => 'square',
                'no_of_content'   => 20,
                'is_pinned'       => 1,
                'sortable'        => 5,
            ],

            // ── GLOBAL EDITORIAL SECTIONS ─────────────────────────────────────────
            [
                'title'           => 'New Releases',
                'sub_title'       => 'Just dropped',
                'type'            => 8,
                'screen_layout'   => 'landscape',
                'no_of_content'   => 20,
                'order_by_upload' => 1,
                'order_by_play'   => 0,
                'is_pinned'       => 0,
                'sortable'        => 6,
            ],
            [
                'title'           => 'Trending Now',
                'sub_title'       => 'Hot this week',
                'type'            => 8,
                'screen_layout'   => 'small_square',
                'no_of_content'   => 20,
                'order_by_upload' => 0,
                'order_by_play'   => 1,
                'time_window_days'=> 7,
                'is_pinned'       => 0,
                'sortable'        => 7,
            ],
            [
                'title'           => 'Browse Artists',
                'sub_title'       => 'Discover who\'s making music',
                'type'            => 4,
                'screen_layout'   => 'round',
                'no_of_content'   => 20,
                'is_pinned'       => 0,
                'sortable'        => 8,
            ],
            [
                'title'           => 'Browse Categories',
                'sub_title'       => 'Explore every genre',
                'type'            => 5,
                'screen_layout'   => 'square',
                'no_of_content'   => 12,
                'is_pinned'       => 0,
                'sortable'        => 9,
            ],
            [
                'title'           => 'Browse Languages',
                'sub_title'       => 'Music from around the world',
                'type'            => 6,
                'screen_layout'   => 'square',
                'no_of_content'   => 12,
                'is_pinned'       => 0,
                'sortable'        => 10,
            ],
        ];

        $base = [
            'user_id'          => 0,
            'section_type'     => 1,
            'artist_id'        => 0,
            'category_id'      => 0,
            'language_id'      => 0,
            'city_id'          => 0,
            'is_premium'       => 0,
            'order_by_upload'  => 1,
            'order_by_play'    => 0,
            'time_window_days' => 0,
            'is_paid'          => 0,
            'is_title'         => 1,
            'is_category'      => 1,
            'is_artist_name'   => 1,
            'view_all'         => 1,
            'status'           => 1,
            'is_pinned'        => 0,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];

        foreach ($sections as $section) {
            $row = array_merge($base, $section);
            // Skip if a section with the same title already exists globally
            $exists = DB::table('tbl_section')
                ->where('user_id', 0)
                ->where('title', $row['title'])
                ->exists();
            if (!$exists) {
                DB::table('tbl_section')->insert($row);
            }
        }
    }

    public function down(): void
    {
        $titles = [
            'Continue Listening', 'Liked Songs', 'From Your Artists',
            'Because You Play', 'New in Your Language', 'New Releases',
            'Trending Now', 'Browse Artists', 'Browse Categories', 'Browse Languages',
        ];
        DB::table('tbl_section')->where('user_id', 0)->whereIn('title', $titles)->delete();
    }
};
