<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class LoginController extends Controller
{
    public $common;
    public function __construct()
    {
        try {
            $this->middleware('guest:artist', ['except' => 'logout']);
            $this->common = new Common();
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        try {
            Auth()->guard('artist')->logout();
            $params['result'] = Setting_Data();
            return view('artist.login.login', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function save_login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:4',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $credentials = ['email' => $requestData['email'], 'password' => $requestData['password'], 'role' => 'artist'];

            if (Auth()->guard('artist')->attempt($credentials)) {
                return response()->json(['status' => 200, 'success' => __('label.success_login')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_login')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        try {
            Auth()->guard('artist')->logout();
            return redirect()->route('artist.login')->with('success', __('label.logout_successfully'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
