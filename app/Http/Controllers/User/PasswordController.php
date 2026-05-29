<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $User = User_Data();
            $params['user_id'] = $User['id'];

            return view('user.password.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
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

            $user = User::where('id', $request['id'])->first();
            if (isset($user) && $user != null) {

                if (Hash::check($request['current_password'], $user['password'])) {

                    $user['password'] = Hash::make($request['new_password']);
                    if ($user->save()) {
                        return response()->json(['status' => 200, 'success' => __('label.password_change_successfully')]);
                    }
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.please_enter_right_current_password')]);
                }
            } else {
                return redirect()->route('user.logout');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
