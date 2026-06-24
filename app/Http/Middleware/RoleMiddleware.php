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
            'admin.support-tickets.*',
            'dashboard',
        ],
    ];

    private static array $permissionGroups = [
        'DASHBOARD' => [
            ['label' => 'Dashboard', 'routes' => ['dashboard']],
        ],
        'USERS & ARTISTS' => [
            ['label' => 'Users — Full Access', 'routes' => ['user.*']],
            ['label' => 'Artists — Full Access', 'routes' => ['artist.*']],
            ['label' => 'Artist Requests', 'routes' => ['admin.artist-requests.*']],
        ],
        'CONTENT' => [
            ['label' => 'Music / Songs', 'routes' => ['music.*']],
            ['label' => 'Radio Stations', 'routes' => ['song.*']],
            ['label' => 'Podcasts', 'routes' => ['podcast.*']],
            ['label' => 'Live Events', 'routes' => ['liveevent.*']],
            ['label' => 'Sections', 'routes' => ['section.*']],
            ['label' => 'Categories', 'routes' => ['category.*']],
            ['label' => 'Languages', 'routes' => ['language.*']],
            ['label' => 'Cities', 'routes' => ['city.*']],
        ],
        'MARKETING' => [
            ['label' => 'Banners', 'routes' => ['banner.*']],
            ['label' => 'Notifications', 'routes' => ['notification.*']],
            ['label' => 'Admob & Ads', 'routes' => ['admob.*', 'fbads.*', 'ads_setting.*', 'ads.*']],
        ],
        'FINANCE' => [
            ['label' => 'Packages', 'routes' => ['package.*']],
            ['label' => 'Transactions', 'routes' => ['transaction.*']],
            ['label' => 'Payments & Invoices', 'routes' => ['payment.*', 'invoice.*']],
            ['label' => 'Withdrawals', 'routes' => ['admin.withdrawals.*']],
            ['label' => 'Earnings & Analytics', 'routes' => ['admin.earnings.*', 'admin.artist-analytics.*']],
            ['label' => 'Monetization Applications', 'routes' => ['admin.monetization.*']],
            ['label' => 'KYC Requests', 'routes' => ['admin.kyc.*']],
        ],
        'SETTINGS' => [
            ['label' => 'App Settings', 'routes' => ['setting*']],
            ['label' => 'System Settings', 'routes' => ['system.setting.*']],
            ['label' => 'Panel Settings', 'routes' => ['panel_setting.*']],
            ['label' => 'Notification Config', 'routes' => ['notification_configuration.*']],
            ['label' => 'Pages', 'routes' => ['page.*']],
        ],
        'SUPPORT' => [
            ['label' => 'Support Tickets', 'routes' => ['admin.support-tickets.*']],
        ],
        'COMMUNITY' => [
            ['label' => 'Comments', 'routes' => ['comment.*']],
            ['label' => 'Play Errors', 'routes' => ['admin.play-errors']],
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

    public static function getPermissionGroups(): array
    {
        return self::$permissionGroups;
    }

    public static function getDefaultPatterns(string $role): array
    {
        return self::$roleAccess[$role] ?? [];
    }

    public static function getRolePatternsForLabel(string $role, string $label): bool
    {
        $groupPatterns = self::$roleAccess[$role] ?? [];
        foreach (self::$permissionGroups as $group) {
            foreach ($group as $item) {
                if ($item['label'] === $label) {
                    foreach ($item['routes'] as $pattern) {
                        foreach ($groupPatterns as $rolePattern) {
                            if ($rolePattern === '*' || $rolePattern === $pattern || fnmatch($rolePattern, $pattern)) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            }
        }
        return false;
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
