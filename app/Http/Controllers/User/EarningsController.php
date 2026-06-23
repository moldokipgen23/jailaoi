<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\ArtistKyc;
use App\Models\General_Setting;
use App\Models\MonetizationApplication;
use App\Models\Music;
use App\Models\Transaction;
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
            $monetizationApproved = $artist
                ? MonetizationApplication::where('artist_id', $artist->id)->where('status', 'approved')->exists()
                : false;
            $kycApproved = $artist
                ? ArtistKyc::where('user_id', $user->id)->where('status', 'approved')->exists()
                : false;

            $earningsModel = General_Setting::where('key', 'earnings_model')->value('value') ?? 'pool';

            // JAILAOI: Per-song earnings breakdown
            $songBreakdown = [];
            if ($artist) {
                $query = DB::table('tbl_artist_earnings')
                    ->select('content_id', 'content_type',
                        DB::raw('COUNT(*) as play_count'),
                        DB::raw('SUM(amount) as earned'))
                    ->where('artist_id', $artist->id);

                if ($earningsModel === 'pool') {
                    $query->whereNotNull('settled_month');
                }

                $rows = $query
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
                $mq = DB::table('tbl_artist_earnings')
                    ->select(
                        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                        DB::raw('SUM(amount) as earned'),
                        DB::raw('COUNT(*) as plays')
                    )
                    ->where('artist_id', $artist->id)
                    ->where('created_at', '>=', now()->subMonths(5)->startOfMonth());

                if ($earningsModel === 'pool') {
                    $mq->whereNotNull('settled_month');
                }

                $rows = $mq
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

            $currentStreamValue = 0;
            $currentMonthStr = now()->format('Y-m');
            if ($earningsModel === 'pool') {
                $lastSettlement = DB::table('tbl_earnings_settlements')
                    ->orderByDesc('month')
                    ->first();
                $currentStreamValue = $lastSettlement ? (float) $lastSettlement->rate_per_stream : 0;
            }

            return view('user.earnings.index', [
                'artist'        => $artist,
                'stats'         => $stats,
                'withdrawals'   => $withdrawals,
                'songBreakdown' => $songBreakdown,
                'monthlyTrend'  => $monthlyTrend,
                'monetizationApproved' => $monetizationApproved,
                'kycApproved'    => $kycApproved,
                'eligibility'   => $monetizationApproved && $kycApproved ? ['eligible' => true] : ['eligible' => false, 'monetizationApproved' => $monetizationApproved, 'kycApproved' => $kycApproved],
                'user'          => $user,
                'earningsModel' => $earningsModel,
                'currentStreamValue' => $currentStreamValue,
                'rate'          => $stats['rate'] ?? 0,
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

            // Check monetization + KYC approval
            $monetizationApproved = MonetizationApplication::where('artist_id', $artist->id)->where('status', 'approved')->exists();
            $kycApproved = ArtistKyc::where('user_id', $user->id)->where('status', 'approved')->exists();

            if (!$monetizationApproved) {
                return response()->json(['status' => 400, 'errors' => 'Monetization not approved. Please apply first.']);
            }
            if (!$kycApproved) {
                return response()->json(['status' => 400, 'errors' => 'KYC not approved. Please complete KYC verification first.']);
            }

            // Enforce min streams threshold
            $minStreams = (int) $this->setting('min_streams_for_payout', 1000);
            $totalPlays = ArtistEarning::where('artist_id', $artist->id)->count();
            if ($totalPlays < $minStreams) {
                return response()->json(['status' => 400, 'errors' => 'You need at least ' . number_format($minStreams) . ' streams to withdraw. You have ' . number_format($totalPlays) . '.']);
            }

            // Enforce min earnings threshold (settled only in pool mode)
            $minEarnings = (float) $this->setting('min_earnings_for_payout', 200);
            $earningsModel = $this->setting('earnings_model', 'pool');
            $totalEarned = $earningsModel === 'pool'
                ? (float) ArtistEarning::where('artist_id', $artist->id)->whereNotNull('settled_month')->sum('amount')
                : (float) ArtistEarning::where('artist_id', $artist->id)->sum('amount');
            if ($totalEarned < $minEarnings) {
                return response()->json(['status' => 400, 'errors' => 'You need at least ₹' . number_format($minEarnings, 0) . ' in settled earnings to withdraw. You have ₹' . number_format($totalEarned, 2) . '.']);
            }

            $amount = (float) $request->amount;
            $min = (float) $this->setting('min_withdrawal_amount', 200);

            if ($amount < $min) {
                return response()->json(['status' => 400, 'errors' => 'Minimum withdrawal is ₹' . number_format($min, 0)]);
            }

            // Atomic hold: lock row, check balance, deduct, create request
            DB::beginTransaction();
            try {
                // Lock the artist row to prevent race conditions
                $artist = Artist::where('id', $artist->id)->lockForUpdate()->first();

                if ((float) $artist->wallet_balance < $amount) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'errors' => 'Insufficient wallet balance']);
                }

                // Block if there's already a pending or approved withdrawal
                $existing = WithdrawalRequest::where('artist_id', $artist->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();
                if ($existing) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'errors' => 'You already have a pending withdrawal request. Complete or cancel it first.']);
                }

                // Deduct from wallet (atomic)
                $artist->wallet_balance = round((float) $artist->wallet_balance - $amount, 4);
                $artist->save();

                // Create withdrawal request
                WithdrawalRequest::create([
                    'artist_id' => $artist->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => $request->payment_method,
                    'payment_details' => $request->payment_details,
                    'status' => 'pending',
                ]);

                DB::commit();
                return response()->json(['status' => 200, 'success' => 'Withdrawal request submitted. Balance held.']);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    private function getStats($artist)
    {
        $model = $this->setting('earnings_model', 'pool');
        $stats = [
            'currency' => $this->setting('payout_currency', 'USD'),
            'rate' => (float) $this->setting('payout_rate_per_stream', 0),
            'min_withdrawal' => (float) $this->setting('min_withdrawal_amount', 200),
            'total_plays' => 0,
            'total_earned' => 0.0,
            'wallet_balance' => 0.0,
            'paid_out' => 0.0,
            'pending' => 0.0,
            'available' => 0.0,
            'pending_plays' => 0,
            'has_pending_withdrawal' => false,
            'earnings_model' => $model,
        ];
        if (!$artist) return $stats;

        $stats['total_plays'] = ArtistEarning::where('artist_id', $artist->id)->count();

        if ($model === 'pool') {
            $stats['total_earned'] = round((float) ArtistEarning::where('artist_id', $artist->id)
                ->whereNotNull('settled_month')
                ->sum('amount'), 4);
            $stats['pending_plays'] = ArtistEarning::where('artist_id', $artist->id)
                ->whereNull('settled_month')
                ->count();
        } else {
            $stats['total_earned'] = round((float) ArtistEarning::where('artist_id', $artist->id)->sum('amount'), 4);
        }

        // Wallet balance is the single source of truth for withdrawable funds
        $stats['wallet_balance'] = round((float) $artist->wallet_balance, 4);

        $stats['paid_out'] = round((float) WithdrawalRequest::where('artist_id', $artist->id)
            ->where('status', 'paid')->sum('amount'), 2);

        $stats['pending'] = round((float) WithdrawalRequest::where('artist_id', $artist->id)
            ->whereIn('status', ['pending', 'approved'])->sum('amount'), 2);

        $stats['has_pending_withdrawal'] = WithdrawalRequest::where('artist_id', $artist->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        // available = wallet_balance (pending/approved already deducted from wallet at request time)
        $stats['available'] = round(max(0, $stats['wallet_balance']), 4);

        return $stats;
    }

    private function setting($key, $default = null)
    {
        $row = General_Setting::where('key', $key)->first();
        return $row ? $row->value : $default;
    }
}
