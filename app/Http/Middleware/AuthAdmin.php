<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {

        if (Auth::guard('admin')->guest()) {
            return $request->expectsJson()
                ? response()->json(['status' => 401, 'message' => 'Please sign in.'], 401)
                : redirect(route('admin.login'));
        }
        return $next($request);
    }
}
