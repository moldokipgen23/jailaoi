<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Category;
use App\Models\City;
use App\Models\Common;
use App\Models\Favorite;
use App\Models\Language;
use App\Models\Song;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

// Song Upload Type :1- Server Content, 2- External URL
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

            // JAILAOI FIX: removed type=1 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->latest()->get();

            $input_search = $request['input_search'];
            $input_artist = $request['input_artist'];

            $query = Song::query();

            if (!empty($input_search)) {
                $query->where('name', 'LIKE', "%{$input_search}%");
            }

            if (!empty($input_artist)) {
                $query->where('artist_id', $input_artist);
            }
            $params['data'] = $query->latest()->paginate(15);

            $this->common->imageNameToUrl($params['data'], 'image', $this->folder);
            $this->common->songNameToUrl($params['data'], 'song_url', $this->folder);

            return view('admin.song.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            // JAILAOI FIX: removed type=1 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['city'] = City::orderBy('sort_order', 'asc')->where('status', 1)->get();

            return view('admin.song.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            if ($request->upload_type == 1) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'song_url' => 'required',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'url' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);
            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");
            }

            if ($requestData['upload_type'] == 1) {
                $requestData['song_url'] = $requestData['song_url'];
            } else {
                $requestData['song_url'] = $requestData['url'];
            }
            unset($requestData['url']);

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {

                // Send Notification
                $imageURL = $this->common->Get_Image($this->folder, $requestData['image']);

                $noti_array = array(
                    'title' => __('label.new_radio_added'),
                    'description' => $song_data->name,
                    'image' => $song_data->image,
                    'image_url' => $imageURL,
                );
                $check = $this->common->basic_notification_configuration('add-radio-station');
                if ($check['status'] == 1 && $check['send_notification'] == 1) {
                    $this->common->sendNotification($noti_array);
                }

                return response()->json(['status' => 200, 'success' => __('label.success_add_radio_station')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.radio_station_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = Song::where('id', $id)->first();
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            // JAILAOI FIX: removed type=1 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['city'] = City::orderBy('sort_order', 'asc')->where('status', 1)->get();

            // Image Name to URL
            $this->common->imageNameToUrl(array($params['data']), 'image', $this->folder);
            $this->common->songNameToUrl(array($params['data']), 'song_url', $this->folder);

            if ($params['data'] != null) {
                return view('admin.song.edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        try {

            if ($request->upload_type == 1) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:2',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'image' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'url' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);

            // Image
            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }
            // Song URL
            if ($requestData['upload_type'] == 1) {
                if ($requestData['upload_type'] == $requestData['old_upload_type']) {
                    if ($requestData['song_url']) {
                        $requestData['song_url'] = $requestData['song_url'];
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_song_url']));
                    } else {
                        $requestData['song_url'] = basename($requestData['old_song_url']);
                    }
                } else {
                    if ($requestData['song_url']) {
                        $requestData['song_url'] = $requestData['song_url'];
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_song_url']));
                    } else {
                        $requestData['song_url'] = "";
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_song_url']));
                    }
                }
            } else {
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_song_url']));
                $requestData['song_url'] = "";
                if ($requestData['url']) {
                    $requestData['song_url'] = $requestData['url'];
                }
            }
            unset($requestData['old_image'], $requestData['url'], $requestData['old_song_url'], $requestData['old_upload_type']);

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_radio_station')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.radio_station_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Song::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $this->common->deleteImageToFolder($this->folder, $data['song_url']);
                $data->delete();

                Favorite::where('content_id', $id)->delete();
                Banner::where('content_id', $id)->delete();
            }

            return redirect()->route('song.index')->with('success', __('label.success_delete_radio_station'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus($id)
    {
        try {

            $data = Song::where('id', $id)->first();
            if ($data) {
                $data->status = $data->status = 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function saveChunk()
    {
        @set_time_limit(5 * 60);

        $targetDir = storage_path('/app/public/song');
        //$targetDir = 'uploads';

        $cleanupTargetDir = true; // Remove old files

        $maxFileAge = 5 * 3600; // Temp file age in seconds

        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $category_image = $fileName;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $category_image;
        // Chunking might be enabled

        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        // Remove old temp files

        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        // Open temp file

        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);
        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);

            // Generate a new filename based on the current date and time
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'song' . date('_d_m_Y_') . rand(1111, 9999) . '.' . $extension;

            // JAILAOI: Organize into artist subfolder (song/artist-slug/filename.mp3)
            $artistId = isset($_REQUEST['artist_id']) ? intval($_REQUEST['artist_id']) : 0;
            $artistSlug = 'various';
            if ($artistId > 0) {
                $artist = \App\Models\Artist::find($artistId);
                if ($artist) {
                    $artistSlug = \Illuminate\Support\Str::slug($artist->name, '-') ?: 'various';
                }
            }
            $artistSubDir = $targetDir . DIRECTORY_SEPARATOR . $artistSlug;
            if (!is_dir($artistSubDir)) {
                @mkdir($artistSubDir, 0755, true);
            }
            $newFilePath = $artistSubDir . DIRECTORY_SEPARATOR . $newFileName;
            $remoteFileName = $artistSlug . '/' . $newFileName;

            rename($filePath, $newFilePath);

            // JAILAOI: Upload assembled chunk file to CDN if driver is bunny or r2.
            $audioDriver = getAudioStorageDriver();
            if ($audioDriver == 'bunny') {
                try {
                    (new \App\Models\Common)->uploadFileToBunny($newFilePath, 'song/' . $remoteFileName);
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Bunny upload failed for song: ' . $e->getMessage());
                }
            } elseif ($audioDriver == 'r2') {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->put(
                        'song/' . $remoteFileName,
                        file_get_contents($newFilePath)
                    );
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('R2 upload failed for song: ' . $e->getMessage());
                }
            }

            die(json_encode(array('jsonrpc' => '2.0', 'result' => $remoteFileName, 'id' => 'id')));
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
