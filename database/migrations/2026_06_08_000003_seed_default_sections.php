<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if sections already exist (don't double-seed)
        $existing = DB::table('tbl_section')->count();
        if ($existing > 0) {
            // Still allow re-seeding with title prefix tag to avoid dups
        }

        $now = now();

        // Helper to look up category id by name (case-insensitive). Returns 0 if not found.
        $catId = function (array $names) {
            foreach ($names as $name) {
                $row = DB::table('tbl_category')->whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
                if ($row) return (int) $row->id;
            }
            return 0;
        };

        $gospelId = $catId(['gospel', 'worship', 'spiritual', 'devotional']);
        $loveId   = $catId(['love', 'romance', 'romantic']);

        // Common field defaults
        $base = [
            'user_id' => 0,
            'artist_id' => 0,
            'category_id' => 0,
            'language_id' => 0,
            'city_id' => 0,
            'is_premium' => 0,
            'is_paid' => 0,
            'is_title' => 1,
            'is_category' => 1,
            'is_artist_name' => 1,
            'order_by_upload' => 1,
            'order_by_play' => 1,
            'time_window_days' => 0,
            'view_all' => 1,
            'no_of_content' => 10,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Section TYPE codes:
        //   1=Song, 2=Podcast, 4=Artist, 5=Category, 6=Language, 7=City, 8=Music
        // section_type codes (which tab):
        //   1=Home, 2=Music, 3=Radio, 4=Podcast

        $sections = [];

        /* ============================================================
         * HOME TAB (section_type = 1)
         * ============================================================ */
        $homeSortable = 1;

        $sections[] = array_merge($base, [
            'title' => 'Top Hits This Week',
            'sub_title' => 'Most played in the last 7 days',
            'section_type' => 1,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 7,
            'no_of_content' => 10,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Featured Artists',
            'sub_title' => 'Discover voices behind the music',
            'section_type' => 1,
            'type' => 4,                 // Artist
            'screen_layout' => 'round',
            'order_by_upload' => 1,
            'order_by_play' => 0,
            'no_of_content' => 8,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Trending Now',
            'sub_title' => 'Hot tracks people cannot stop playing',
            'section_type' => 1,
            'type' => 8,                 // Music
            'screen_layout' => 'landscape',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 30,
            'no_of_content' => 5,
            'view_all' => 0,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'New Releases',
            'sub_title' => 'Freshest uploads',
            'section_type' => 1,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 1,
            'order_by_play' => 0,
            'no_of_content' => 12,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Browse by Mood',
            'sub_title' => 'Pick a vibe',
            'section_type' => 1,
            'type' => 5,                 // Category
            'screen_layout' => 'small_square',
            'order_by_upload' => 1,
            'order_by_play' => 0,
            'no_of_content' => 8,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Top Podcasts',
            'sub_title' => 'Conversations and stories',
            'section_type' => 1,
            'type' => 2,                 // Podcast
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 30,
            'no_of_content' => 8,
            'sortable' => $homeSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Sing in Your Language',
            'sub_title' => 'Music by language',
            'section_type' => 1,
            'type' => 6,                 // Language
            'screen_layout' => 'round',
            'no_of_content' => 8,
            'sortable' => $homeSortable++,
        ]);

        /* ============================================================
         * MUSIC TAB (section_type = 2)
         * ============================================================ */
        $musicSortable = 1;

        $sections[] = array_merge($base, [
            'title' => 'Hot Right Now',
            'sub_title' => 'This week\'s top plays',
            'section_type' => 2,
            'type' => 8,                 // Music
            'screen_layout' => 'landscape',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 7,
            'no_of_content' => 5,
            'view_all' => 0,
            'sortable' => $musicSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Top 50 This Month',
            'sub_title' => 'Most played in 30 days',
            'section_type' => 2,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 30,
            'no_of_content' => 50,
            'sortable' => $musicSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Artists You Should Know',
            'sub_title' => 'Talent worth following',
            'section_type' => 2,
            'type' => 4,                 // Artist
            'screen_layout' => 'round',
            'no_of_content' => 10,
            'sortable' => $musicSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Fresh Drops',
            'sub_title' => 'Just uploaded',
            'section_type' => 2,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 1,
            'order_by_play' => 0,
            'no_of_content' => 20,
            'sortable' => $musicSortable++,
        ]);

        if ($gospelId > 0) {
            $sections[] = array_merge($base, [
                'title' => 'Gospel Hits',
                'sub_title' => 'Worship and praise',
                'section_type' => 2,
                'type' => 8,
                'category_id' => $gospelId,
                'screen_layout' => 'square',
                'order_by_upload' => 0,
                'order_by_play' => 1,
                'time_window_days' => 30,
                'no_of_content' => 15,
                'sortable' => $musicSortable++,
            ]);
        }

        if ($loveId > 0) {
            $sections[] = array_merge($base, [
                'title' => 'Love Songs',
                'sub_title' => 'For the feels',
                'section_type' => 2,
                'type' => 8,
                'category_id' => $loveId,
                'screen_layout' => 'square',
                'order_by_upload' => 0,
                'order_by_play' => 1,
                'no_of_content' => 15,
                'sortable' => $musicSortable++,
            ]);
        }

        $sections[] = array_merge($base, [
            'title' => 'Browse Categories',
            'sub_title' => 'All genres',
            'section_type' => 2,
            'type' => 5,                 // Category
            'screen_layout' => 'small_square',
            'no_of_content' => 12,
            'sortable' => $musicSortable++,
        ]);

        /* ============================================================
         * RADIO TAB (section_type = 3)
         * ============================================================ */
        $radioSortable = 1;

        $sections[] = array_merge($base, [
            'title' => 'This Week\'s Hits',
            'sub_title' => 'Top played in 7 days',
            'section_type' => 3,
            'type' => 8,                 // Music
            'screen_layout' => 'landscape',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 7,
            'no_of_content' => 8,
            'view_all' => 0,
            'sortable' => $radioSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Top Hits This Month',
            'sub_title' => 'Most played in 30 days',
            'section_type' => 3,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 30,
            'no_of_content' => 20,
            'sortable' => $radioSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Top Artists This Week',
            'sub_title' => 'Trending voices',
            'section_type' => 3,
            'type' => 4,                 // Artist
            'screen_layout' => 'round',
            'no_of_content' => 10,
            'sortable' => $radioSortable++,
        ]);

        if ($gospelId > 0) {
            $sections[] = array_merge($base, [
                'title' => 'Gospel Point',
                'sub_title' => 'Worship station',
                'section_type' => 3,
                'type' => 8,
                'category_id' => $gospelId,
                'screen_layout' => 'landscape',
                'order_by_upload' => 0,
                'order_by_play' => 1,
                'time_window_days' => 90,
                'no_of_content' => 15,
                'sortable' => $radioSortable++,
            ]);
        }

        if ($loveId > 0) {
            $sections[] = array_merge($base, [
                'title' => 'Love Song Point',
                'sub_title' => 'For the heart',
                'section_type' => 3,
                'type' => 8,
                'category_id' => $loveId,
                'screen_layout' => 'landscape',
                'order_by_upload' => 0,
                'order_by_play' => 1,
                'no_of_content' => 15,
                'sortable' => $radioSortable++,
            ]);
        }

        $sections[] = array_merge($base, [
            'title' => 'All-Time Favorites',
            'sub_title' => 'Most played ever',
            'section_type' => 3,
            'type' => 8,                 // Music
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 0,
            'no_of_content' => 25,
            'sortable' => $radioSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Stations by Language',
            'sub_title' => 'Pick your language',
            'section_type' => 3,
            'type' => 6,                 // Language
            'screen_layout' => 'round',
            'no_of_content' => 8,
            'sortable' => $radioSortable++,
        ]);

        /* ============================================================
         * PODCAST TAB (section_type = 4)
         * ============================================================ */
        $podcastSortable = 1;

        $sections[] = array_merge($base, [
            'title' => 'Featured Podcasts',
            'sub_title' => 'Hand-picked shows',
            'section_type' => 4,
            'type' => 2,                 // Podcast
            'screen_layout' => 'landscape',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 30,
            'no_of_content' => 5,
            'view_all' => 0,
            'sortable' => $podcastSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Trending Podcasts',
            'sub_title' => 'Top played this week',
            'section_type' => 4,
            'type' => 2,                 // Podcast
            'screen_layout' => 'square',
            'order_by_upload' => 0,
            'order_by_play' => 1,
            'time_window_days' => 7,
            'no_of_content' => 10,
            'sortable' => $podcastSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'New Episodes',
            'sub_title' => 'Latest uploads',
            'section_type' => 4,
            'type' => 2,
            'screen_layout' => 'square',
            'order_by_upload' => 1,
            'order_by_play' => 0,
            'no_of_content' => 15,
            'sortable' => $podcastSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Podcast Hosts',
            'sub_title' => 'Voices to follow',
            'section_type' => 4,
            'type' => 4,                 // Artist
            'screen_layout' => 'round',
            'no_of_content' => 8,
            'sortable' => $podcastSortable++,
        ]);

        $sections[] = array_merge($base, [
            'title' => 'Browse Topics',
            'sub_title' => 'All categories',
            'section_type' => 4,
            'type' => 5,
            'screen_layout' => 'small_square',
            'no_of_content' => 8,
            'sortable' => $podcastSortable++,
        ]);

        // Insert all sections. If a section with the same title + section_type already exists, skip it.
        foreach ($sections as $row) {
            $exists = DB::table('tbl_section')
                ->where('title', $row['title'])
                ->where('section_type', $row['section_type'])
                ->where('user_id', 0)
                ->exists();
            if (!$exists) {
                DB::table('tbl_section')->insert($row);
            }
        }
    }

    public function down(): void
    {
        // Don't auto-delete on rollback — these are user content once they exist.
    }
};
