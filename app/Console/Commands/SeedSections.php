<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSections extends Command
{
    protected $signature = 'seed:sections';
    protected $description = 'Insert default home page sections for music content';

    public function handle()
    {
        $sections = [
            [
                'user_id' => 0,
                'section_type' => 8,
                'title' => 'Latest Music',
                'sub_title' => 'Newest releases',
                'type' => 8,
                'artist_id' => 0,
                'category_id' => 0,
                'language_id' => 0,
                'city_id' => 0,
                'screen_layout' => 'landscape',
                'is_premium' => 2,
                'order_by_upload' => 1,
                'order_by_play' => 0,
                'is_paid' => 0,
                'is_title' => 1,
                'is_category' => 0,
                'is_artist_name' => 1,
                'no_of_content' => 20,
                'view_all' => 1,
                'sortable' => 1,
                'status' => 1,
            ],
            [
                'user_id' => 0,
                'section_type' => 8,
                'title' => 'Most Played',
                'sub_title' => 'Trending now',
                'type' => 8,
                'artist_id' => 0,
                'category_id' => 0,
                'language_id' => 0,
                'city_id' => 0,
                'screen_layout' => 'landscape',
                'is_premium' => 2,
                'order_by_upload' => 0,
                'order_by_play' => 1,
                'is_paid' => 0,
                'is_title' => 1,
                'is_category' => 0,
                'is_artist_name' => 1,
                'no_of_content' => 20,
                'view_all' => 1,
                'sortable' => 2,
                'status' => 1,
            ],
            [
                'user_id' => 0,
                'section_type' => 8,
                'title' => 'Artists',
                'sub_title' => 'Featured artists',
                'type' => 4,
                'artist_id' => 0,
                'category_id' => 0,
                'language_id' => 0,
                'city_id' => 0,
                'screen_layout' => 'round',
                'is_premium' => 2,
                'order_by_upload' => 1,
                'order_by_play' => 0,
                'is_paid' => 0,
                'is_title' => 1,
                'is_category' => 0,
                'is_artist_name' => 1,
                'no_of_content' => 20,
                'view_all' => 1,
                'sortable' => 3,
                'status' => 1,
            ],
            [
                'user_id' => 0,
                'section_type' => 8,
                'title' => 'Categories',
                'sub_title' => 'Browse by category',
                'type' => 5,
                'artist_id' => 0,
                'category_id' => 0,
                'language_id' => 0,
                'city_id' => 0,
                'screen_layout' => 'square',
                'is_premium' => 2,
                'order_by_upload' => 1,
                'order_by_play' => 0,
                'is_paid' => 0,
                'is_title' => 1,
                'is_category' => 0,
                'is_artist_name' => 0,
                'no_of_content' => 20,
                'view_all' => 1,
                'sortable' => 4,
                'status' => 1,
            ],
        ];

        $count = 0;
        foreach ($sections as $section) {
            DB::table('tbl_section')->updateOrInsert(
                [
                    'section_type' => $section['section_type'],
                    'title' => $section['title'],
                ],
                $section
            );
            $count++;
        }

        $this->info("$count sections seeded!");
    }
}
