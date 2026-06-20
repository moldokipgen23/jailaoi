<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Only insert if the page has no global sections yet (user_id=0)
        $musicCount  = DB::table('tbl_section')->where('section_type', 2)->where('user_id', 0)->count();
        $radioCount  = DB::table('tbl_section')->where('section_type', 3)->where('user_id', 0)->count();
        $podcastCount = DB::table('tbl_section')->where('section_type', 4)->where('user_id', 0)->count();

        // ── Music page (section_type=2, type=8) ──────────────────────────────
        if ($musicCount === 0) {
            DB::table('tbl_section')->insert([
                [
                    'user_id'        => 0,
                    'section_type'   => 2,
                    'title'          => 'New Releases',
                    'sub_title'      => 'Fresh music just dropped',
                    'type'           => 8,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'landscape',
                    'is_premium'     => 0,
                    'order_by_upload'=> 1,
                    'order_by_play'  => 0,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 10,
                    'view_all'       => 1,
                    'sortable'       => 1,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'user_id'        => 0,
                    'section_type'   => 2,
                    'title'          => 'Popular This Week',
                    'sub_title'      => 'Most played in the last 7 days',
                    'type'           => 8,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'square',
                    'is_premium'     => 0,
                    'order_by_upload'=> 0,
                    'order_by_play'  => 1,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 12,
                    'view_all'       => 1,
                    'sortable'       => 2,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'user_id'        => 0,
                    'section_type'   => 2,
                    'title'          => 'Top Songs',
                    'sub_title'      => 'All time favourites',
                    'type'           => 8,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'small_square',
                    'is_premium'     => 0,
                    'order_by_upload'=> 0,
                    'order_by_play'  => 1,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 15,
                    'view_all'       => 1,
                    'sortable'       => 3,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }

        // ── Radio page (section_type=3, type=1) ──────────────────────────────
        if ($radioCount === 0) {
            DB::table('tbl_section')->insert([
                [
                    'user_id'        => 0,
                    'section_type'   => 3,
                    'title'          => 'Popular Stations',
                    'sub_title'      => 'Most tuned in right now',
                    'type'           => 1,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'landscape',
                    'is_premium'     => 0,
                    'order_by_upload'=> 0,
                    'order_by_play'  => 1,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 10,
                    'view_all'       => 1,
                    'sortable'       => 1,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'user_id'        => 0,
                    'section_type'   => 3,
                    'title'          => 'New Stations',
                    'sub_title'      => 'Recently added stations',
                    'type'           => 1,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'square',
                    'is_premium'     => 0,
                    'order_by_upload'=> 1,
                    'order_by_play'  => 0,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 10,
                    'view_all'       => 1,
                    'sortable'       => 2,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'user_id'        => 0,
                    'section_type'   => 3,
                    'title'          => 'Discover Radio',
                    'sub_title'      => 'Explore all stations',
                    'type'           => 1,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'small_square',
                    'is_premium'     => 0,
                    'order_by_upload'=> 1,
                    'order_by_play'  => 1,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 20,
                    'view_all'       => 1,
                    'sortable'       => 3,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }

        // ── Podcast page (section_type=4, type=2) ────────────────────────────
        if ($podcastCount === 0) {
            DB::table('tbl_section')->insert([
                [
                    'user_id'        => 0,
                    'section_type'   => 4,
                    'title'          => 'Latest Podcasts',
                    'sub_title'      => 'Fresh episodes for you',
                    'type'           => 2,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'landscape',
                    'is_premium'     => 0,
                    'order_by_upload'=> 1,
                    'order_by_play'  => 0,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 10,
                    'view_all'       => 1,
                    'sortable'       => 1,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'user_id'        => 0,
                    'section_type'   => 4,
                    'title'          => 'Popular Podcasts',
                    'sub_title'      => 'Most listened this week',
                    'type'           => 2,
                    'artist_id'      => 0,
                    'category_id'    => 0,
                    'language_id'    => 0,
                    'city_id'        => 0,
                    'screen_layout'  => 'square',
                    'is_premium'     => 0,
                    'order_by_upload'=> 0,
                    'order_by_play'  => 1,
                    'is_paid'        => 0,
                    'is_title'       => 1,
                    'is_category'    => 1,
                    'is_artist_name' => 1,
                    'no_of_content'  => 10,
                    'view_all'       => 1,
                    'sortable'       => 2,
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_section')
            ->where('user_id', 0)
            ->whereIn('section_type', [2, 3, 4])
            ->whereIn('title', [
                'New Releases', 'Popular This Week', 'Top Songs',
                'Popular Stations', 'New Stations', 'Discover Radio',
                'Latest Podcasts', 'Popular Podcasts',
            ])
            ->delete();
    }
};
