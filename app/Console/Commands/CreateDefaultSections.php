<?php

namespace App\Console\Commands;

use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDefaultSections extends Command
{
    protected $signature = 'section:create-defaults';
    protected $description = 'Create default sections for the home page';

    public function handle()
    {
        $this->info('Seeding default languages...');
        if (Schema::hasTable('tbl_language')) {
            $langCount = DB::table('tbl_language')->count();
            if ($langCount === 0) {
                DB::table('tbl_language')->insert([
                    ['name' => 'English', 'storage_type' => 0, 'image' => '', 'sort_order' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Hindi', 'storage_type' => 0, 'image' => '', 'sort_order' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ]);
                $this->info('Added 2 default languages.');
            } else {
                $this->warn("Languages already exist ($langCount found), skipping.");
            }
        } else {
            $this->error('tbl_language table does not exist. Run php artisan migrate first.');
        }

        $this->info('Creating default sections...');

        $sections = [
            [
                'title' => 'Popular Artists',
                'short_title' => 'Popular Artists',
                'is_home_screen' => 1,
                'content_type' => 7,
                'screen_layout' => 'round',
                'no_of_content' => 10,
                'order_by_upload' => 2,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 1,
                'is_fixed' => 1,
                'status' => 1,
            ],
            [
                'title' => 'New Releases',
                'short_title' => 'New Releases',
                'is_home_screen' => 1,
                'content_type' => 1,
                'screen_layout' => 'list_view',
                'no_of_content' => 10,
                'order_by_upload' => 2,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 2,
                'is_fixed' => 0,
                'status' => 1,
            ],
            [
                'title' => 'Trending Playlists',
                'short_title' => 'Playlists',
                'is_home_screen' => 1,
                'content_type' => 4,
                'screen_layout' => 'playlist',
                'no_of_content' => 10,
                'order_by_upload' => 2,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 3,
                'is_fixed' => 0,
                'status' => 1,
            ],
            [
                'title' => 'Browse Categories',
                'short_title' => 'Categories',
                'is_home_screen' => 1,
                'content_type' => 5,
                'screen_layout' => 'category',
                'no_of_content' => 0,
                'order_by_upload' => 0,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 4,
                'is_fixed' => 0,
                'status' => 1,
            ],
            [
                'title' => 'Radio Stations',
                'short_title' => 'Radio',
                'is_home_screen' => 1,
                'content_type' => 3,
                'screen_layout' => 'round',
                'no_of_content' => 10,
                'order_by_upload' => 2,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 5,
                'is_fixed' => 0,
                'status' => 1,
            ],
            [
                'title' => 'Recommended Music',
                'short_title' => 'Recommended',
                'is_home_screen' => 1,
                'content_type' => 1,
                'screen_layout' => 'square',
                'no_of_content' => 10,
                'order_by_upload' => 2,
                'view_all' => 1,
                'category_id' => 0,
                'language_id' => 0,
                'sort_order' => 6,
                'is_fixed' => 1,
                'status' => 1,
            ],
        ];

        foreach ($sections as $data) {
            $existing = Section::where('title', $data['title'])
                ->where('is_home_screen', 1)
                ->first();

            if ($existing) {
                $existing->update($data);
                $this->warn("Updated: {$data['title']}");
            } else {
                Section::create($data);
                $this->info("Created: {$data['title']}");
            }
        }

        $this->info('Done! ' . count($sections) . ' sections processed.');
    }
}
