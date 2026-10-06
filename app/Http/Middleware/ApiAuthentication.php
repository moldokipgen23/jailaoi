<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthentication
{
    private const PUBLIC_ENDPOINTS = [
        'login', 'register', 'general_setting', 'get_payment_option', 'get_pages',
        'get_onboarding_screen', 'get_social_link', 'get_city', 'get_artist',
        'get_category', 'get_language', 'get_package', 'get_radio_by_city',
        'get_radio_by_artist', 'get_radio_by_language', 'get_radio_by_category',
        'get_latest_song', 'get_popular_song', 'get_latest_podcast', 'get_popular_podcast',
        'get_live_event', 'search_content', 'get_section_list', 'get_section_detail',
        'get_podcast_section_list', 'get_podcast_section_detail', 'get_radio_section_list',
        'get_music_section_list', 'get_banner', 'get_episode_by_podcast', 'get_comment',
        'get_related_data', 'get_content_by_artist', 'get_radio_banner', 'get_podcast_banner',
        'get_artist_list', 'get_artist_profile', 'get_artist_content', 'get_content_detail',
        'get_station_songs', 'log_play_error',
    ];

    public function handle(Request $request, Closure $next)
    {
        $endpoint = preg_replace('#^api/#', '', trim($request->path(), '/'));
        $user = null;
        if (!in_array($endpoint, ['login', 'register'], true) && ($bearer = $request->bearerToken())) {
            $token = PersonalAccessToken::findToken($bearer);
            if ($token && (!$token->expires_at || $token->expires_at->isFuture())
                && $token->tokenable instanceof User && $token->tokenable->status === 1
                && $token->can('listener')) {
                $user = $token->tokenable->withAccessToken($token);
            } else {
                return response()->json(['status' => 401, 'message' => 'Your session has expired. Please sign in again.'], 401);
            }
        }
        if (!$user && !in_array($endpoint, self::PUBLIC_ENDPOINTS, true)) {
            return response()->json(['status' => 401, 'message' => 'Please sign in to continue.'], 401);
        }
        $request->setUserResolver(fn () => $user);
        // Clients may send a cached user ID; it never determines the acting identity.
        $request->merge(['user_id' => $user?->id ?? 0, 'login_user_id' => $user?->id ?? 0]);
        return $next($request);
    }
}
