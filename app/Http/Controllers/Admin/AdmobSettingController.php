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

            $params['result'] = Setting_Data();
            if ($params['result']) {
                return view('admin.admob.index', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function admobStatus(Request $request)
    {
        try {

            $data = $request->all();
            $data['admob_status'] = $data['admob_status'] ?? 0;

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
    public function admobAndroid(Request $request)
    {
        try {

            $data = $request->all();
            $data['banner_adid'] = $data['banner_adid'] ?? '';
            $data['interstital_adid'] = $data['interstital_adid'] ?? '';
            $data['reward_adid'] = $data['reward_adid'] ?? '';
            $data['interstital_adclick'] = $data['interstital_adclick'] ?? '';
            $data['reward_adclick'] = $data['reward_adclick'] ?? '';

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
    public function admobIos(Request $request)
    {
        try {

            $data = $request->all();
            $data['ios_banner_adid'] = $data['ios_banner_adid'] ?? '';
            $data['ios_interstital_adid'] = $data['ios_interstital_adid'] ?? '';
            $data['ios_reward_adid'] = $data['ios_reward_adid'] ?? '';
            $data['ios_interstital_adclick'] = $data['ios_interstital_adclick'] ?? '';
            $data['ios_reward_adclick'] = $data['ios_reward_adclick'] ?? '';

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
