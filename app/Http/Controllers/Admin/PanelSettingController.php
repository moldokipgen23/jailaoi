<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Exception;

class PanelSettingController extends Controller
{
    private $folder = "app";
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

                $params['result']['login_page_image'] = $this->common->Get_Image($this->folder, $params['result']['login_page_image']);

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
            $data = $request->all();

            if (isset($data['login_page_image'])) {

                $files = $data['login_page_image'];

                $data['login_page_image'] = $this->common->saveImage($files, $this->folder, 'panel_');
                $this->common->deleteImageToFolder($this->folder, basename($data['old_login_page_image']));
            }

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting['id'])) {
                    $setting['value'] = $value;
                    $setting->save();
                }
            }

            return response()->json(['status' => 200, 'success' => __('label.save_setting')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
