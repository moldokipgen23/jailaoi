<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\Onboarding_Screen;
use App\Models\Smtp_Setting;
use App\Models\Social_Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Mail;

class AppSettingController extends Controller
{
    private $folder = "setting";
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

                $params['result']['app_logo'] = $this->common->getImage($this->folder, $params['result']['app_logo'], $params['result']['app_logo_storage_type']);

                $params['social_link'] = Social_Link::get();
                $this->common->imageNameToUrl($params['social_link'], 'image', $this->folder);

                $params['onboarding_screen'] = Onboarding_Screen::get();
                $this->common->imageNameToUrl($params['onboarding_screen'], 'image', $this->folder);

                $params['smtp'] = Smtp_Setting::latest()->first();

                if (Demo_Mode() == 0) {
                    $params['smtp']['protocol'] = "xxxxxx";
                    $params['smtp']['host'] = "xxxx.xxxx.xxxx";
                    $params['smtp']['port'] = "xxx";
                    $params['smtp']['user'] = "xxxxxx.xxxx@xxx.xxx";
                    $params['smtp']['pass'] = "xxxxxxxx";
                    $params['smtp']['from_name'] = "xxxxxx";
                    $params['smtp']['from_email'] = "xxxxxx.xxxx@xxx.xxx";

                    $params['result']['live_appid'] = "xxxxxxxxxx";
                    $params['result']['live_appsign'] = "xxxxxxxxxx";
                    $params['result']['live_serversecret'] = "xxxxxxxxxx";

                    $params['result']['deepar_android_key'] = "xxxxxxxxxxxxxxxxxxxx";
                    $params['result']['deepar_ios_key'] = "xxxxxxxxxxxxxxxxxxxx";
                    $params['result']['vap_id_key'] = "xxxxxxxxxxxxxxxxxxxx";
                }

