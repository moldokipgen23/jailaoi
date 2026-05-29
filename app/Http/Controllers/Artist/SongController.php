<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Category;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class SongController extends Controller
{
    private $folder = "song";
    public $common;

    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return redirect()->route('artist.login')->with('error', 'Artist profile not found');
            }

            $params['data'] = Song::where('artist_id', $artist->id)->with('artist')->orderBy('id', 'desc')->paginate(15);
            $this->common->imageNameToUrl($params['data'], 'image', $this->folder);
            $this->common->imageNameToUrl($params['data'], 'song_url', $this->folder);

            return view('artist.song.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function create()
    {
        try {
            $params['category'] = Category::select('*')->latest()->get();
            return view('artist.song.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return response()->json(array('status' => 400, 'errors' => 'Artist profile not found'));
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'category_id' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'song_url' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            $requestData['artist_id'] = $artist->id;
            $requestData['song_upload_type'] = 'server_video';
            $requestData['status'] = 0;

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");
            }

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {
                return response()->json(array('status' => 200, 'success' => 'Song uploaded successfully. Awaiting admin approval.'));
            } else {
                return response()->json(array('status' => 400, 'errors' => 'Failed to upload song'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function edit($id)
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return redirect()->route('artist.login')->with('error', 'Artist profile not found');
            }

            $params['data'] = Song::where('id', $id)->where('artist_id', $artist->id)->first();
            if (!$params['data']) {
                return redirect()->back()->with('error', 'Song not found');
            }

            $params['category'] = Category::select('*')->latest()->get();
            $this->common->imageNameToUrl(array($params['data']), 'image', $this->folder);

            return view('artist.song.edit', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return response()->json(array('status' => 400, 'errors' => 'Artist profile not found'));
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'category_id' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            $requestData['artist_id'] = $artist->id;

            $song = Song::where('id', $requestData['id'])->where('artist_id', $artist->id)->first();
            if (!$song) {
                return response()->json(array('status' => 400, 'errors' => 'Song not found'));
            }

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }
            unset($requestData['old_image']);

            if (isset($requestData['song_url']) && $requestData['song_url']) {
                $requestData['song_upload_type'] = 'server_video';
            }

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {
                return response()->json(array('status' => 200, 'success' => 'Song updated successfully'));
            } else {
                return response()->json(array('status' => 400, 'errors' => 'Failed to update song'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('web')->user();
            $artist = $user->artist;

            if (!$artist) {
                return redirect()->route('artist.login')->with('error', 'Artist profile not found');
            }

            $data = Song::where('id', $id)->where('artist_id', $artist->id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $this->common->deleteImageToFolder($this->folder, $data['song_url']);
                $data->delete();
            }

            return redirect()->route('artist.songs.index')->with('success', 'Song deleted successfully');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
