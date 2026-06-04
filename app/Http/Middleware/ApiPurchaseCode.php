<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Artisan;

class ApiPurchaseCode
{
    public function handle(Request $request, Closure $next)
    {
       return $next($request);
    }
}
