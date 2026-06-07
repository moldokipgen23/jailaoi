<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyController extends Controller
{
    public function index()
    {
        return view('user.verify.index');
    }

    public function verify(Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                return redirect()->route('user.login')
                    ->with('error', 'The verification link is invalid or has expired.');
            }

            $user = User::find($request->user);
            if (!$user) {
                return redirect()->route('user.login')
                    ->with('error', 'User not found.');
            }

            if ($user->email_verified_at) {
                return redirect()->route('user.login')
                    ->with('success', 'Email already verified. You can log in.');
            }

            $user->email_verified_at = now();
            $user->save();

            return redirect()->route('user.login')
                ->with('success', 'Email verified! You can now log in.');
        } catch (Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return redirect()->route('user.login')
                ->with('error', 'Verification failed. Please try again.');
        }
    }

    public function resend(Request $request)
    {
        try {
            $email = $request->email;
            if (!$email) {
                return response()->json(['status' => 400, 'errors' => 'Email is required.']);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['status' => 400, 'errors' => 'Email not found.']);
            }

            if ($user->email_verified_at) {
                return response()->json(['status' => 400, 'errors' => 'Email already verified.']);
            }

            $this->common = new \App\Models\Common;
            $this->common->SetSmtpConfig();

            $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'user.verify.email',
                now()->addHours(24),
                ['user' => $user->id]
            );

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\VerifyEmail($user->full_name ?? 'there', $verifyUrl)
                );
            } catch (Exception $e) {
                Log::error('Resend verification email failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 200,
                'success' => 'Verification email sent. Check your inbox.',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
