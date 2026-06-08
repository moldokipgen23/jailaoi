<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Common;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ArtistController extends Controller
{
    private $folder_user = "user";
    private $folder_artist = "artist";
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
            $artist['total_followers'] = $artist->user_id ? Subscriber::where('to_user_id', $artist->user_id)->count() : 0;
            $artist['monthly_listeners'] = $artist->user_id ? Subscriber::where('to_user_id', $artist->user_id)->where('created_at', '>=', now()->subDays(30))->count() : 0;

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

            $this->common->imageNameToUrl($content, 'image', 'radio');

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

            $total_content = \App\Models\Song::where('artist_id', 'LIKE', "%{$artist->id}%")->count();
            $total_followers = Subscriber::where('to_user_id', $user->id)->count();
            $total_views = \App\Models\Song::where('artist_id', 'LIKE', "%{$artist->id}%")->sum('total_play');

            $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [[
                'artist' => $artist->toArray(),
                'total_content' => $total_content,
                'total_followers' => $total_followers,
                'total_views' => $total_views,
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
