<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdsSettingController extends Controller
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
                return view('admin.ads_setting.index', ['data' => $data]);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function bannerads(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'banner_ads_status' => 'required',
                'banner_ads_cpv' => 'numeric|min:0',
                'banner_ads_cpc' => 'numeric|min:0',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $data = $request->all();
            $data['banner_ads_status'] = $data['banner_ads_status'] ?? 0;
            $data['banner_ads_cpv'] = $data['banner_ads_cpv'] ?? 0;
            $data['banner_ads_cpc'] = $data['banner_ads_cpc'] ?? 0;

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
    public function interstitalads(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'interstital_ads_status' => 'required',
                'interstital_ads_cpv' => 'numeric|min:0',
                'interstital_ads_cpc' => 'numeric|min:0',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $data = $request->all();
            $data['interstital_ads_status'] = $data['interstital_ads_status'] ?? 0;
            $data['interstital_ads_cpv'] = $data['interstital_ads_cpv'] ?? 0;
            $data['interstital_ads_cpc'] = $data['interstital_ads_cpc'] ?? 0;

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
    public function rewardads(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reward_ads_status' => 'required',
                'reward_ads_cpv' => 'numeric|min:0',
                'reward_ads_cpc' => 'numeric|min:0',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $data = $request->all();
            $data['reward_ads_status'] = $data['reward_ads_status'] ?? 0;
            $data['reward_ads_cpv'] = $data['reward_ads_cpv'] ?? 0;
            $data['reward_ads_cpc'] = $data['reward_ads_cpc'] ?? 0;

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
