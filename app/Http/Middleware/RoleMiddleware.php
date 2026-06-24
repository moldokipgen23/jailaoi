<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RoleMiddleware
{
    private static array $roleAccess = [
        'super_admin' => ['*'],
        'staff' => [
            'dashboard',
            'profile.*',
            'user.*',
            'artist.*',
            'song.*',
            'music.*',
            'podcast.*',
            'liveevent.*',
            'section.*',
            'category.*',
            'language.*',
            'city.*',
            'banner.*',
            'notification.*',
            'comment.*',
            'page.*',
            'admin.artist-requests.*',
            'admin.play-errors',
            'setting*',
            'system.setting.*',
            'panel_setting.*',
            'notification_configuration.*',
            'admob.*',
            'fbads.*',
            'ads_setting.*',
            'ads.*',
        ],
        'finance' => [
            'profile.*',
            'package.*',
            'transaction.*',
            'payment.*',
            'invoice.*',
            'admin.withdrawals.*',
            'admin.earnings.*',
            'admin.monetization.*',
            'admin.kyc.*',
            'admin.artist-analytics.*',
            'admin.artist-requests.*',
            'user.index',
            'artist.index',
            'dashboard',
        ],
        'support' => [
            'profile.*',
            'user.index',
            'artist.index',
            'artist.show',
            'admin.kyc.*',
            'admin.artist-requests.*',
            'comment.*',
            'admin.play-errors',
            'dashboard',
        ],
    ];

    private static array $roleLabels = [
        'super_admin' => 'Super Admin',
        'staff' => 'Staff / Operations',
        'finance' => 'Finance',
        'support' => 'Customer Support',
    ];

    public static function getRoleLabels(): array
    {
        return self::$roleLabels;
    }

    public static function getRoleAccess(): array
    {
        return self::$roleAccess;
    }

    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            abort(403, 'Unauthorized.');
        }

        $routeName = Route::currentRouteName();
        if (!$routeName) {
            return $next($request);
        }

        if (!$this->adminHasAccess($admin->role, $routeName, $admin->permissions)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }

    public static function adminHasAccess(string $role, string $routeName, ?array $overrides = null): bool
    {
        $patterns = self::$roleAccess[$role] ?? [];

        if ($overrides && is_array($overrides)) {
            $patterns = $overrides;
        }

        foreach ($patterns as $pattern) {
            if ($pattern === '*') {
                return true;
            }
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    public static function canAccess(string $routeName): bool
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return false;
        }
        return self::adminHasAccess($admin->role, $routeName, $admin->permissions);
    }
}
