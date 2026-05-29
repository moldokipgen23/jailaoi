<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (env('DEMO_MODE') == 'ON') {
            return back()->with('error', __('label.access_denied_can_not_add_edit_delete'));
        }
        return $next($request);
    }
}
