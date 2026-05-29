<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthArtist
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->guest()) {
            if (!$request->ajax() || !$request->wantsJson()) {
                return redirect(route('artist.login'));
            }
        }
        return $next($request);
    }
}
