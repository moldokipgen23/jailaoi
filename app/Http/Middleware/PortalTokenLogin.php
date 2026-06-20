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

        if ($token && !Auth::guard('user')->check()) {
            $userId = Cache::pull("portal_token:{$token}");
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    Auth::guard('user')->login($user);
                }
            }
        }

        return $next($request);
    }
}
