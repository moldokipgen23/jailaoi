<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Follower;
use App\Models\Play;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class ArtistController extends Controller
{
    private $folder_user = "user";
    private $folder_artist = "artist";
    private $folder_song = "song";
    public $common;
    public $page_limit;

    public function __construct()
    {
        $this->common = new Common;
        $this->page_limit = env('PAGE_LIMIT');
    }

    // ==================== Public User Profile ====================
    public function get_user_profile(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user_id = $request->user_id;
            $user = User::where('id', $user_id)->first();
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $this->common->imageNameToUrl([$user], 'image', $this->folder_user);
            $user['is_buy'] = $this->common->is_any_package_buy($user->id);
            $user['total_songs'] = Song::where('artist_id', $user->artist?->id)->count();

            $artist = Artist::where('user_id', $user->id)->first();
            if ($artist) {
                $user['artist_id'] = $artist->id;
                $user['artist_name'] = $artist->name;
                $user['artist_image'] = $this->common->Get_Image($this->folder_artist, $artist->image);
                $user['artist_bio'] = $artist->bio;
                $user['follower_count'] = Follower::where('artist_id', $artist->id)->count();
                $user['following_count'] = Follower::where('user_id', $user->id)->count();
            } else {
                $user['artist_id'] = 0;
                $user['artist_name'] = "";
                $user['artist_image'] = "";
                $user['artist_bio'] = "";
                $user['follower_count'] = 0;
                $user['following_count'] = Follower::where('user_id', $user->id)->count();
            }

            $user['role'] = $user->role ?? 'user';

            // Check if logged-in user follows this artist
            $login_user_id = $request->login_user_id ?? 0;
            $user['is_following'] = 0;
            if ($login_user_id > 0 && $artist) {
                $follow = Follower::where('user_id', $login_user_id)->where('artist_id', $artist->id)->first();
                if ($follow) {
                    $user['is_following'] = 1;
                }
            }

            // Artist request status
            $user['artist_request_status'] = null;
            $req = ArtistRequest::where('user_id', $user->id)->first();
            if ($req) {
                $user['artist_request_status'] = $req->status;
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$user]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Public Artist Profile ====================
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
            $artist['total_songs'] = Song::where('artist_id', $artist->id)->count();
            $artist['total_plays'] = Play::where('type', 1)->whereIn('content_id', Song::where('artist_id', $artist->id)->pluck('id'))->count();
            $artist['follower_count'] = Follower::where('artist_id', $artist->id)->count();

            // User info if linked
            if ($artist->user) {
                $this->common->imageNameToUrl([$artist->user], 'image', $this->folder_user);
                $artist['user_name'] = $artist->user->full_name;
                $artist['user_image'] = $artist->user->image;
                $artist['user_role'] = $artist->user->role;
            } else {
                $artist['user_name'] = $artist->name;
                $artist['user_image'] = $artist->image;
                $artist['user_role'] = 'artist';
            }

            // Check if logged-in user follows
            $login_user_id = $request->login_user_id ?? 0;
            $artist['is_following'] = 0;
            if ($login_user_id > 0) {
                $follow = Follower::where('user_id', $login_user_id)->where('artist_id', $artist->id)->first();
                if ($follow) {
                    $artist['is_following'] = 1;
                }
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$artist]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Artist Songs ====================
    public function get_artist_songs(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $artist_id = $request->artist_id;
            $user_id = $request->login_user_id ?? 0;

            $data = Song::where('artist_id', $artist_id)->where('status', 1)->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            }
            return $this->common->API_Response(400, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== User Songs ====================
    public function get_user_songs(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $target_user_id = $request->user_id;
            $login_user_id = $request->login_user_id ?? 0;

            $artist = Artist::where('user_id', $target_user_id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $data = Song::where('artist_id', $artist->id)->where('status', 1)->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $login_user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($login_user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            }
            return $this->common->API_Response(400, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Apply to Become Artist ====================
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

            $user = User::where('id', $request->user_id)->first();
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            if ($user->role === 'artist') {
                return $this->common->API_Response(400, 'You are already an artist');
            }

            // Check existing pending request
            $existing = ArtistRequest::where('user_id', $request->user_id)->where('status', 'pending')->first();
            if ($existing) {
                return $this->common->API_Response(400, 'You already have a pending request');
            }

            $insert = ArtistRequest::create([
                'user_id' => $request->user_id,
                'artist_name' => $request->artist_name,
                'bio' => $request->bio ?? '',
                'status' => 'pending',
            ]);

            return $this->common->API_Response(200, 'Artist request submitted successfully', [$insert]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Get Artist Request Status ====================
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
                return $this->common->API_Response(400, 'No request found');
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$req]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Follow Artist ====================
    public function follow_artist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $existing = Follower::where('user_id', $request->user_id)->where('artist_id', $request->artist_id)->first();
            if ($existing) {
                return $this->common->API_Response(400, 'Already following this artist');
            }

            Follower::create([
                'user_id' => $request->user_id,
                'artist_id' => $request->artist_id,
            ]);

            return $this->common->API_Response(200, 'Followed successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Unfollow Artist ====================
    public function unfollow_artist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $existing = Follower::where('user_id', $request->user_id)->where('artist_id', $request->artist_id)->first();
            if (!$existing) {
                return $this->common->API_Response(400, 'Not following this artist');
            }

            $existing->delete();
            return $this->common->API_Response(200, 'Unfollowed successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Get Followers ====================
    public function get_followers(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $data = Follower::where('artist_id', $request->artist_id)->with('user')->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            $result = [];
            foreach ($data as $f) {
                if ($f->user) {
                    $this->common->imageNameToUrl([$f->user], 'image', $this->folder_user);
                    $result[] = [
                        'id' => $f->user->id,
                        'user_name' => $f->user->user_name,
                        'full_name' => $f->user->full_name,
                        'image' => $f->user->image,
                    ];
                }
            }

            if (count($result) > 0) {
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
            }
            return $this->common->API_Response(400, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Get Following ====================
    public function get_following(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $data = Follower::where('user_id', $request->user_id)->with('artist')->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            $result = [];
            foreach ($data as $f) {
                if ($f->artist) {
                    $this->common->imageNameToUrl([$f->artist], 'image', $this->folder_artist);
                    $result[] = [
                        'id' => $f->artist->id,
                        'name' => $f->artist->name,
                        'image' => $f->artist->image,
                        'bio' => $f->artist->bio,
                    ];
                }
            }

            if (count($result) > 0) {
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
            }
            return $this->common->API_Response(400, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Artist Dashboard ====================
    public function get_artist_dashboard(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $artist_id = $request->artist_id;
            $artist = Artist::find($artist_id);
            if (!$artist) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $song_ids = Song::where('artist_id', $artist_id)->pluck('id');

            // Total stats
            $total_songs = count($song_ids);
            $total_plays = Play::where('type', 1)->whereIn('content_id', $song_ids)->count();
            $total_comments = Comment::where('type', 1)->whereIn('content_id', $song_ids)->count();
            $total_followers = Follower::where('artist_id', $artist_id)->count();

            // Monthly chart data (last 30 days)
            $days = [];
            $plays_data = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $days[] = $date;
                $day_start = $date . ' 00:00:00';
                $day_end = $date . ' 23:59:59';
                $count = Play::where('type', 1)
                    ->whereIn('content_id', $song_ids)
                    ->where('created_at', '>=', $day_start)
                    ->where('created_at', '<=', $day_end)
                    ->count();
                $plays_data[] = $count;
            }

            // Most played songs
            $top_songs = Song::whereIn('id', $song_ids)->where('status', 1)->orderBy('total_play', 'DESC')->take(10)->get();
            $this->common->imageNameToUrl($top_songs, 'image', $this->folder_song);
            $this->common->getAllIdByName($top_songs);

            $result = [
                'total_songs' => $total_songs,
                'total_plays' => $total_plays,
                'total_comments' => $total_comments,
                'total_followers' => $total_followers,
                'chart_days' => $days,
                'chart_plays' => $plays_data,
                'top_songs' => $top_songs,
            ];

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$result]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Upload Song (User-Artist) ====================
    public function upload_song(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'name' => 'required|min:2',
                'category_id' => 'required|numeric',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'song' => 'required|mimes:mp3,wav,ogg,aac,m4a|max:51200',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, 'Only artists can upload songs');
            }

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, 'Artist profile not found');
            }

            $imageFile = $request->file('image');
            $songFile = $request->file('song');

            $imageName = 'song_' . date('d_m_Y_') . rand(1111, 9999) . '.' . $imageFile->getClientOriginalExtension();
            $songName = 'audio_' . date('d_m_Y_') . rand(1111, 9999) . '.' . $songFile->getClientOriginalExtension();

            $imageFile->move(base_path('storage/app/public/' . $this->folder_song), $imageName);
            $songFile->move(base_path('storage/app/public/' . $this->folder_song), $songName);

            $song = Song::create([
                'artist_id' => $artist->id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'image' => $imageName,
                'song_upload_type' => 'server_video',
                'song_url' => $songName,
                'is_premium' => $request->is_premium ?? 0,
                'status' => 1,
            ]);

            return $this->common->API_Response(200, 'Song uploaded successfully', [$song]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Delete Song (User-Artist) ====================
    public function delete_song(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'song_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, 'Only artists can delete songs');
            }

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, 'Artist profile not found');
            }

            $song = Song::where('id', $request->song_id)->where('artist_id', $artist->id)->first();
            if (!$song) {
                return $this->common->API_Response(400, 'Song not found or not yours');
            }

            // Delete files
            Storage::disk('public')->delete($this->folder_song . '/' . $song->image);
            Storage::disk('public')->delete($this->folder_song . '/' . $song->song_url);

            $song->delete();

            return $this->common->API_Response(200, 'Song deleted successfully');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== Update Song (User-Artist) ====================
    public function update_song(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'song_id' => 'required|numeric',
                'name' => 'required|min:2',
            ]);
            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, 'Only artists can update songs');
            }

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) {
                return $this->common->API_Response(400, 'Artist profile not found');
            }

            $song = Song::where('id', $request->song_id)->where('artist_id', $artist->id)->first();
            if (!$song) {
                return $this->common->API_Response(400, 'Song not found or not yours');
            }

            $updateData = [
                'name' => $request->name,
                'category_id' => $request->category_id ?? $song->category_id,
            ];

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($this->folder_song . '/' . $song->image);
                $imageFile = $request->file('image');
                $imageName = 'song_' . date('d_m_Y_') . rand(1111, 9999) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(base_path('storage/app/public/' . $this->folder_song), $imageName);
                $updateData['image'] = $imageName;
            }

            if ($request->hasFile('song')) {
                Storage::disk('public')->delete($this->folder_song . '/' . $song->song_url);
                $songFile = $request->file('song');
                $songName = 'audio_' . date('d_m_Y_') . rand(1111, 9999) . '.' . $songFile->getClientOriginalExtension();
                $songFile->move(base_path('storage/app/public/' . $this->folder_song), $songName);
                $updateData['song_url'] = $songName;
            }

            $song->update($updateData);

            return $this->common->API_Response(200, 'Song updated successfully', [$song]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // ==================== List Artists (with user-link flag) ====================
    public function get_artists(Request $request)
    {
        try {
            $data = Artist::orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                $this->common->imageNameToUrl($data, 'image', $this->folder_artist);
                foreach ($data as $artist) {
                    $artist['total_songs'] = Song::where('artist_id', $artist->id)->where('status', 1)->count();
                    $artist['is_user_artist'] = $artist->user_id !== null ? 1 : 0;
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            }
            return $this->common->API_Response(400, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
