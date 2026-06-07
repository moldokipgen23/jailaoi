<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\General_Setting;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EarningsController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return redirect()->route('user.login');

            $artist = Artist::where('user_id', $user->id)->first();
            $stats = $this->getStats($artist);

            $withdrawals = $artist
                ? WithdrawalRequest::where('artist_id', $artist->id)->orderByDesc('id')->get()
                : collect();

            return view('user.earnings.index', [
                'artist' => $artist,
                'stats' => $stats,
                'withdrawals' => $withdrawals,
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

    private function setting($key, $default = null)
    {
        $row = General_Setting::where('key', $key)->first();
        return $row ? $row->value : $default;
    }
}
