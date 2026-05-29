<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\Notification;
use App\Models\Read_Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class NotificationController extends Controller
{
    private $folder = "notification";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['data'] = [];
            if ($request->ajax()) {

                $query = Notification::where('type', 1);

                $input_search = $request['input_search'];
                if ($input_search != null) {
                    $query->where('title', 'LIKE', "%{$input_search}%");
                }
                $data = $query->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $notification_delete = __('label.delete_notification');

                        $delete = '<form onsubmit="return confirm(\'' . $notification_delete . '\');" method="POST" action="' . route('admin.notification.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.notification.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'message' => 'required|min:2',
                'image' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['type'] = 1;
            $requestData['storage_type'] = Storage_Type();
            $notificationImageURL = '';
            if (isset($requestData['image']) && $requestData['image'] != null) {

                $file = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($file, $this->folder, 'noti_', $requestData['storage_type']);
                $notificationImageURL = $this->common->getImage($this->folder, $requestData['image'], $requestData['storage_type']);
            } else {
                $requestData['image'] = "";
            }
            $requestData['user_id'] = 0;
            $requestData['from_user_id'] = 0;
            $requestData['content_id'] = 0;
            $requestData['status'] = 1;

            $data = Notification::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {

                $settingData = Setting_Data();
                $ONESIGNAL_APP_ID = $settingData['onesignal_app_id'];
                $ONESIGNAL_REST_KEY = $settingData['onesignal_rest_key'];

                $fields = array(
                    'app_id' => $ONESIGNAL_APP_ID,
                    'included_segments' => array('All'),
                    'data' => array("foo" => "bar"),
                    'headings' => array("en" => $request['title']),
                    'contents' => array("en" => $request['message']),
                    'big_picture' => $notificationImageURL,
                );
                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic ' . $ONESIGNAL_REST_KEY,
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);
                curl_close($ch);

                return response()->json(['status' => 200, 'success' => __('label.success_add_notification')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_notification')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Notification::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['storage_type']);
                $data->delete();

                Read_Notification::where('notification_id', $id)->delete();
            }
            return redirect()->route('admin.notification.index')->with('success', __('label.notification_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // Setting
    public function setting()
    {
        try {

            $data = Setting_Data();
            if ($data) {

                if (Demo_Mode() == 0) {
                    $data['onesignal_app_id'] = "xxxxxxxxxxxxxxxxxxxx";
                    $data['onesignal_rest_key'] = "xxxxxxxxxxxxxxxxxxxx";
                }

                return view('admin.notification.setting', ['result' => $data]);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function settingsave(Request $request)
    {
        try {

            $data = $request->all();
            $data["onesignal_app_id"] = $data['onesignal_app_id'] ?? '';
            $data["onesignal_rest_key"] = $data['onesignal_rest_key'] ?? '';

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
