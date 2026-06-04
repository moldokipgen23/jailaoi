<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Artisan;

class Installation
{
    public function handle(Request $request, Closure $next)
{
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        
        // Check DB connection only
        if (@mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'))) {
            return $next($request);
        } else {
            return redirect()->route('step0');
        }
    } catch (Exception $e) {
        session()->flash('error', $e->getMessage());
        return redirect()->route('step0');
    }
}
}
