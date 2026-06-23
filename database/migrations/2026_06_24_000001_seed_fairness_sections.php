<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedFairnessSections extends Migration
{
    public function up()
    {
        $now = now();

        $base = [
            'user_id'         => 0,
            'artist_id'       => 0,
            'category_id'     => 0,
            'language_id'     => 0,
            'city_id'         => 0,
            'is_premium'      => 0,
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
            'status'          => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        $sections = [
            array_merge($base, [
                'title'           => 'Trending This Week',
                'sub_title'       => 'What everyone is playing right now',
                'type'            => 8,
                'order_by_play'   => 1,
                'time_window_days'=> 7,
            ]),
            array_merge($base, [
                'title'           => 'New Releases',
                'sub_title'       => 'Fresh music just added',
                'type'            => 8,
                'order_by_upload' => 1,
            ]),
            array_merge($base, [
                'title'           => 'Hidden Gems',
                'sub_title'       => "Rising tracks you haven't heard yet",
                'type'            => 14,
            ]),
        ];

        foreach ($sections as $section) {
            $exists = DB::table('tbl_section')
                ->where('title', $section['title'])
                ->where('section_type', 1)
                ->where('user_id', 0)
                ->exists();
            if (!$exists) {
                DB::table('tbl_section')->insert($section);
            }
        }
    }

    public function down()
    {
        DB::table('tbl_section')
            ->whereIn('title', ['Trending This Week', 'New Releases', 'Hidden Gems'])
            ->delete();
    }
}
