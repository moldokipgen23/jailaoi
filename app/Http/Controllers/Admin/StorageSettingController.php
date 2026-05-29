<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Storage_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Cache;

class StorageSettingController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $params['storage'] = Storage_Setting::latest()->first();
            if ($params['storage']) {
                return view('admin.storage_setting.index', $params);
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
            if ($request['storage_type'] == 2) {

                $validator = Validator::make($request->all(), [
                    's3_access_key' => 'required',
                    's3_secret_key' => 'required',
                    's3_bucket_name' => 'required',
                    's3_region' => 'required',
                    's3_endpoint' => 'required'
                ]);
                if ($validator->fails()) {
                    $errs = $validator->errors()->all();
                    return response()->json(['status' => 400, 'errors' => $errs]);
                }
            }

            $requestData = $request->all();

            $requestData['s3_access_key'] = $requestData['s3_access_key'] ?? "";
            $requestData['s3_secret_key'] = $requestData['s3_secret_key'] ?? "";
            $requestData['s3_bucket_name'] = $requestData['s3_bucket_name'] ?? "";
            $requestData['s3_region'] = $requestData['s3_region'] ?? "";
            $requestData['s3_endpoint'] = $requestData['s3_endpoint'] ?? "";
            $requestData['status'] = 1;

            Storage_Setting::updateOrCreate(['id' => $requestData['id']], $requestData);

            // Remove Cache
            Cache::forget('s3_storage_settings');

            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
