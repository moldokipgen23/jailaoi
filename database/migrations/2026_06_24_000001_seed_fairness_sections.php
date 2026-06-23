<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedFairnessSections extends Migration
{
    public function up()
    {
        // Wipe ALL global home sections so we start fresh with the curated layout
        DB::table('tbl_section')->where('section_type', 1)->where('user_id', 0)->delete();

        $now  = now();
        $sort = 1;

        $base = [
            'user_id'         => 0,
            'artist_id'       => 0,
            'category_id'     => 0,
            'language_id'     => 0,
            'city_id'         => 0,
            'is_premium'      => 2,  // 2 = show all (free + premium)
            'is_paid'         => 0,
            'is_title'        => 1,
            'is_category'     => 1,
            'is_artist_name'  => 1,
            'order_by_upload' => 0,
            'order_by_play'   => 0,
            'time_window_days'=> 0,
            'view_all'        => 1,
            'no_of_content'   => 20,
            'screen_layout'   => 'landscape',
            'section_type'    => 1,
            'is_fixed'        => 0,
            'is_pinned'       => 0,
            'status'          => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        $sections = [

            // ── 1. Continue Listening ────────────────────────────────────
            // Shows logged-in user's recently played music. Hidden for guests.
            array_merge($base, [
                'title'         => 'Continue Listening',
                'sub_title'     => 'Pick up where you left off',
                'type'          => 9,
                'screen_layout' => 'landscape',
                'no_of_content' => 10,
                'view_all'      => 0,
                'sortable'      => $sort++,
            ]),

            // ── 2. Trending This Week ────────────────────────────────────
            // Play counts from the last 7 days only — NOT all-time.
            // Artist diversity cap (max 2 per artist) applies via code.
            array_merge($base, [
                'title'           => 'Trending This Week',
                'sub_title'       => 'What everyone is playing right now',
                'type'            => 8,
                'screen_layout'   => 'landscape',
                'order_by_play'   => 1,
                'time_window_days'=> 7,
                'no_of_content'   => 20,
                'sortable'        => $sort++,
            ]),

            // ── 3. New Releases ──────────────────────────────────────────
            // Sorted by upload date — freshest music first.
            array_merge($base, [
                'title'           => 'New Releases',
                'sub_title'       => 'Fresh music just dropped',
                'type'            => 8,
                'screen_layout'   => 'square',
                'order_by_upload' => 1,
                'order_by_play'   => 0,
                'no_of_content'   => 20,
                'sortable'        => $sort++,
            ]),

            // ── 4. Based on Your Taste ───────────────────────────────────
            // Personalized: newest music in the user's most-played category.
            array_merge($base, [
                'title'         => 'Based on Your Taste',
                'sub_title'     => 'Picked for you',
                'type'          => 12,
                'screen_layout' => 'square',
                'no_of_content' => 20,
                'sortable'      => $sort++,
            ]),

            // ── 5. Hidden Gems ───────────────────────────────────────────
            // Rising tracks (50–5000 total plays) ranked by play velocity.
            // Gives new artists a fair chance at discovery.
            array_merge($base, [
                'title'         => 'Hidden Gems',
                'sub_title'     => "Rising tracks you haven't heard yet",
                'type'          => 14,
                'screen_layout' => 'landscape',
                'no_of_content' => 20,
                'sortable'      => $sort++,
            ]),

            // ── 6. From Artists You Follow ───────────────────────────────
            // Personalized: latest uploads from followed artists.
            array_merge($base, [
                'title'           => 'From Artists You Follow',
                'sub_title'       => 'New from your favourites',
                'type'            => 11,
                'screen_layout'   => 'landscape',
                'order_by_upload' => 1,
                'no_of_content'   => 20,
                'sortable'        => $sort++,
            ]),

            // ── 7. Liked Songs ───────────────────────────────────────────
            // Personalized: user's favourited tracks, newest first.
            array_merge($base, [
                'title'         => 'Liked Songs',
                'sub_title'     => 'Your saved favourites',
                'type'          => 10,
                'screen_layout' => 'square',
                'no_of_content' => 20,
                'view_all'      => 0,
                'sortable'      => $sort++,
            ]),

            // ── 8. New in Your Language ──────────────────────────────────
            // Personalized: newest music in the user's most-played language.
            array_merge($base, [
                'title'         => 'New in Your Language',
                'sub_title'     => 'Music that speaks your tongue',
                'type'          => 13,
                'screen_layout' => 'square',
                'no_of_content' => 20,
                'sortable'      => $sort++,
            ]),

            // ── 9. Top Podcasts ──────────────────────────────────────────
            array_merge($base, [
                'title'           => 'Top Podcasts',
                'sub_title'       => 'Most-listened shows this month',
                'type'            => 2,
                'screen_layout'   => 'square',
                'order_by_play'   => 1,
                'time_window_days'=> 30,
                'no_of_content'   => 15,
                'sortable'        => $sort++,
            ]),

            // ── 10. Featured Artists ─────────────────────────────────────
            array_merge($base, [
                'title'           => 'Featured Artists',
                'sub_title'       => 'Discover voices behind the music',
                'type'            => 4,
                'screen_layout'   => 'round',
                'order_by_upload' => 1,
                'order_by_play'   => 0,
                'no_of_content'   => 12,
                'sortable'        => $sort++,
            ]),

            // ── 11. Browse by Mood ───────────────────────────────────────
            array_merge($base, [
                'title'         => 'Browse by Mood',
                'sub_title'     => 'Pick a vibe',
                'type'          => 5,
                'screen_layout' => 'small_square',
                'no_of_content' => 12,
                'view_all'      => 0,
                'sortable'      => $sort++,
            ]),

            // ── 12. Browse by Language ───────────────────────────────────
            array_merge($base, [
                'title'         => 'Browse by Language',
                'sub_title'     => 'Music from every culture',
                'type'          => 6,
                'screen_layout' => 'round',
                'no_of_content' => 10,
                'view_all'      => 0,
                'sortable'      => $sort++,
            ]),
        ];

        DB::table('tbl_section')->insert($sections);
    }

    public function down()
    {
        DB::table('tbl_section')
            ->whereIn('title', [
                'Continue Listening', 'Trending This Week', 'New Releases',
                'Based on Your Taste', 'Hidden Gems', 'From Artists You Follow',
                'Liked Songs', 'New in Your Language', 'Top Podcasts',
                'Featured Artists', 'Browse by Mood', 'Browse by Language',
            ])
            ->where('section_type', 1)
            ->delete();
    }
}
