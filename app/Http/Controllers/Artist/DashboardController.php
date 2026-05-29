<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return redirect()->route('artist.login')->with('error', 'Artist profile not found');
            }

            $params['artist'] = $artist;
            $params['songCount'] = Song::where('artist_id', $artist->id)->count();
            $params['followerCount'] = Follower::where('artist_id', $artist->id)->count();
            $params['totalPlays'] = Song::where('artist_id', $artist->id)->sum('total_play');
            $params['recentSongs'] = Song::where('artist_id', $artist->id)->with('artist')->latest()->take(5)->get();

            return view('artist.dashboard', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
