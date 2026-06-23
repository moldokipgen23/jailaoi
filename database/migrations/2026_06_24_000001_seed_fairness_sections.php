<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedFairnessSections extends Migration
{
    public function up()
    {
        $now = now();

        // Only insert if not already present (idempotent)
        $existing = DB::table('tbl_section')->pluck('title')->map(fn($t) => strtolower($t))->toArray();

        $sections = [
            [
                'title'           => 'Trending This Week',
                'sub_title'       => 'What everyone is playing right now',
                'type'            => 8,
                'order_by_play'   => 1,
                'order_by_upload' => 0,
                'time_window_days'=> 7,
                'no_of_content'   => 20,
                'screen_layout'   => 'landscape',
                'status'          => 1,
            ],
            [
                'title'           => 'New Releases',
                'sub_title'       => 'Fresh music just added',
                'type'            => 8,
                'order_by_play'   => 0,
                'order_by_upload' => 1,
                'time_window_days'=> 0,
                'no_of_content'   => 20,
                'screen_layout'   => 'landscape',
                'status'          => 1,
            ],
            [
                'title'           => 'Hidden Gems',
                'sub_title'       => 'Rising tracks you haven\'t heard yet',
                'type'            => 14,
                'order_by_play'   => 0,
                'order_by_upload' => 0,
                'time_window_days'=> 0,
                'no_of_content'   => 20,
                'screen_layout'   => 'landscape',
                'status'          => 1,
            ],
        ];

        foreach ($sections as $section) {
            if (!in_array(strtolower($section['title']), $existing)) {
                $section['created_at'] = $now;
                $section['updated_at'] = $now;
                DB::table('tbl_section')->insert($section);
            }
        }
    }

    public function down()
    {
        DB::table('tbl_section')->whereIn('title', ['Trending This Week', 'New Releases', 'Hidden Gems'])->delete();
    }
}
