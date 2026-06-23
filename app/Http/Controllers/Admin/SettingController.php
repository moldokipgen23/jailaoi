<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\Onboarding_Screen;
use App\Models\Smtp;
use App\Models\Social_Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class SettingController extends Controller
{
    private $folder = "images/app";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $setting = General_Setting::get();
            foreach ($setting as $row) {
                $data[$row->key] = $row->value;
            }
            $data['app_logo'] = $this->common->Get_Image($this->folder, $data['app_logo'] ?? '');
            $data['dev_logo'] = $this->common->Get_Image($this->folder, $data['dev_logo'] ?? '');
            $data['company_logo'] = $this->common->Get_Image($this->folder, $data['company_logo'] ?? '');

            if ($data) {
                $params['result'] = $data;

                $params['smtp'] = Smtp::latest()->first();

                $params['sociallink'] = Social_Link::get();
                $this->common->imageNameToUrl($params['sociallink'], 'image', $this->folder);

                $params['onboarding_screen'] = Onboarding_Screen::get();
                $this->common->imageNameToUrl($params['onboarding_screen'], 'image', $this->folder);

                return view('admin.setting.index', $params);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function app(Request $request)
    {
        try {

            $data = $request->all();
            $data["app_name"] = isset($data['app_name']) ? $data['app_name'] : '';
            $data["host_email"] = isset($data['host_email']) ? $data['host_email'] : '';
            $data["app_version"] = isset($data['app_version']) ? $data['app_version'] : '';
            $data["author"] = isset($data['author']) ? $data['author'] : '';
            $data["email"] = isset($data['email']) ? $data['email'] : '';
            $data["contact"] = isset($data['contact']) ? $data['contact'] : '';
            $data["app_desripation"] = isset($data['app_desripation']) ? $data['app_desripation'] : '';
            $data["website"] = isset($data['website']) ? $data['website'] : '';
            $data["company_name"] = isset($data['company_name']) ? $data['company_name'] : '';

            if (isset($data['app_logo'])) {
                $files = $data['app_logo'];
                $data['app_logo'] = $this->common->saveImage($files, $this->folder, "app_");

                $this->common->deleteImageToFolder($this->folder, $data['old_app_logo']);
            }
            if (isset($data['company_logo'])) {
                $files = $data['company_logo'];
                $data['company_logo'] = $this->common->saveImage($files, $this->folder, "app_");

                $this->common->deleteImageToFolder($this->folder, $data['old_company_logo']);
            }

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
    public function currency(Request $request)
    {
        try {

            $data = $request->all();
            $data["currency"] = isset($data['currency']) ? $data['currency'] : '';
            $data["currency_code"] = isset($data['currency_code']) ? $data['currency_code'] : '';

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
    public function dev(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'dev_title' => 'required',
                    'dev_logo' => 'image|mimes:jpeg,jpg,png|max:2048',
                ]
            );
            if ($validate->fails()) {
                $errs = $validate->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            $data = $request->all();
            $data['dev_title'] = isset($data['dev_title']) ? $data['dev_title'] : '';
            if (isset($data['dev_logo'])) {
                $files = $data['dev_logo'];
                $data['dev_logo'] = $this->common->saveImage($files, $this->folder, 'app_');
                $this->common->deleteImageToFolder($this->folder, $data['old_dev_logo']);
            }
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
    public function saveBannerSetting(Request $request)
    {
        try {
            $value = $request->input('home_banner_enabled', '0');
            $setting = General_Setting::where('key', 'home_banner_enabled')->first();
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function screenshot(Request $request)
    {
        try {
            $data = $request->all();
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
    public function save_key(Request $request)
    {
        try {
            $data = $request->all();
            $data["ai_api_key"] = isset($data['ai_api_key']) ? $data['ai_api_key'] : '';
            $data["ai_section"] = isset($data['ai_section']) ? $data['ai_section'] : 0;
            $data["ai_section_count"] = max(1, (int) ($data['ai_section_count'] ?? 2));
            if ($data['ai_section'] == 0) {
                $data['ai_api_key'] = '';
            }
            foreach ($data as $key => $value) {
                if (is_null($value)) continue; // JAILAOI: skip null — tbl_general_setting.value NOT NULL
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
    public function smtpSave(Request $request)
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

            if (isset($request->id) && $request->id != null && $request->id != "") {

                $smtp = Smtp::where('id', $request->id)->first();
                if (isset($smtp->id)) {
                    $smtp->protocol = $request->protocol;
                    $smtp->host = $request->host;
                    $smtp->port = $request->port;
                    $smtp->user = $request->user;
                    $smtp->pass = $request->pass;
                    $smtp->from_name = $request->from_name;
                    $smtp->from_email = $request->from_email;
                    $smtp->status = $request->status;
                    if ($smtp->save()) {
                        return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
                    } else {
                        return response()->json(['status' => 400, 'errors' => __('label.data_not_updated')]);
                    }
                }
            } else {
                $insert = new Smtp();
                $insert->protocol = $request->protocol;
                $insert->host = $request->host;
                $insert->port = $request->port;
                $insert->user = $request->user;
                $insert->pass = $request->pass;
                $insert->from_name = $request->from_name;
                $insert->from_email = $request->from_email;
                $insert->status = $request->status;
                if ($insert->save()) {
                    return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.data_not_updated')]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function testSmtp(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }
            $this->common->Send_Mail(4, $request->email, '', '', 0, '', '');

            return response()->json(['status' => 200, 'success' => __('label.mail_sent')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function sociallinksupdate(Request $request)
    {
        try {

            $step_name = $request['step_name'];
            $step_url = $request['step_url'];
            $step_image = $request->file('step_image');
            $step_id = $request['step_id'];
            $old_step_image = $request['old_step_image'];

            // remove step from database 
            if (!empty($step_id)) {
                $id = array_slice($step_id, 1);
                $step_remove = Social_Link::whereNotIn('id', $id)->get();

                foreach ($step_remove as $step) {
                    $removed_step_image = $step->image;
                    $this->common->deleteImageToFolder($this->folder, $removed_step_image);
                    Social_Link::where('id', $step->id)->delete();
                }
            }

            // update step 
            for ($i = 0; $i < count($step_name); $i++) {
                $stepname = $step_name[$i];
                $stepurl = $step_url[$i];
                $stepimage = isset($step_image[$i]) ? $step_image[$i] : null;
                $oldstepimage = isset($old_step_image[$i]) ? $old_step_image[$i] : null;
                $stepid = isset($step_id[$i]) ? $step_id[$i] : null;

                if (!empty($stepname) && !empty($stepurl) && (!is_null($stepimage) || !is_null($oldstepimage))) {
                    $stepdata = [
                        'name' => $stepname,
                        'url' => $stepurl,
                    ];

                    // image update 
                    if (!is_null($stepimage)) {
                        $stepimagepath = $this->common->saveImage($stepimage, $this->folder, 'app_');
                        $stepdata['image'] = $stepimagepath;

                        $this->common->deleteImageToFolder($this->folder, $oldstepimage);
                    }
                    unset($request['old_step_image' . $i]);

                    // update if id is set 
                    if (!is_null($stepid)) {

                        Social_Link::where(['id' => $stepid])->update($stepdata);
                    } else {

                        Social_Link::create([
                            'name' => $stepname,
                            'url' => $stepurl,
                            'image' => $stepimagepath,
                        ]);
                    }
                } else {

                    // delete if any fildes are missing 
                    Social_Link::where('id', $stepid)->delete();
                    $this->common->deleteImageToFolder($this->folder, $oldstepimage);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function saveOnBoardingScreen(Request $request)
    {
        try {

            $arr_title = $request['title'];
            $arr_img = $request->file('image');
            $arr_old_image = $request['old_image'];

            // Save New All Link
            $not_delete_img = array();
            $not_delete_ids = array();

            for ($i = 0; $i < count($arr_title); $i++) {

                if (!empty($arr_title[$i])) {

                    if (!empty($arr_img[$i])) {

                        $insert = new Onboarding_Screen();
                        $insert->title = $arr_title[$i];
                        $insert->image = $this->common->saveImage($arr_img[$i], $this->folder, 'app_');
                        $insert->save();

                        $this->common->deleteImageToFolder($this->folder, $arr_old_image[$i]);
                    } else {
                        if (!empty($arr_old_image[$i])) {

                            $insert = new Onboarding_Screen();
                            $insert->title = $arr_title[$i];
                            $insert->image = $arr_old_image[$i];
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

                    $this->common->deleteImageToFolder($this->folder, $all_old_data[$i]['image']);
                }
                $all_old_data[$i]->delete();
            }

            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
