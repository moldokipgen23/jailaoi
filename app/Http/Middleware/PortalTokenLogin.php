<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class PortalTokenLogin
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('portal_token');

        if (is_string($token) && preg_match('/^[A-Za-z0-9]{48}$/D', $token) && !Auth::guard('user')->check()) {
            $userId = Cache::pull("portal_token:{$token}");
            if ($userId) {
                $user = User::find($userId);
                if ($user && $user->role === 'artist' && (int) $user->status === 1
                    && !\App\Models\Artist::where('user_id', $user->id)->where('is_suspended', 1)->exists()) {
                    Auth::guard('user')->login($user);
                    if ($request->hasSession()) $request->session()->regenerate();
                }
            }
        }

        return $next($request);
    }
}
