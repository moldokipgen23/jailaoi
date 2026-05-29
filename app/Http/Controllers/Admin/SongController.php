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

// Song Upload Type : server_video, external_url
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

            $params['artist'] = Artist::latest()->get();

            $input_search = $request['input_search'];
            $input_artist = $request['input_artist'];
            if ($input_search != null && isset($input_search)) {

                if ($input_artist != 0) {
                    $params['data'] = Song::where('name', 'LIKE', "%{$input_search}%")->where('artist_id', $input_artist)->with('artist')->orderBy('id', 'desc')->paginate(15);
                } else {
                    $params['data'] = Song::where('name', 'LIKE', "%{$input_search}%")->with('artist')->orderBy('id', 'desc')->paginate(15);
                }
            } else {

                if ($input_artist != 0) {
                    $params['data'] = Song::where('artist_id', $input_artist)->with('artist')->orderBy('id', 'desc')->paginate(15);
                } else {
                    $params['data'] = Song::with('artist')->orderBy('id', 'desc')->paginate(15);
                }
            }

            $this->common->imageNameToUrl($params['data'], 'image', $this->folder);
            $this->common->imageNameToUrl($params['data'], 'song_url', $this->folder);

            return view('admin.song.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function create()
    {
        try {
            $params['data'] = [];
            $params['category'] = Category::select('*')->latest()->get();
            $params['artist'] = Artist::select('*')->latest()->get();
            $params['language'] = Language::select('*')->latest()->get();
            $params['city'] = City::select('*')->latest()->get();

            return view('admin.song.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function store(Request $request)
    {
        try {

            if ($request->song_upload_type == "server_video") {
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
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");
            }

            if ($request->song_upload_type == "server_video") {
                $requestData['song_url'] = $request->song_url;
            } else {
                $requestData['song_url'] = $request->url;
            }
            unset($requestData['url']);

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {

                // Send Notification
                $imageURL = $this->common->Get_Image($this->folder, $requestData['image']);
                if ($request->song_upload_type == "server_video") {
                    $songurl = $this->common->Get_Song($this->folder, $requestData['song_url']);
                } else {
                    $songurl = $request->url;
                }

                $noti_array = array(
                    'id' => $song_data->id,
                    'name' => $song_data->name,
                    'image' => $imageURL,
                    'song_url' => $songurl
                );
                $this->common->sendNotification($noti_array);

                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {
            $params['data'] = Song::where('id', $id)->first();
            $params['category'] = Category::select('*')->latest()->get();
            $params['artist'] = Artist::select('*')->latest()->get();
            $params['language'] = Language::select('*')->latest()->get();
            $params['city'] = City::select('*')->latest()->get();

            // Image Name to URL
            $this->common->imageNameToUrl(array($params['data']), 'image', $this->folder);

            if ($params['data'] != null) {
                return view('admin.song.edit', $params);
            } else {
                return redirect()->back()->with('error', __('Label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {
            if ($request->song_upload_type == "server_video") {
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
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            // Image
            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "song_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }
            // Song URL
            if ($request->song_upload_type == "server_video") {
                if ($request->song_url) {
                    $requestData['song_url'] = $request->song_url;
                    $this->common->deleteImageToFolder($this->folder, $requestData['old_song_url']);
                } else {
                    $requestData['song_url'] = $request->old_song_url;
                }
            } else {
                $requestData['song_url'] = $request->url;
                $this->common->deleteImageToFolder($this->folder, $requestData['old_song_url']);
            }
            unset($requestData['old_image'], $requestData['url'], $requestData['old_song_url']);

            $song_data = Song::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($song_data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_edit_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_updated')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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

            return redirect()->route('song.index')->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function changeStatus($id)
    {
        try {

            $data = Song::where('id', $id)->first();
            if ($data->status == 0) {
                $data->status = 1;
            } elseif ($data->status == 1) {
                $data->status = 0;
            } else {
                $data->status = 0;
            }
            $data->save();
            return response()->json(array('status' => 200, 'success' => __('Label.status_changed'), 'id' => $data->id, 'Status' => $data->status));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            $extension = pathinfo($fileName, PATHINFO_EXTENSION); // Get the file extension from the original filename
            $newFileName = 'song' . date('_d_m_Y_') . rand(1111, 9999) . '.' . $extension; // Use the extracted extension
            $newFilePath = $targetDir . DIRECTORY_SEPARATOR . $newFileName;

            // Rename the uploaded file to the new filename
            rename($filePath, $newFilePath);

            // Send the new file name back to the client
            die(json_encode(array('jsonrpc' => '2.0', 'result' => $newFileName, 'id' => 'id')));
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
