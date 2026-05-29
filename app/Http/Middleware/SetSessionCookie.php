<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSessionCookie
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        if (str_starts_with($path, 'admin')) {
            config(['session.cookie' => 'jailaoi_admin_session']);
        } elseif (str_starts_with($path, 'artist')) {
            config(['session.cookie' => 'jailaoi_artist_session']);
        }

        return $next($request);
    }
}
