<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('user')->guest()) {
            return $request->expectsJson()
                ? response()->json(['status' => 401, 'message' => 'Please sign in.'], 401)
                : redirect(route('user.login'));
        }
        $user = Auth::guard('user')->user();
        if ($user && ($user->role !== 'artist' || (int) $user->status !== 1)) {
            Auth::guard('user')->logout();
            return redirect(route('user.login'));
        }
        if (\App\Models\Artist::where('user_id', $user->id)->where('is_suspended', 1)->exists()) {
            Auth::guard('user')->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $request->expectsJson()
                ? response()->json(['status' => 423, 'message' => 'Artist access is suspended. Please contact support.'], 423)
                : redirect(route('user.login'))->withErrors(['email' => 'Artist access is suspended. Please contact support.']);
        }
        $response = $next($request);
        return $response;
    }
}
