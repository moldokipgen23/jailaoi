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
            if (!$request->ajax() || !$request->wantsJson()) {
                return redirect(route('user.login'));
            }
        }
        $user = Auth::guard('user')->user();
        if ($user && $user->role !== 'artist') {
            Auth::guard('user')->logout();
            return redirect(route('user.login'));
        }
        $response = $next($request);
        return $response;
    }
}
