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
            'app_name' => 'DTRadio',
            'app_version' => '1.0',
            'app_desripation' => 'Music & Radio Streaming App',
            'app_description' => '',
            'app_logo' => '',
            'app_logo_storage_type' => '0',
            'app_favicon' => '',
            'app_contact' => '',
            'app_email' => '',
            'app_copyright' => '',
            'appstore_id' => '',
            'currency_code' => 'USD',
            'currency' => 'USD',
            'author' => '',
            'contact' => '',
            'email' => '',
            'website' => '',
            'host_email' => '',
            'company_name' => '',
            'company_logo' => '',
            'dev_logo' => '',
            'dev_title' => '',
            'screenshot' => '0',
            'login_page_image' => '',
            'ai_api_key' => '',
            'ai_section' => '0',
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
            'notification_configuration' => '1',
            'refer_and_earn_status' => '0',
            'parent_user_earn' => '0',
            'child_user_earn' => '0',
            'ads_commission' => '0',
            'rent_commission' => '0',
            'video_status' => '1',
            'playstore_id' => '',
            'vap_id_key' => '',
            'deepar_android_key' => '',
            'deepar_ios_key' => '',
        ];

        foreach ($keys as $key => $value) {
            try {
                DB::table('tbl_general_setting')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
            } catch (\Exception $e) {
                $this->error("FAIL $key: " . $e->getMessage());
            }
        }

        $this->info(count($keys) . ' settings seeded!');
    }
}