                return view('admin.app_setting.index', $params);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function app(Request $request)
    {
        try {

            $data = $request->all();
            $data['app_name'] = $data['app_name'] ?? '';
            $data['app_version'] = $data['app_version'] ?? '';
            $data['email'] = $data['email'] ?? '';
            $data['author'] = $data['author'] ?? '';
            $data['contact'] = $data['contact'] ?? '';
            $data['website'] = $data['website'] ?? '';
            $data['app_desripation'] = $data['app_desripation'] ?? '';
            if (isset($data['app_logo'])) {
                $files = $data['app_logo'];
                $data['app_logo_storage_type'] = Storage_Type();
                $data['app_logo'] = $this->common->saveImage($files, $this->folder, 'logo_', $data['app_logo_storage_type']);
                $this->common->deleteImageToFolder($this->folder, basename($data['old_app_logo']), $data['old_app_logo_storage_type']);
            }

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
    public function currency(Request $request)
    {
        try {

            $data = $request->all();
            $data['currency'] = strtoupper($data['currency']) ?? '';
            $data['currency_code'] = $data['currency_code'] ?? '';

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
    public function vapIdKey(Request $request)
    {
        try {

            $data = $request->all();
            $data['vap_id_key'] = $data['vap_id_key'] ?? '';

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
    public function smtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required',
                'host' => 'required',
                'port' => 'required',
                'protocol' => 'required',
                'user' => 'required',
                'pass' => 'required',
                'from_name' => 'required',
                'from_email' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            if (isset($request['id']) && $request['id'] != null && $request['id'] != "") {

                $smtp = Smtp_Setting::where('id', $request['id'])->first();
                if (isset($smtp['id'])) {

                    $smtp['protocol'] = $request['protocol'];
                    $smtp['host'] = $request['host'];
                    $smtp['port'] = $request['port'];
                    $smtp['user'] = $request['user'];
                    $smtp['pass'] = $request['pass'];
                    $smtp['from_name'] = $request['from_name'];
                    $smtp['from_email'] = $request['from_email'];
                    $smtp['status'] = $request['status'];
                    if ($smtp->save()) {
                        return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
                    } else {
                        return response()->json(['status' => 400, 'errors' => __('label.data_not_updated')]);
                    }
                }
            } else {

                $insert = new Smtp_Setting();
                $smtp['protocol'] = $request['protocol'];
                $smtp['host'] = $request['host'];
                $smtp['port'] = $request['port'];
                $smtp['user'] = $request['user'];
                $smtp['pass'] = $request['pass'];
                $smtp['from_name'] = $request['from_name'];
                $smtp['from_email'] = $request['from_email'];
                $smtp['status'] = $request['status'];
                if ($insert->save()) {

                    $this->common->SetSmtpConfig();
                    return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.data_not_updated')]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function sociallink(Request $request)
    {
        try {

            $arr_name = $request['name'];
            $arr_url = $request['url'];
            $arr_img = $request->file('image');
            $arr_old_image = $request['old_image'];
            $arr_old_storage_type = $request['old_storage_type'];

            // Save New All Link
            $not_delete_img = array();
            $not_delete_ids = array();
            $storage_type = Storage_Type();

            for ($i = 0; $i < count($arr_name); $i++) {

                if (!empty($arr_name[$i]) && !empty($arr_url[$i])) {

                    if (!empty($arr_img[$i])) {

                        $insert = new Social_Link();
                        $insert['name'] = $arr_name[$i];
                        $insert['url'] = $arr_url[$i];
                        $insert['storage_type'] = $storage_type;
                        $insert['image'] = $this->common->saveImage($arr_img[$i], $this->folder, 'soc_link_', $storage_type);
                        $insert->save();

                        $this->common->deleteImageToFolder($this->folder, $arr_old_image[$i], $arr_old_storage_type[$i]);
                    } else {
                        if (!empty($arr_old_image[$i])) {

                            $insert = new Social_Link();
                            $insert['name'] = $arr_name[$i];
                            $insert['url'] = $arr_url[$i];
                            $insert['storage_type'] = $arr_old_storage_type[$i];
                            $insert['image'] = $arr_old_image[$i];
                            $insert->save();
                            $not_delete_img[] = $arr_old_image[$i];
                        }
                    }
                    $not_delete_ids[] = $insert->id;
                }
            }

            // Delete Old All Link 
            $all_old_link = Social_Link::whereNotIn('id', $not_delete_ids)->get();
            for ($i = 0; $i < count($all_old_link); $i++) {

                if (!in_array($all_old_link[$i]['image'], $not_delete_img)) {
                    $this->common->deleteImageToFolder($this->folder, $all_old_link[$i]['image'], $all_old_link[$i]['storage_type']);
                }

                $all_old_link[$i]->delete();
            }

            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function onboardingscreen(Request $request)
    {
        try {

            $arr_title = $request['title'];
            $arr_description = $request['description'];
            $arr_img = $request->file('image');
            $arr_old_image = $request['old_image'];
            $arr_old_storage_type = $request['old_storage_type'];

            // Save New All Link
            $not_delete_img = array();
            $not_delete_ids = array();
            $storage_type = Storage_Type();

            for ($i = 0; $i < count($arr_title); $i++) {

                if (!empty($arr_title[$i]) && !empty($arr_description[$i])) {

                    if (!empty($arr_img[$i])) {

                        $insert = new Onboarding_Screen();
                        $insert['title'] = $arr_title[$i];
                        $insert['description'] = $arr_description[$i];
                        $insert['storage_type'] = $storage_type;
                        $insert['image'] = $this->common->saveImage($arr_img[$i], $this->folder, 'on_board_', $storage_type);
                        $insert->save();

                        $this->common->deleteImageToFolder($this->folder, $arr_old_image[$i], $arr_old_storage_type[$i]);
                    } else {
                        if (!empty($arr_old_image[$i])) {

                            $insert = new Onboarding_Screen();
                            $insert['title'] = $arr_title[$i];
                            $insert['description'] = $arr_description[$i];
                            $insert['storage_type'] = $arr_old_storage_type[$i];
                            $insert['image'] = $arr_old_image[$i];
                            $insert->save();
                            $not_delete_img[] = $arr_old_image[$i];
                        }
                    }
                    $not_delete_ids[] = $insert->id;
                }
            }

            // Delete Old Data
            $all_old_data = Onboarding_Screen::whereNotIn('id', $not_delete_ids)->get();
            for ($i = 0; $i < count($all_old_data); $i++) {

                if (!in_array($all_old_data[$i]['image'], $not_delete_img)) {
                    $this->common->deleteImageToFolder($this->folder, $all_old_data[$i]['image'], $all_old_data[$i]['storage_type']);
                }
                $all_old_data[$i]->delete();
            }

            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function livestreaming(Request $request)
    {
        try {

            $data = $request->all();
            $data['live_appid'] = $data['live_appid'] ?? '';
            $data['live_appsign'] = $data['live_appsign'] ?? '';
            $data['live_serversecret'] = $data['live_serversecret'] ?? '';
            $data['is_live_streaming_fake'] = $data['is_live_streaming_fake'] ?? 0;

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
    public function deepar(Request $request)
    {
        try {

            $data = $request->all();
            $data['deepar_android_key'] = $data['deepar_android_key'] ?? '';
            $data['deepar_ios_key'] = $data['deepar_ios_key'] ?? '';

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
    public function adscommission(Request $request)
    {
        try {

            $data = $request->all();
            $data['ads_commission'] = $data['ads_commission'] ?? 0;

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
    public function rentcommission(Request $request)
    {
        try {

            $data = $request->all();
            $data['rent_commission'] = $data['rent_commission'] ?? 0;

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
    public function referearn(Request $request)
    {
        try {

            $data = $request->all();
            $data['refer_and_earn_status'] = $data['refer_and_earn_status'] ?? 0;
            $data['parent_user_earn'] = $data['parent_user_earn'] ?? 0;
            $data['child_user_earn'] = $data['child_user_earn'] ?? 0;

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
    public function emailtest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $this->common->SetSmtpConfig();
            $email = $request['email'];

            $title = "SMTP Test Email from " . App_Name();
            $body = "This is a test email to confirm that your SMTP configuration is working correctly for the " . App_Name() . " application.";
            $view = 'mail.test';

            $details = [
                'title' => $title,
                'body' => $body
            ];

            // Send Mail
            try {
                Mail::to($email)->send(new \App\Mail\mail($details, $view));
                return response()->json(['status' => 200, 'success' => __('label.mail_send_successfully')]);
            } catch (Exception $e) {
                return response()->json(['status' => 400, 'errors' => __('label.mail_not_send')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function appdownload(Request $request)
    {
        try {

            $data = $request->all();
            $data['playstore_id'] = $data['playstore_id'] ?? "";
            $data['appstore_id'] = $data['appstore_id'] ?? "";

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
