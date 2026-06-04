<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\Notification_Configuration;
use Illuminate\Http\Request;
use Exception;

class NotificationConfigurationController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $data = Setting_Data();
            $params['main_status'] = $data['notification_configuration'];

            if ($request->ajax()) {
                $notification = Notification_Configuration::get();

                return DataTables()::of($notification)
                    ->addIndexColumn()
                    ->make(true);
            }
            return view('admin.notification_configuration.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            $key = $request['key'];
            $data = $request['data'];

            if ($key == 0) {
                General_Setting::where('key', 'notification_configuration')->update(['value' => 0]);
                Notification_Configuration::query()->update(['send_mail' => 0, 'send_notification' => 0]);
            } else {
                General_Setting::where('key', 'notification_configuration')->update(['value' => 1]);
                foreach ($data as $row) {
                    Notification_Configuration::where('type', $row['type'])->update(['send_mail' => $row['mail'], 'send_notification' => $row['notification']]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_edit_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
