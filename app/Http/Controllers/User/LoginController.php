<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class LoginController extends Controller
{
    private $folder = "setting";
    public $common;
    protected $redirectTo = 'user/login';
    public function __construct()
    {
        try {
            $this->middleware('guest', ['except' => 'logout']);
            $this->common = new Common();
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        try {
            Auth()->guard('user')->logout();

            $params['result'] = Setting_Data();
            if ($params['result']['panel_login_page_view'] == 1) {
                $params['result']['panel_login_page_bg_image'] = $this->common->getImage($this->folder, $params['result']['panel_login_page_bg_image'], $params['result']['panel_login_page_bg_image_storage_type']);
            } else if ($params['result']['panel_login_page_view'] == 2) {
                $params['result']['panel_login_page_image'] = $this->common->getImage($this->folder, $params['result']['panel_login_page_image'], $params['result']['panel_login_page_image_storage_type']);
            }

            return view('user.login.login', $params);
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
            if (Auth()->guard('user')->attempt(['email' => $requestData['email'], 'password' => $requestData['password'], 'role' => 'artist', 'user_penal_status' => 1])) {
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
            Auth()->guard('user')->logout();
            return redirect()->route('user.login')->with('success', __('label.logout_successfully'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
