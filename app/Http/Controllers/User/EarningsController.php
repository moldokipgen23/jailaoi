<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\ArtistKyc;
use App\Models\General_Setting;
use App\Models\Music;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EarningsController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return redirect()->route('user.login');

            $artist = Artist::where('user_id', $user->id)->first();
            $stats  = $this->getStats($artist);
            $eligibility = $artist ? $this->isEligibleForWithdrawal($artist) : null;

            // JAILAOI: Per-song earnings breakdown
            $songBreakdown = [];
            if ($artist) {
                $rows = DB::table('tbl_artist_earnings')
                    ->select('content_id', 'content_type',
                        DB::raw('COUNT(*) as play_count'),
                        DB::raw('SUM(amount) as earned'))
                    ->where('artist_id', $artist->id)
                    ->groupBy('content_id', 'content_type')
                    ->orderByDesc('play_count')
                    ->limit(20)
                    ->get();

                foreach ($rows as $row) {
                    $title = '—';
                    if ($row->content_type == 1 || $row->content_type == 8) {
                        $music = Music::find($row->content_id);
                        if ($music) $title = $music->title;
                    } elseif ($row->content_type == 2) {
                        $song = DB::table('tbl_song')->where('id', $row->content_id)->first();
                        if ($song) $title = $song->title ?? '—';
                    } else {
                        $content = DB::table('tbl_content')->where('id', $row->content_id)->first();
                        if ($content) $title = $content->title ?? '—';
                    }
                    $songBreakdown[] = [
                        'title'      => $title,
                        'play_count' => (int) $row->play_count,
                        'earned'     => round((float) $row->earned, 4),
                    ];
                }
            }

            // JAILAOI: Monthly earnings trend (last 6 months)
            $monthlyTrend = [];
            if ($artist) {
                $rows = DB::table('tbl_artist_earnings')
                    ->select(
                        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                        DB::raw('SUM(amount) as earned'),
                        DB::raw('COUNT(*) as plays')
                    )
                    ->where('artist_id', $artist->id)
                    ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->keyBy('month');

                for ($i = 5; $i >= 0; $i--) {
                    $key   = now()->subMonths($i)->format('Y-m');
                    $label = now()->subMonths($i)->format('M Y');
                    $monthlyTrend[] = [
                        'label'  => $label,
                        'earned' => round((float) ($rows[$key]->earned ?? 0), 2),
                        'plays'  => (int) ($rows[$key]->plays ?? 0),
                    ];
                }
            }

            $withdrawals = $artist
                ? WithdrawalRequest::where('artist_id', $artist->id)->orderByDesc('id')->get()
                : collect();

            return view('user.earnings.index', [
                'artist'        => $artist,
                'stats'         => $stats,
                'withdrawals'   => $withdrawals,
                'songBreakdown' => $songBreakdown,
                'monthlyTrend'  => $monthlyTrend,
                'eligibility'   => $eligibility,
                'user'          => $user,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function requestWithdrawal(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return response()->json(['status' => 400, 'errors' => 'Not authenticated']);

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) {
                return response()->json(['status' => 400, 'errors' => 'Artist profile not found']);
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|max:50',
                'payment_details' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            // JAILAOI: Check monetization eligibility
            $eligibility = $this->isEligibleForWithdrawal($artist);
            if (!$eligibility['eligible']) {
                return response()->json(['status' => 400, 'errors' => implode(' ', $eligibility['reasons'])]);
            }

            $stats = $this->getStats($artist);
            $min = (float) $this->setting('min_withdrawal_amount', 10);
            $amount = (float) $request->amount;

            if ($amount < $min) {
                return response()->json(['status' => 400, 'errors' => 'Minimum withdrawal is ' . $min]);
            }
            if ($amount > $stats['available']) {
                return response()->json(['status' => 400, 'errors' => 'Amount exceeds available balance']);
            }

            WithdrawalRequest::create([
                'artist_id' => $artist->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
                'status' => 'pending',
            ]);

            return response()->json(['status' => 200, 'success' => 'Withdrawal request submitted']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    private function getStats($artist)
    {
        $stats = [
            'currency' => $this->setting('payout_currency', 'USD'),
            'rate' => (float) $this->setting('payout_rate_per_stream', 0),
            'min_withdrawal' => (float) $this->setting('min_withdrawal_amount', 10),
            'total_plays' => 0,
            'total_earned' => 0.0,
            'paid_out' => 0.0,
            'pending' => 0.0,
            'available' => 0.0,
        ];
        if (!$artist) return $stats;

        $stats['total_plays'] = ArtistEarning::where('artist_id', $artist->id)->count();
        $stats['total_earned'] = round((float) ArtistEarning::where('artist_id', $artist->id)->sum('amount'), 4);

        $stats['paid_out'] = round((float) WithdrawalRequest::where('artist_id', $artist->id)
            ->where('status', 'paid')->sum('amount'), 2);

        $stats['pending'] = round((float) WithdrawalRequest::where('artist_id', $artist->id)
            ->whereIn('status', ['pending', 'approved'])->sum('amount'), 2);

        $stats['available'] = round(max(0, $stats['total_earned'] - $stats['paid_out'] - $stats['pending']), 4);

        return $stats;
    }

    private function isEligibleForWithdrawal($artist)
    {
        $result = [
            'eligible' => true,
            'kyc_approved' => false,
            'reasons' => [],
        ];

        // 1. KYC must be approved
        $kyc = ArtistKyc::where('artist_id', $artist->id)->where('status', 'approved')->first();
        if ($kyc) {
            $result['kyc_approved'] = true;
        } else {
            $result['eligible'] = false;
            $result['reasons'][] = 'KYC not approved. Please complete KYC verification first.';
        }

        $user = $artist->user;

        // 2. Total plays >= min_streams_for_payout
        $minPlays = (int) $this->setting('min_streams_for_payout', 50);
        $totalPlays = ArtistEarning::where('artist_id', $artist->id)->count();
        if ($totalPlays < $minPlays) {
            $result['eligible'] = false;
            $result['reasons'][] = "Minimum {$minPlays} plays required (you have {$totalPlays}).";
        }

        // 3. Total earned >= min_earnings_for_payout
        $minEarnings = (float) $this->setting('min_earnings_for_payout', 5.00);
        $totalEarned = (float) ArtistEarning::where('artist_id', $artist->id)->sum('amount');
        if ($totalEarned < $minEarnings) {
            $result['eligible'] = false;
            $result['reasons'][] = "Minimum {$minEarnings} earned required (you have {$totalEarned}).";
        }

        // 4. Account age >= min_account_days_for_payout days
        if ($user && $user->created_at) {
            $minDays = (int) $this->setting('min_account_days_for_payout', 30);
            $accountAge = $user->created_at->diffInDays(now());
            if ($accountAge < $minDays) {
                $result['eligible'] = false;
                $result['reasons'][] = "Account must be at least {$minDays} days old (currently {$accountAge} days).";
            }
        }

        return $result;
    }

    private function setting($key, $default = null)
    {
        $row = General_Setting::where('key', $key)->first();
        return $row ? $row->value : $default;
    }
}
