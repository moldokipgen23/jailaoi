<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class Installation
{
    public function handle(Request $request, Closure $next)
    {
        try { DB::connection()->getPdo(); }
        catch (\Throwable $e) {
            Log::error('Database unavailable', ['exception'=>get_class($e)]);
            abort(503, 'Service temporarily unavailable.');
        }
        return $next($request);
    }
}
