<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistKyc;
use App\Models\ArtistRequest;
use App\Models\Common;
use App\Models\Music;
use App\Models\Song;
use App\Models\Podcast;
use App\Models\ArtistEarning;
use App\Models\MonetizationApplication;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\User_Action;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class ArtistController extends Controller
{
    private $folder_user = "images/user";
    private $folder_artist = "images/artist";
    public $common;
    public $page_limit;

    public function __construct()
    {
        $this->common = new Common;
        $this->page_limit = env('PAGE_LIMIT', 20);
    }

    public function get_artist_list(Request $request)
    {
        try {
            $page_no = $request->page_no ?? 1;
            $user_id = $request->user_id ?? 0;

            $data = Artist::where('status', 1)->latest();
            if (method_exists($data, 'with')) {
                $data = $data->with('user');
            }

            $total = $data->count();
            $offset = $this->page_limit * ($page_no - 1);
            $artists = $data->skip($offset)->take($this->page_limit)->get();

            $this->common->imageNameToUrl($artists, 'image', $this->folder_artist);

            $result = [];
            foreach ($artists as $artist) {
                $is_following = 0;
                if ($user_id > 0 && $artist->user_id) {
                    $follow = Subscriber::where('user_id', $user_id)
                        ->where('to_user_id', $artist->user_id)
                        ->first();
                    if ($follow) $is_following = 1;
                }
                $result[] = [
                    'id' => $artist->id,
                    'user_id' => $artist->user_id,
                    'name' => $artist->name,
                    'image' => $artist->image,
                    'bio' => $artist->bio,
                    'total_followers' => $artist->user_id ? Subscriber::where('to_user_id', $artist->user_id)->count() : 0,
                    'is_following' => $is_following,
                ];
            }

            $more_page = ($page_no * $this->page_limit) < $total;
            $pagination = [
                'total_rows' => $total,
                'total_page' => ceil($total / $this->page_limit),
                'current_page' => $page_no,
                'more_page' => $more_page,
            ];

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_artist_profile(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $artist = Artist::where('id', $request->artist_id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);

            // Real followers count
            $artist['total_followers'] = $artist->user_id
                ? Subscriber::where('to_user_id', $artist->user_id)->count()
                : 0;

            // Real monthly listeners: unique users who played this artist's content in last 30 days
            $artist['monthly_listeners'] = User_Action::where('artist_id', $artist->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id');

            // Total content count (radio + music + podcast)
            $songCount  = Song::where('artist_id', $artist->id)->where('status', 1)->count();
            $musicCount = Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->where('status', 1)->count();
            $podcastCount = Podcast::where('artist_id', $artist->id)->where('status', 1)->count();
            $artist['total_songs'] = $songCount + $musicCount + $podcastCount;

            // Total plays across all content
            $artist['total_plays'] = Song::where('artist_id', $artist->id)->sum('total_play')
                + Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->sum('total_play')
                + Podcast::where('artist_id', $artist->id)->sum('total_play');

            // Verified if KYC approved
            $artist['is_verified'] = ArtistKyc::where('artist_id', $artist->id)
                ->where('status', 'approved')
                ->exists() ? 1 : 0;

            $login_user_id = $request->login_user_id ?? 0;
            $artist['is_following'] = 0;
            if ($login_user_id > 0 && $artist->user_id) {
                $follow = Subscriber::where('user_id', $login_user_id)->where('to_user_id', $artist->user_id)->first();
                if ($follow) $artist['is_following'] = 1;
            }

            if ($artist->user_id) {
                $user = User::find($artist->user_id);
                if ($user) {
                    $this->common->imageNameToUrl([$user], 'image', $this->folder_user);
                    $artist['channel_name'] = $user->channel_name ?? '';
                    $artist['channel_image'] = $user->image ?? '';
                }
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$artist]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_artist_content(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $artist = Artist::where('id', $request->artist_id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $page_no = $request->page_no ?? 1;
            $content_model = null;
            $channel_id = null;

            if ($artist->user_id) {
                $user = User::find($artist->user_id);
                $channel_id = $user->channel_id ?? null;
            }

            if (!$channel_id) {
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [], [
                    'total_rows' => 0, 'total_page' => 1, 'current_page' => 1, 'more_page' => false,
                ]);
            }

            $content = collect();
            $total = 0;

            $songs = \App\Models\Song::where('status', 1)
                ->where(function($q) use ($channel_id, $artist) {
                    $q->where('artist_id', 'LIKE', "%{$artist->id}%")
                      ->orWhere('artist_id', $artist->id);
                });

            $total = $songs->count();
            $offset = $this->page_limit * ($page_no - 1);
            $content = $songs->skip($offset)->take($this->page_limit)->get();

            $this->common->imageNameToUrl($content, 'image', 'images/radio');

            $more_page = ($page_no * $this->page_limit) < $total;
            $pagination = [
                'total_rows' => $total,
                'total_page' => ceil($total / $this->page_limit),
                'current_page' => $page_no,
                'more_page' => $more_page,
            ];

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $content->toArray(), $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function apply_artist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_name' => 'required|min:2',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $existing = ArtistRequest::where('user_id', $request->user_id)->where('status', 'pending')->first();
            if ($existing) {
                return $this->common->API_Response(400, 'You already have a pending request');
            }

            $existingArtist = Artist::where('user_id', $request->user_id)->first();
            if ($existingArtist) {
                return $this->common->API_Response(400, 'You are already an artist');
            }

            // JAILAOI: save artist_types (comma-separated: music,podcast)
            $artistTypes = 'music';
            if ($request->filled('artist_types')) {
                $types = is_array($request->artist_types)
                    ? implode(',', $request->artist_types)
                    : $request->artist_types;
                $artistTypes = $types;
            }

            $req = new ArtistRequest();
            $req->user_id = $request->user_id;
            $req->artist_name = $request->artist_name;
            $req->bio = $request->bio ?? '';
            $req->artist_types = $artistTypes;
            $req->status = 'pending';
            $req->save();

            return $this->common->API_Response(200, 'Artist request submitted successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_artist_request_status(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $req = ArtistRequest::where('user_id', $request->user_id)->latest()->first();
            if (!$req) {
                $user = User::find($request->user_id);
                if ($user && $user->role == 'artist') {
                    return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [
                        'status' => 'approved',
                        'is_artist' => true,
                    ]);
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [
                    'status' => null,
                    'is_artist' => false,
                ]);
            }

            $is_artist = Artist::where('user_id', $request->user_id)->exists();

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [
                'status' => $req->status,
                'is_artist' => $is_artist,
                'admin_note' => $req->admin_note ?? '',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function follow_artist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            if ($request->user_id == $request->artist_user_id) {
                return $this->common->API_Response(400, 'Cannot follow yourself');
            }

            $existing = Subscriber::where('user_id', $request->user_id)
                ->where('to_user_id', $request->artist_user_id)
                ->first();

            if ($existing) {
                return $this->common->API_Response(400, 'Already following this artist');
            }

            $sub = new Subscriber();
            $sub->user_id = $request->user_id;
            $sub->to_user_id = $request->artist_user_id;
            $sub->status = 1;
            $sub->save();

            return $this->common->API_Response(200, 'Followed successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function unfollow_artist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            Subscriber::where('user_id', $request->user_id)
                ->where('to_user_id', $request->artist_user_id)
                ->delete();

            return $this->common->API_Response(200, 'Unfollowed successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function generate_portal_token(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $token = Str::random(48);
            // Store token in cache for 5 minutes keyed to user_id
            Cache::put("portal_token:{$token}", $user->id, now()->addMinutes(5));

            return response()->json([
                'status' => 200,
                'message' => 'Token generated',
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_artist_dashboard(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, 'You are not an artist');
            }

            $total_followers = Subscriber::where('to_user_id', $user->id)->count();

            // Plays across all content types
            $total_views = Song::where('artist_id', $artist->id)->sum('total_play')
                + Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->sum('total_play')
                + Podcast::where('artist_id', $artist->id)->sum('total_play');

            // Content counts
            $song_count    = Song::where('artist_id', $artist->id)->where('status', 1)->count();
            $music_count   = Music::whereRaw("FIND_IN_SET(?, artist_id)", [$artist->id])->where('status', 1)->count();
            $podcast_count = Podcast::where('artist_id', $artist->id)->where('status', 1)->count();
            $total_content = $song_count + $music_count + $podcast_count;

            // Monthly listeners — unique users who played in last 30 days
            $monthly_listeners = User_Action::where('artist_id', $artist->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id');

            // Verified status
            $is_verified = ArtistKyc::where('artist_id', $artist->id)
                ->where('status', 'approved')
                ->exists() ? 1 : 0;

            // Earnings balance
            $total_earned  = round((float) ArtistEarning::where('artist_id', $artist->id)->sum('amount'), 2);
            $paid_out      = round((float) WithdrawalRequest::where('artist_id', $artist->id)->where('status', 'approved')->sum('amount'), 2);
            $pending_payout = round((float) WithdrawalRequest::where('artist_id', $artist->id)->where('status', 'pending')->sum('amount'), 2);
            $available_balance = round(max(0, $total_earned - $paid_out - $pending_payout), 2);

            // Monetization — full status
            $monetization_row = MonetizationApplication::where('artist_id', $artist->id)
                ->latest()->first();
            $monetization_status = $monetization_row ? $monetization_row->status : null;
            $monetization_approved = $monetization_status === 'approved' ? 1 : 0;

            // KYC — full status
            $kyc_row = ArtistKyc::where('artist_id', $artist->id)->latest()->first();
            $kyc_status = $kyc_row ? $kyc_row->status : null;

            // Recent 5 tracks
            $recent_tracks = Song::where('artist_id', $artist->id)
                ->where('status', 1)
                ->orderByDesc('id')
                ->take(5)
                ->get(['id', 'title', 'image', 'total_play']);
            $this->common->imageNameToUrl($recent_tracks, 'image', 'images/radio');

            $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [[
                'artist'                => $artist->toArray(),
                'total_content'         => $total_content,
                'total_followers'       => $total_followers,
                'total_views'           => $total_views,
                'monthly_listeners'     => $monthly_listeners,
                'is_verified'           => $is_verified,
                'total_earned'          => $total_earned,
                'available_balance'     => $available_balance,
                'monetization_approved' => $monetization_approved,
                'monetization_status'   => $monetization_status,
                'kyc_status'            => $kyc_status,
                'recent_tracks'         => $recent_tracks->toArray(),
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
