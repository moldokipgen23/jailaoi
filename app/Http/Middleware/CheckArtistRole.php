<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckArtistRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'artist') {
            Auth::guard('web')->logout();
            return redirect(route('artist.login'));
        }
        return $next($request);
    }
}
