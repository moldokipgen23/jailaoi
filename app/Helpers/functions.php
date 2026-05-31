<?php

use App\Models\General_Setting;
use App\Models\Storage_Setting;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

function Setting_Data()
{
    $setting = General_Setting::get();
    $data = [];
    foreach ($setting as $value) {
        $data[$value->key] = $value->value;
    }
    return $data;
}
function Tab_Icon()
{
    try {
        $setting_data = Setting_Data();
        $app_logo = $setting_data['app_logo'];
        $app_logo_storage_type = $setting_data['app_logo_storage_type'];
        $folder = "setting";

        if ($app_logo != "") {

            if ($app_logo_storage_type == 1) {

                $img_base_url = Config::get('app.image_url');
                if (Storage::disk('public')->exists($folder . '/' . $app_logo)) {
                    $app_logo = $img_base_url . $folder . '/' . $app_logo;
                } else {
                    $app_logo = "";
                }
            } else if ($app_logo_storage_type == 2) {

                $storage = Storage_Setting();
                $appName = $storage['s3_endpoint'];
                $bucket_name = $storage['s3_bucket_name'];

                if (Storage::disk('s3')->exists($folder . '/' . $app_logo)) {
                    $app_logo =  'https://' . $bucket_name . '.' . $appName . '/' . $folder . '/' . $app_logo;
                } else {
                    $app_logo = "";
                }
            } else {
                $app_logo = "";
            }
        } else {
            $app_logo = "";
        }
        return $app_logo;
    } catch (Exception $e) {
        return $app_logo = '';
    }
}
function App_Name()
{
    $setting_data = Setting_Data();
    $app_name = $setting_data['app_name'];

    if (isset($app_name) && $app_name != "") {
        return $app_name;
    } else {
        return env('APP_NAME');
    }
}
function String_Cut($string, $len)
{
    if (strlen($string) > $len) {
        $string = mb_substr(strip_tags($string), 0, $len, 'utf-8') . '...';
    }
    return $string;
}
function Login_Image()
{
    try {
        $Setting_Data = Setting_Data();
        $login_page_img = $Setting_Data['app_logo'];
        $app_logo_storage_type = $Setting_Data['app_logo_storage_type'];
        $folder = "setting";

        if ($login_page_img != "") {

            if ($app_logo_storage_type == 1) {

                $img_base_url = Config::get('app.image_url');
                if (Storage::disk('public')->exists($folder . '/' . $login_page_img)) {
                    $login_page_img = $img_base_url . $folder . '/' . $login_page_img;
                } else {
                    $login_page_img = asset('assets/imgs/login.png');
                }
            } else if ($app_logo_storage_type == 2) {

                $storage = Storage_Setting();
                $appName = $storage['s3_endpoint'];
                $bucket_name = $storage['s3_bucket_name'];

                if (Storage::disk('s3')->exists($folder . '/' . $login_page_img)) {
                    $login_page_img =  'https://' . $bucket_name . '.' . $appName . '/' . $folder . '/' . $login_page_img;
                } else {
                    $login_page_img = asset('assets/imgs/login.png');
                }
            } else {
                $login_page_img = asset('assets/imgs/login.png');
            }
        } else {
            $login_page_img = asset('assets/imgs/login.png');
        }
        return $login_page_img;
    } catch (Exception $e) {
        return asset('assets/imgs/login.png');
    }
}
function Admin_Data()
{
    if (Auth::guard('admin')->check()) {
        return Auth::guard('admin')->user();
    }
    return redirect()->route('admin.logout')->send();
}
function Currency_Code()
{
    $data = Setting_Data();
    return $data['currency_code'] ?? '$';
}
function No_Format($num)
{
    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('K', 'M', 'B', 'T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }
    return $num;
}
function User_Data()
{
    if (Auth::guard('user')->user()) {
        return Auth::guard('user')->user();
    } else {
        return redirect()->route('user.logout');
    }
}
function Storage_Setting()
{
    $data = Storage_Setting::first();
    return $data;
}
function Storage_Type()
{
    $data = Storage_Setting::first();
    return $data['storage_type'];
}

// Access & Installation
function Demo_Mode()
{
    if (env('DEMO_MODE') == 'ON') {
        return 0;
    } else {
        return 1;
    }
}
function Demo_Domain()
{
	return 1;
    $domain = request()->getHost();
    if ($domain == base64_decode('bG9jYWxob3N0') || $domain == base64_decode('ZGV2LmRpdmluZXRlY2hzLmNvbQ==') || $domain == base64_decode('c3RhZy5kaXZpbmV0ZWNocy5jb20=') || $domain == base64_decode('ZHR0dWJlLmRpdmluZXRlY2hzLmNvbQ==')) {
        return 1;
    } else {
        return 0;
    }
}
function Item_Code()
{
    return base64_decode('NTAzMTM0NDQ=');
}
function Set_Environment_Value($envKey, $envValue)
{
    $envFile = app()->environmentFilePath();
    $str = file_get_contents($envFile);

    $oldValue = env($envKey);

    if (strpos($str, $envKey) !== false) {
        $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
    } else {
        $str .= "{$envKey}={$envValue}\n";
    }

    $fp = fopen($envFile, 'w');
    fwrite($fp, $str);
    fclose($fp);
    return $envValue;
}
