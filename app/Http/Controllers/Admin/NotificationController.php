<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\Notification;
use App\Models\User_Notification_Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class NotificationController extends Controller
{
    private $folder = "images/notification";
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

                $input_search = $request['input_search'];
                if ($input_search != null && isset($input_search)) {
                    $data = Notification::where('title', 'LIKE', "%{$input_search}%")->orwhere('message', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Notification::latest()->get();
                }

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_notification') . '\');" method="POST"  action="' . route('notification.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn"  title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';
                        $btn = $delete;
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
    public function create()
    {
        try {

            $params['data'] = [];
            return view('admin.notification.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255', 'description' => 'required|string|max:10000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            // Form tokens, empty IDs and client-supplied recipients are never database fields.
            $data = $request->only(['title', 'description']);
            $data['image'] = $request->hasFile('image') ? $this->common->saveImage($request->file('image'), $this->folder, 'notification_') : '';
            $record = Notification::create($data);
            $payload = [
                'included_segments' => ['Subscribed Users'],
                'headings' => ['en' => $record->title], 'contents' => ['en' => $record->description],
                'data' => ['notification_id' => $record->id],
            ];
            if ($record->image !== '') $payload['big_picture'] = $this->common->Get_Image($this->folder, $record->image);
            $push = app(\App\Services\OneSignalPush::class)->send($payload);
            $message = $push['sent'] ? 'Saved to the notification inbox and accepted for push delivery.'
                : ($push['reason'] === 'not_configured' ? 'Saved to the notification inbox. Push notifications are not configured.'
                    : 'Saved to the notification inbox. Push delivery failed; check notification settings.');
            return response()->json(['status' => 200, 'success' => $message, 'push_sent' => $push['sent'], 'push_status' => $push['reason']]);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification save failed.', ['exception' => get_class($e)]);
            return response()->json(['status' => 400, 'errors' => 'Could not save the notification. Please try again.']);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Notification::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $data->delete();

                User_Notification_Tracking::where('notification_id', $id)->delete();
            }

            return redirect()->route('notification.index')->with('success', __('label.success_delete_notification'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // Notification Setting
    public function setting()
    {
        try {

            $data = Setting_Data();
            if ($data) {
                return view('admin.notification.setting', ['result' => $data]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function settingsave(Request $request)
    {
        try {

            $data = $request->only(['onesignal_apid', 'onesignal_rest_key']);
            $data["onesignal_apid"] = isset($data['onesignal_apid']) ? $data['onesignal_apid'] : '';
            $data["onesignal_rest_key"] = isset($data['onesignal_rest_key']) ? $data['onesignal_rest_key'] : '';

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
}
