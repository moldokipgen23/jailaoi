<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $adKeys = [
            'banner_ad',
            'interstital_ad',
            'reward_ad',
            'ios_banner_ad',
            'ios_interstital_ad',
            'ios_reward_ad',
        ];
        foreach ($adKeys as $key) {
            DB::table('tbl_general_setting')
                ->where('key', $key)
                ->update(['value' => '0']);
        }
    }

    public function down()
    {
        $adKeys = [
            'banner_ad',
            'interstital_ad',
            'reward_ad',
            'ios_banner_ad',
            'ios_interstital_ad',
            'ios_reward_ad',
        ];
        foreach ($adKeys as $key) {
            DB::table('tbl_general_setting')
                ->where('key', $key)
                ->where('value', '0')
                ->update(['value' => '1']);
        }
    }
};
