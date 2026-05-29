<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedAppSettings extends Command
{
    protected $signature = 'seed:settings';
    protected $description = 'Insert all default app settings into tbl_general_setting';

    public function handle()
    {
        $keys = [
            'app_name' => 'Jailaoi',
            'app_version' => '1.0',
            'app_desripation' => 'Radio Streaming App',
            'app_logo' => '',
            'app_favicon' => '',
            'app_contact' => '',
            'app_email' => '',
            'app_copyright' => '© Jailaoi',
            'currency_code' => 'USD',
            'author' => '',
            'currency' => 'USD',
            'contact' => '',
            'email' => '',
            'website' => '',
            'host_email' => '',
            'banner_ad' => '1',
            'banner_adid' => '',
            'interstital_ad' => '1',
            'interstital_adid' => '',
            'interstital_adclick' => '3',
            'reward_ad' => '1',
            'reward_adid' => '',
            'reward_adclick' => '3',
            'ios_banner_ad' => '1',
            'ios_banner_adid' => '',
            'ios_interstital_ad' => '1',
            'ios_interstital_adid' => '',
            'ios_interstital_adclick' => '3',
            'ios_reward_ad' => '1',
            'ios_reward_adid' => '',
            'ios_reward_adclick' => '3',
            'fb_banner_id' => '',
            'fb_banner_status' => '1',
            'fb_interstiatial_id' => '',
            'fb_interstiatial_status' => '1',
            'fb_interstital_adclick' => '3',
            'fb_ios_banner_id' => '',
            'fb_ios_banner_status' => '1',
            'fb_ios_interstiatial_id' => '',
            'fb_ios_interstiatial_status' => '1',
            'fb_ios_interstital_adclick' => '3',
            'fb_ios_native_full_id' => '',
            'fb_ios_native_full_status' => '1',
            'fb_ios_native_id' => '',
            'fb_ios_native_status' => '1',
            'fb_ios_reward_adclick' => '3',
            'fb_ios_rewardvideo_id' => '',
            'fb_ios_rewardvideo_status' => '1',
            'fb_native_full_id' => '',
            'fb_native_full_status' => '1',
            'fb_native_id' => '',
            'fb_native_status' => '1',
            'fb_reward_adclick' => '3',
            'fb_rewardvideo_id' => '',
            'fb_rewardvideo_status' => '1',
            'onesignal_apid' => '',
            'onesignal_rest_key' => '',
        ];

        foreach ($keys as $key => $value) {
            try {
                DB::table('tbl_general_setting')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
                $this->line("  <info>OK</info>  $key");
            } catch (\Exception $e) {
                $this->error("FAIL $key: " . $e->getMessage());
            }
        }

        $this->info('All settings seeded!');
    }
}
