<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\ArtistKyc;
use App\Models\General_Setting;
use App\Models\MonetizationApplication;
use App\Models\Music;
use App\Models\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonetizationController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return redirect()->route('user.login');

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) return redirect()->route('user.dashboard');

            $stats = $this->getArtistStats($artist, $user);
            $settings = $this->loadSettings();
            $strict = (bool) ($settings['monetization_strict_eligibility'] ?? true);

            $eligibility = $this->checkEligibility($stats, $settings);

            $application = MonetizationApplication::where('artist_id', $artist->id)->latest()->first();
            $kyc = ArtistKyc::where('user_id', $user->id)->latest()->first();

            $stage = 'not_eligible';
            if ($application) {
                if ($application->status === 'approved') $stage = 'approved';
                elseif ($application->status === 'rejected') $stage = 'rejected';
                else $stage = 'applied';
            } elseif ($eligibility['eligible'] || !$strict) {
                $stage = 'eligible';
            }

            return view('user.monetization.index', array_merge(
                compact('artist', 'user', 'eligibility', 'application', 'kyc', 'stage', 'settings', 'strict'),
                $stats
            ));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function apply(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return response()->json(['status' => 400, 'errors' => 'Not authenticated']);

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) return response()->json(['status' => 400, 'errors' => 'Artist profile not found']);

            $existing = MonetizationApplication::where('artist_id', $artist->id)
                ->where('status', 'pending')->first();
            if ($existing) return response()->json(['status' => 400, 'errors' => 'You already have a pending application.']);

            $approved = MonetizationApplication::where('artist_id', $artist->id)
                ->where('status', 'approved')->first();
            if ($approved) return response()->json(['status' => 400, 'errors' => 'Monetization already approved.']);

            $stats = $this->getArtistStats($artist, $user);
            $settings = $this->loadSettings();
            $strict = (bool) ($settings['monetization_strict_eligibility'] ?? true);

            if ($strict) {
                $eligibility = $this->checkEligibility($stats, $settings);
                if (!$eligibility['eligible']) {
                    return response()->json(['status' => 400, 'errors' => 'You do not meet all eligibility requirements.']);
                }
            }

            MonetizationApplication::create([
                'artist_id' => $artist->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'snapshot_plays' => $stats['totalPlays'],
                'snapshot_followers' => $stats['followers'],
                'snapshot_monthly_plays' => $stats['monthlyPlays'],
                'snapshot_tracks' => $stats['tracks'],
                'snapshot_earnings' => $stats['totalEarned'],
                'applied_at' => now(),
            ]);

            return response()->json(['status' => 200, 'success' => 'Your monetization application has been submitted. We will review it within 3-5 business days.']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    private function getArtistStats($artist, $user)
    {
        return [
            'totalPlays' => ArtistEarning::where('artist_id', $artist->id)->count(),
            'monthlyPlays' => ArtistEarning::where('artist_id', $artist->id)
                ->where('created_at', '>=', now()->startOfMonth())->count(),
            'followers' => Subscriber::where('to_user_id', $user->id)->where('status', 1)->count(),
            'tracks' => Music::where('artist_id', $artist->id)->where('status', 1)->count(),
            'accountAgeDays' => $user->created_at ? $user->created_at->diffInDays(now()) : 0,
            'totalEarned' => (float) ArtistEarning::where('artist_id', $artist->id)->sum('amount'),
        ];
    }

    private function checkEligibility($stats, $settings)
    {
        $checks = [
            [
                'label' => 'Total Plays',
                'current' => $stats['totalPlays'],
                'required' => (int) $settings['eligibility_min_plays'],
                'passed' => $stats['totalPlays'] >= (int) $settings['eligibility_min_plays'],
            ],
            [
                'label' => 'Followers',
                'current' => $stats['followers'],
                'required' => (int) $settings['eligibility_min_followers'],
                'passed' => $stats['followers'] >= (int) $settings['eligibility_min_followers'],
            ],
            [
                'label' => 'Plays This Month',
                'current' => $stats['monthlyPlays'],
                'required' => (int) $settings['eligibility_min_monthly_plays'],
                'passed' => $stats['monthlyPlays'] >= (int) $settings['eligibility_min_monthly_plays'],
            ],
            [
                'label' => 'Tracks Uploaded',
                'current' => $stats['tracks'],
                'required' => (int) $settings['eligibility_min_tracks'],
                'passed' => $stats['tracks'] >= (int) $settings['eligibility_min_tracks'],
            ],
            [
                'label' => 'Account Age (Days)',
                'current' => $stats['accountAgeDays'],
                'required' => (int) $settings['eligibility_min_account_days'],
                'passed' => $stats['accountAgeDays'] >= (int) $settings['eligibility_min_account_days'],
            ],
        ];

        $eligible = true;
        foreach ($checks as $check) {
            if (!$check['passed']) {
                $eligible = false;
                break;
            }
        }

        return [
            'eligible' => $eligible,
            'checks' => $checks,
        ];
    }

    private function loadSettings()
    {
        $keys = [
            'eligibility_min_plays',
            'eligibility_min_followers',
            'eligibility_min_monthly_plays',
            'eligibility_min_tracks',
            'eligibility_min_account_days',
            'monetization_strict_eligibility',
        ];
        $settings = [];
        foreach ($keys as $key) {
            $row = General_Setting::where('key', $key)->first();
            $settings[$key] = $row ? $row->value : '0';
        }
        return $settings;
    }
}
