<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\PlaylistSong;
use App\Models\Common;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PlaylistController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function create_playlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'name' => 'required|min:1',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $data = Playlist::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'privacy' => $request->privacy ?? 0,
            ]);

            return response()->json(['status' => 200, 'message' => 'Playlist created successfully', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_user_playlists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $playlists = Playlist::where('user_id', $request->user_id)
                ->orderBy('id', 'DESC')
                ->get();

            $this->common->imageNameToUrl($playlists, 'image', 'playlist');

            return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $playlists]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_playlist_songs(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $songIds = PlaylistSong::where('playlist_id', $request->playlist_id)
                ->pluck('song_id');

            $songs = Song::whereIn('id', $songIds)->where('status', 1)->get();

            $this->common->songNameToUrl($songs, 'song_url', 'song');
            $this->common->imageNameToUrl($songs, 'image', 'song');

            return response()->json(['status' => 200, 'message' => 'Data found', 'data' => $songs]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function add_song_to_playlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
                'song_id' => 'required',
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            $exists = PlaylistSong::where('playlist_id', $request->playlist_id)
                ->where('song_id', $request->song_id)
                ->first();

            if ($exists) {
                return response()->json(['status' => 400, 'errors' => 'Song already in playlist']);
            }

            PlaylistSong::create([
                'playlist_id' => $request->playlist_id,
                'song_id' => $request->song_id,
                'user_id' => $request->user_id,
            ]);

            return response()->json(['status' => 200, 'message' => 'Song added to playlist']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function remove_song_from_playlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
                'song_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            PlaylistSong::where('playlist_id', $request->playlist_id)
                ->where('song_id', $request->song_id)
                ->delete();

            return response()->json(['status' => 200, 'message' => 'Song removed from playlist']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function delete_playlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            PlaylistSong::where('playlist_id', $request->playlist_id)->delete();
            Playlist::where('id', $request->playlist_id)->where('user_id', $request->user_id)->delete();

            return response()->json(['status' => 200, 'message' => 'Playlist deleted']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
