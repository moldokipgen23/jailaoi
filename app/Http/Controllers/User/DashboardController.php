<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\ArtistKyc;
use App\Models\MonetizationApplication;
use App\Models\Music;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Subscriber;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::guard('user')->user();
        if (!$user) return redirect()->route('user.login');

        $artist = Artist::where('user_id', $user->id)->first();

        // --- Counts ---
        $songCount    = $artist ? Song::where('artist_id', $artist->id)->where('status', 1)->count() : 0;
        $musicCount   = $artist ? Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->where('status', 1)->count() : 0;
        $podcastCount = $artist ? Podcast::where('artist_id', $artist->id)->where('status', 1)->count() : 0;
        $totalContent = $songCount + $musicCount + $podcastCount;

        // --- Plays & followers ---
        $totalPlays = $artist
            ? Song::where('artist_id', $artist->id)->sum('total_play')
              + Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->sum('total_play')
              + Podcast::where('artist_id', $artist->id)->sum('total_play')
            : 0;

        $totalFollowers = $artist
            ? Subscriber::where('to_user_id', $user->id)->count()
            : 0;

        $monthlyListeners = $artist
            ? DB::table('tbl_user_action')
                ->where('artist_id', $artist->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')->count('user_id')
            : 0;

        // --- Wallet / earnings ---
        $walletBalance   = $artist ? round((float) $artist->wallet_balance, 2) : 0;
        $totalEarned     = $artist
            ? round((float) ArtistEarning::where('artist_id', $artist->id)->whereNotNull('settled_month')->sum('amount'), 2)
            : 0;
        $pendingPlays    = $artist
            ? ArtistEarning::where('artist_id', $artist->id)->whereNull('settled_month')->count()
            : 0;

        // --- Monetization / KYC status ---
        $monetizationRow    = $artist ? MonetizationApplication::where('artist_id', $artist->id)->latest()->first() : null;
        $monetizationStatus = $monetizationRow ? $monetizationRow->status : null;
        $kycRow             = $artist ? ArtistKyc::where('artist_id', $artist->id)->latest()->first() : null;
        $kycStatus          = $kycRow ? $kycRow->status : null;

        // --- Top songs by plays ---
        $topSongs = $artist
            ? Song::where('artist_id', $artist->id)
                ->where('status', 1)
                ->orderByDesc('total_play')
                ->take(5)
                ->get(['id', 'title', 'image', 'total_play'])
            : collect();

        // --- Monthly plays trend (last 6 months) ---
        $monthlyTrend = [];
        if ($artist) {
            $rows = DB::table('tbl_artist_earnings')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as plays'))
                ->where('artist_id', $artist->id)
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get()->keyBy('month');

            for ($i = 5; $i >= 0; $i--) {
                $key = now()->subMonths($i)->format('Y-m');
                $monthlyTrend[] = [
                    'label' => now()->subMonths($i)->format('M'),
                    'plays' => (int) ($rows[$key]->plays ?? 0),
                ];
            }
        }

        // --- Withdrawal stats ---
        $paidOut = $artist
            ? round((float) WithdrawalRequest::where('artist_id', $artist->id)->where('status', 'paid')->sum('amount'), 2)
            : 0;
        $pendingWithdrawal = $artist
            ? round((float) WithdrawalRequest::where('artist_id', $artist->id)->whereIn('status', ['pending', 'approved'])->sum('amount'), 2)
            : 0;

        return view('user.dashboard.index', compact(
            'user', 'artist',
            'songCount', 'musicCount', 'podcastCount', 'totalContent',
            'totalPlays', 'totalFollowers', 'monthlyListeners',
            'walletBalance', 'totalEarned', 'pendingPlays',
            'monetizationStatus', 'kycStatus',
            'topSongs', 'monthlyTrend',
            'paidOut', 'pendingWithdrawal'
        ));
    }
}
