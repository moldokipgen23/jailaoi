<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Exception;

class AdmobSettingController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $data = Setting_Data();
            if ($data) {
                return view('admin.admob.index', ['result' => $data]);
            } else {
                return redirect()->back()->with('error', __('label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function admobAndroid(Request $request)
    {
        try {

            $data = $request->only(['banner_ad', 'banner_adid', 'interstital_ad', 'interstital_adid', 'interstital_adclick', 'interstital_cooldown', 'reward_ad', 'reward_adid', 'reward_adclick']);
            $data["banner_adid"] = isset($data['banner_adid']) ? $data['banner_adid'] : '';
            $data["interstital_adid"] = isset($data['interstital_adid']) ? $data['interstital_adid'] : '';
            $data["reward_adid"] = isset($data['reward_adid']) ? $data['reward_adid'] : '';
            $data["interstital_adclick"] = isset($data['interstital_adclick']) ? $data['interstital_adclick'] : '';
            $data["interstital_cooldown"] = isset($data['interstital_cooldown']) ? $data['interstital_cooldown'] : '60';
            $data["reward_adclick"] = isset($data['reward_adclick']) ? $data['reward_adclick'] : '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting->id)) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function startioAndroid(Request $request)
    {
        try {
            $keys = [
                'startio_enabled', 'startio_banner_enabled',
                'startio_interstitial_enabled', 'startio_rewarded_enabled',
                'startio_app_id_android', 'startio_interstitial_cooldown',
            ];
            foreach ($keys as $key) {
                $value = $request->input($key, '0');
                $setting = General_Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function startioIos(Request $request)
    {
        try {
            $keys = [
                'ios_startio_enabled', 'ios_startio_banner_enabled',
                'ios_startio_interstitial_enabled', 'ios_startio_rewarded_enabled',
                'startio_app_id_ios', 'ios_startio_interstitial_cooldown',
            ];
            foreach ($keys as $key) {
                $value = $request->input($key, '0');
                $setting = General_Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function metaAndroid(Request $request)
    {
        try {
            $keys = [
                'meta_status', 'meta_banner_enabled', 'meta_interstitial_enabled',
                'meta_rewarded_enabled', 'meta_interstitial_cooldown',
                'meta_placement_id_interstitial', 'meta_placement_id_banner',
                'meta_placement_id_rewarded',
            ];
            foreach ($keys as $key) {
                $default = $key === 'meta_interstitial_cooldown' ? '60' : '0';
                $value = $request->input($key, $default);
                $setting = General_Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function metaIos(Request $request)
    {
        try {
            $keys = [
                'ios_meta_status', 'ios_meta_banner_enabled', 'ios_meta_interstitial_enabled',
                'ios_meta_rewarded_enabled', 'ios_meta_interstitial_cooldown',
                'ios_meta_placement_id_interstitial', 'ios_meta_placement_id_banner',
                'ios_meta_placement_id_rewarded',
            ];
            foreach ($keys as $key) {
                $default = $key === 'ios_meta_interstitial_cooldown' ? '60' : '0';
                $value = $request->input($key, $default);
                $setting = General_Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function admobIos(Request $request)
    {
        try {

            $data = $request->only(['ios_banner_ad', 'ios_banner_adid', 'ios_interstital_ad', 'ios_interstital_adid', 'ios_interstital_adclick', 'ios_interstital_cooldown', 'ios_reward_ad', 'ios_reward_adid', 'ios_reward_adclick']);
            $data["ios_banner_adid"] = isset($data['ios_banner_adid']) ? $data['ios_banner_adid'] : '';
            $data["ios_interstital_adid"] = isset($data['ios_interstital_adid']) ? $data['ios_interstital_adid'] : '';
            $data["ios_reward_adid"] = isset($data['ios_reward_adid']) ? $data['ios_reward_adid'] : '';
            $data["ios_interstital_adclick"] = isset($data['ios_interstital_adclick']) ? $data['ios_interstital_adclick'] : '';
            $data["ios_interstital_cooldown"] = isset($data['ios_interstital_cooldown']) ? $data['ios_interstital_cooldown'] : '60';
            $data["ios_reward_adclick"] = isset($data['ios_reward_adclick']) ? $data['ios_reward_adclick'] : '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting->id)) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
