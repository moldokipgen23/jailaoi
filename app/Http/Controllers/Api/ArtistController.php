<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Common;
use App\Models\Content;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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

            $data = Artist::with('user')->where('status', 1)->latest();

            $total = $data->count();
            $offset = $this->page_limit * ($page_no - 1);
            $artists = $data->skip($offset)->take($this->page_limit)->get();

            $this->common->imageNameToUrl($artists, 'image', $this->folder_artist);

            $result = [];
            foreach ($artists as $artist) {
                $is_following = 0;
                if ($user_id > 0) {
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
                    'total_followers' => Subscriber::where('to_user_id', $artist->user_id)->count(),
                    'total_content' => Content::where('channel_id', $artist->user?->channel_id ?? '')->count(),
                    'is_following' => $is_following,
                ];
            }

            $more_page = ($page_no * $this->page_limit) < $total ? true : false;
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

            $artist_id = $request->artist_id;
            $artist = Artist::with('user')->where('id', $artist_id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);
            $artist['total_followers'] = Subscriber::where('to_user_id', $artist->user_id)->count();

            $login_user_id = $request->login_user_id ?? 0;
            $artist['is_following'] = 0;
            if ($login_user_id > 0) {
                $follow = Subscriber::where('user_id', $login_user_id)->where('to_user_id', $artist->user_id)->first();
                if ($follow) $artist['is_following'] = 1;
            }

            if ($artist->user) {
                $this->common->imageNameToUrl([$artist->user], 'image', $this->folder_user);
                $artist['channel_name'] = $artist->user->channel_name;
                $artist['channel_image'] = $artist->user->image;
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

            $artist_id = $request->artist_id;
            $artist = Artist::with('user')->where('id', $artist_id)->first();
            if (!$artist || !$artist->user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $page_no = $request->page_no ?? 1;
            $data = Content::where('channel_id', $artist->user->channel_id)
                ->where('status', 1);

            if (!$this->common->isContentTypeEnabled(1)) {
                $data = $data->where('content_type', '!=', 1);
            }

            $data = $data->latest();

            $total = $data->count();
            $offset = $this->page_limit * ($page_no - 1);
            $content = $data->skip($offset)->take($this->page_limit)->get();

            $this->common->imageNameToUrl($content, 'portrait_img', 'content');
            $this->common->imageNameToUrl($content, 'landscape_img', 'content');

            $more_page = ($page_no * $this->page_limit) < $total ? true : false;
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
                'bio' => 'nullable|string',
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

            $req = new ArtistRequest();
            $req->user_id = $request->user_id;
            $req->artist_name = $request->artist_name;
            $req->bio = $request->bio ?? '';
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

            $total_content = Content::where('channel_id', $user->channel_id)->count();
            $total_followers = Subscriber::where('to_user_id', $user->id)->count();
            $total_views = Content::where('channel_id', $user->channel_id)->sum('total_view');
            $total_likes = Content::where('channel_id', $user->channel_id)->sum('total_like');

            $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [[
                'artist' => $artist->toArray(),
                'total_content' => $total_content,
                'total_followers' => $total_followers,
                'total_views' => $total_views,
                'total_likes' => $total_likes,
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
