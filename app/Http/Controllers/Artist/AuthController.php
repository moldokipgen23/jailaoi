<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            Auth::guard('web')->logout();
            return view('artist.login.login');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $credentials = $request->only('email', 'password');
            $credentials['role'] = 'artist';
            $credentials['status'] = 1;

            if (Auth::guard('web')->attempt($credentials)) {
                return response()->json(array('status' => 200, 'success' => 'Login successful'));
            } else {
                return response()->json(array('status' => 400, 'errors' => 'Invalid credentials or account not activated'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function logout()
    {
        try {
            Auth::guard('web')->logout();
            return redirect()->route('artist.login')->with('success', 'Logged out successfully');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
