<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthArtist
{
    public function handle(Request $request, Closure $next)
    {
        $artist = Auth::guard('artist')->user();
        if (!$artist) {
            if (!$request->ajax() || !$request->wantsJson()) {
                return redirect(route('artist.login'));
            }
        }
        if ($artist && $artist->role !== 'artist') {
            Auth::guard('artist')->logout();
            return redirect(route('artist.login'));
        }
        $response = $next($request);
        return $response;
    }
}
