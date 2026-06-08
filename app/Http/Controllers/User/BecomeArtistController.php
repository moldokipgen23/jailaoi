<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BecomeArtistController extends Controller
{
    public function index(Request $request)
    {
        // JAILAOI: Check if user is already logged in via user guard
        if (Auth::guard('user')->check()) {
            $user = Auth::guard('user')->user();

            // Already an artist → redirect to dashboard
            if ($user->role === 'artist') {
                return redirect()->route('user.dashboard');
            }

            // Check for existing artist request
            $existingRequest = ArtistRequest::where('user_id', $user->id)->latest()->first();
            if ($existingRequest) {
                return view('user.become_artist.index', [
                    'step' => 'status',
                    'request' => $existingRequest,
                    'user' => $user,
                ]);
            }

            // Show the application form
            return view('user.become_artist.index', [
                'step' => 'form',
                'user' => $user,
            ]);
        }

        // Not logged in → show login form
        return view('user.become_artist.index', ['step' => 'login']);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:4',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['status' => 400, 'errors' => 'Invalid email or password.']);
            }

            // Log in with the user guard (no role restriction)
            Auth::guard('user')->login($user);

            $user->last_login_at = now();
            $user->save();

            return response()->json(['status' => 200, 'success' => 'Logged in successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'artist_name' => 'required|string|max:100',
                'bio' => 'required|string|min:20|max:2000',
                'artist_types' => 'required|array|min:1',
                'artist_types.*' => 'in:music,podcast',
            ], [
                'artist_types.required' => 'Please select at least one content type (Music or Podcast).',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                ]);
            }

            $user = Auth::guard('user')->user();
            if (!$user) {
                return response()->json(['status' => 400, 'errors' => 'Please log in first.']);
            }

            if ($user->role === 'artist') {
                return response()->json(['status' => 400, 'errors' => 'You are already an artist.']);
            }

            $existing = ArtistRequest::where('user_id', $user->id)->where('status', 'pending')->first();
            if ($existing) {
                return response()->json(['status' => 400, 'errors' => 'You already have a pending application.']);
            }

            ArtistRequest::create([
                'user_id' => $user->id,
                'artist_name' => $request->artist_name,
                'bio' => $request->bio,
                'artist_types' => implode(',', $request->artist_types),
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => 200,
                'success' => 'Your application has been submitted. We will review it shortly.',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
