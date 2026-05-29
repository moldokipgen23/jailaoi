<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Exception;

class FaceBookAdsSettingController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $params['result'] = Setting_Data();
            if ($params['result']) {
                return view('admin.facebook_ads.index', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function facebookadStatus(Request $request)
    {
        try {

            $data = $request->all();
            $data['facebook_ads_status'] = $data['facebook_ads_status'] ?? 0;

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting['id'])) {
                    $setting['value'] = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function facebookadAndroid(Request $request)
    {
        try {

            $data = $request->all();
            $data['fb_native_id'] = $data['fb_native_id'] ?? '';
            $data['fb_banner_id'] = $data['fb_banner_id'] ?? '';
            $data['fb_interstiatial_id'] = $data['fb_interstiatial_id'] ?? '';
            $data['fb_rewardvideo_id'] = $data['fb_rewardvideo_id'] ?? '';
            $data['fb_native_full_id'] = $data['fb_native_full_id'] ?? '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting['id'])) {
                    $setting['value'] = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function facebookadIos(Request $request)
    {
        try {

            $data = $request->all();
            $data['fb_ios_native_id'] = $data['fb_ios_native_id'] ?? '';
            $data['fb_ios_banner_id'] = $data['fb_ios_banner_id'] ?? '';
            $data['fb_ios_interstiatial_id'] = $data['fb_ios_interstiatial_id'] ?? '';
            $data['fb_ios_rewardvideo_id'] = $data['fb_ios_rewardvideo_id'] ?? '';
            $data['fb_ios_native_full_id'] = $data['fb_ios_native_full_id'] ?? '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting['id'])) {
                    $setting['value'] = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
