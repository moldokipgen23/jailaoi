<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PanelSettingController extends Controller
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

                $params['result']['panel_login_page_image'] = $this->common->getImage($this->folder, $params['result']['panel_login_page_image'], $params['result']['panel_login_page_image_storage_type']);
                $params['result']['panel_login_page_bg_image'] = $this->common->getImage($this->folder, $params['result']['panel_login_page_bg_image'], $params['result']['panel_login_page_bg_image_storage_type']);

                return view('admin.panel_setting.index', $params);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function save(Request $request)
    {
        try {

            $rules = [
                'panel_login_page_view' => 'required',
            ];
            if ($request['panel_login_page_view'] == 1) {
                $rules['panel_login_page_image'] = 'image|mimes:jpeg,png,jpg';
            } else {
                $rules['panel_login_page_bg_color'] = 'required';
                $rules['panel_login_page_bg_image'] = 'image|mimes:jpeg,png,jpg';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $data = $request->all();
            $storage_type = Storage_Type();

            $data['panel_login_page_view'] = $data['panel_login_page_view'] ?? 0;
            if ($data['panel_login_page_view'] == 1) {

                if (isset($data['panel_login_page_bg_image'])) {

                    $files = $data['panel_login_page_bg_image'];
                    $data['panel_login_page_bg_image_storage_type'] = $storage_type;
                    $data['panel_login_page_bg_image'] = $this->common->saveImage($files, $this->folder, 'panel_', $storage_type);
                    $this->common->deleteImageToFolder($this->folder, basename($data['old_panel_login_page_bg_image']), $data['old_panel_login_page_bg_image_storage_type']);
                }

                $data['panel_login_page_bg_color'] = "#000000";
                $data['panel_login_page_image'] = "";
                $data['panel_login_page_image_storage_type'] = $storage_type;
                $this->common->deleteImageToFolder($this->folder, basename($data['old_panel_login_page_image']), $data['old_panel_login_page_image_storage_type']);
            } else if ($data['panel_login_page_view'] == 2) {

                $data['panel_login_page_bg_image'] = "";
                $data['panel_login_page_bg_image_storage_type'] = $storage_type;
                $this->common->deleteImageToFolder($this->folder, basename($data['old_panel_login_page_bg_image']), $data['old_panel_login_page_bg_image_storage_type']);

                $data['panel_login_page_bg_color'] = $data['panel_login_page_bg_color'] ?? "#00000";
                if (isset($data['panel_login_page_image'])) {

                    $files = $data['panel_login_page_image'];
                    $data['panel_login_page_image_storage_type'] = $storage_type;
                    $data['panel_login_page_image'] = $this->common->saveImage($files, $this->folder, 'panel_', $storage_type);
                    $this->common->deleteImageToFolder($this->folder, basename($data['old_panel_login_page_image']), $data['old_panel_login_page_image_storage_type']);
                }
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
}
