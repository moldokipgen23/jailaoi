<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Mail\PasswordChangedMail;
use App\Models\Common;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    private $common;

    public function __construct()
    {
        $this->common = new Common;
    }

    public function showForgot()
    {
        return view('user.password.forgot');
    }

    public function sendResetLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $user = User::where('email', $request->email)->first();
            // Always respond OK even if email not found (prevents email enumeration)
            if (!$user) {
                return response()->json([
                    'status' => 200,
                    'success' => 'If an account exists for that email, a reset link has been sent.',
                ]);
            }

            $token = Str::random(64);
            DB::table('password_resets')->where('email', $request->email)->delete();
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            $resetUrl = url('/user/password/reset?' . http_build_query([
                'token' => $token,
                'email' => $request->email,
            ]));

            $this->sendMail($request->email, $user->full_name ?? 'there', $resetUrl);

            return response()->json([
                'status' => 200,
                'success' => 'If an account exists for that email, a reset link has been sent.',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function showReset(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        if (!$token || !$email) {
            return redirect()->route('user.login')->with('error', 'Invalid reset link.');
        }
        return view('user.password.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $row = DB::table('password_resets')->where('email', $request->email)->first();
            if (!$row) {
                return response()->json(['status' => 400, 'errors' => 'Invalid or expired link.']);
            }

            // 1 hour expiry
            if (Carbon::parse($row->created_at)->addHour()->isPast()) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                return response()->json(['status' => 400, 'errors' => 'This link has expired. Please request a new one.']);
            }

            if (!Hash::check($request->token, $row->token)) {
                return response()->json(['status' => 400, 'errors' => 'Invalid or expired link.']);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['status' => 400, 'errors' => 'Account not found.']);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_resets')->where('email', $request->email)->delete();

            try {
                $this->common->SetSmtpConfig();
                Mail::to($user->email)->send(new PasswordChangedMail($user->full_name ?: $user->user_name));
            } catch (Exception $e) {
                // silent — don't break the main flow
            }

            return response()->json(['status' => 200, 'success' => 'Password reset. You can now log in.']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    private function sendMail($email, $name, $resetUrl)
    {
        try {
            $this->common->SetSmtpConfig();
            Mail::to($email)->send(new ForgotPasswordMail($name, $resetUrl));
        } catch (Exception $e) {
            // silent — don't break the flow if email sending fails
        }
    }
}
