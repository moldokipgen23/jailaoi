<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $admin = Admin_Data();
            if (isset($admin) && isset($admin['id'])) {

                $params['data'] = Admin::where('id', $admin['id'])->first();
                return view('admin.profile.index', $params);
            } else {
                return redirect()->route('admin.logout');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|min:4',
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $data = Admin::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {
                return response()->json(['status' => 200, 'success' => __('label.data_edit_successfully')]);
            } else {
                return redirect()->route('admin.logout');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ChangePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:4',
                'confirm_password' => 'required|min:4|same:new_password',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $admin = Admin::where('id', $request['id'])->first();
            if (isset($admin) && $admin != null) {

                if (Hash::check($request['current_password'], $admin['password'])) {

                    $admin['password'] = Hash::make($request['new_password']);
                    if ($admin->save()) {
                        return response()->json(['status' => 200, 'success' => __('label.password_change_successfully')]);
                    }
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.please_enter_right_current_password')]);
                }
            } else {
                return redirect()->route('admin.logout');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
