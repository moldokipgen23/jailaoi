<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Category;
use App\Models\City;
use App\Models\Common;
use App\Models\Favorite;
use App\Models\Language;
use App\Models\Music;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

// Music Upload Type :1- Server Content, 2- External URL
class MusicController extends Controller
{
    private $folder     = "music";           // audio folder
    private $folder_img = "images/music";    // JAILAOI: image folder on Bunny CDN
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            // JAILAOI FIX: removed type=3 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();
            // JAILAOI: category/language filters for music index
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();

            $input_search = $request['input_search'];
            $input_artist = $request['input_artist'];
            $input_category = $request['input_category'];
            $input_language = $request['input_language'];

            $query = Music::query();

            if (!empty($input_search)) {
                $query->where('title', 'LIKE', "%{$input_search}%");
            }

            if (!empty($input_artist)) {
                $query->whereRaw("FIND_IN_SET(?,artist_id)", $input_artist);
            }

            if (!empty($input_category)) {
                $query->where('category_id', $input_category);
            }

            if (!empty($input_language)) {
                $query->where('language_id', $input_language);
            }
            $params['data'] = $query->latest()->paginate(15)->appends($request->query());

            foreach ($params['data'] as $music) {
                $music['portrait_img'] = $this->common->Get_Image($this->folder_img, $music['portrait_img']);
                if ($music['upload_type'] == 1) {
                    $music['music'] = $this->common->Get_Song($this->folder, $music['music']);
                }
            }

            return view('admin.music.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            // JAILAOI FIX: removed type=3 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['city'] = City::orderBy('sort_order', 'asc')->where('status', 1)->get();

            return view('admin.music.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'is_premium' => 'required',
                    'upload_type' => 'required',
                    'portrait_img' => 'required|image|mimes:jpg,png,jpeg|max:2048',
                    'ogtag_img' => 'image|mimes:jpg,png,jpeg|max:2048',
                    'landscape_img' => 'image|mimes:jpg,png,jpeg|max:2048',
                ]
            );

            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            if ($request->upload_type == 1) {
                $validator2 = Validator::make(
                    $request->all(),
                    [
                        'music' => 'required',
                    ]
                );
            } else {
                $validator2 = Validator::make(
                    $request->all(),
                    [
                        'url' => 'required|url',
                    ]
                );
            }

            if ($validator2->fails()) {
                $errs = $validator2->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            if (isset($requestData['artist_id'])) {
                $requestData['artist_id'] = implode(',', $requestData['artist_id']);
            } else {
                $requestData['artist_id'] = "";
            }
            $artistSlug = 'various';
            $firstArtistId = intval(explode(',', $requestData['artist_id'])[0] ?? 0);
            if ($firstArtistId > 0) {
                $artist = \App\Models\Artist::find($firstArtistId);
                if ($artist) $artistSlug = \Illuminate\Support\Str::slug($artist->name, '-') ?: 'various';
            }
            if (isset($requestData['portrait_img'])) {
                $files = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);
            }
            if (isset($requestData['ogtag_img'])) {
                $files = $requestData['ogtag_img'];
                $requestData['ogtag_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);
            } else {
                $requestData['ogtag_img'] = "";
            }
            if (isset($requestData['landscape_img'])) {
                $files = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);
            } else {
                $requestData['landscape_img'] = "";
            }
            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);
            $requestData['description'] = isset($requestData['description']) ? $requestData['description'] : "";
            $requestData['album_name'] = isset($requestData['album_name']) ? $requestData['album_name'] : "";
            $requestData['language_id'] = isset($requestData['language_id']) ? $requestData['language_id'] : 0;
            $requestData['category_id'] = isset($requestData['category_id']) ? $requestData['category_id'] : 0;
            $requestData['release_year'] = !empty($requestData['release_year']) ? (int) $requestData['release_year'] : null;
            $requestData['tags'] = !empty($requestData['tags']) ? trim($requestData['tags']) : null;
            $requestData['total_play'] = 0;

            if ($requestData['upload_type'] == 1) {
                $requestData['music'] = $requestData['music'];
            } else {
                $requestData['music'] = $requestData['url'];
            }
            unset($requestData['url']);

            $music_data = Music::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($music_data->id)) {

                // Send Notification
                $imageURL = $this->common->Get_Image($this->folder_img, $requestData['portrait_img']);

                $noti_array = array(
                    'title' => __('label.new_music_added'),
                    'description' => $music_data->title,
                    'image' => $music_data->portrait_img,
                    'image_url' => $imageURL,
                );
                $check = $this->common->basic_notification_configuration('add-music');
                if ($check['status'] == 1 && $check['send_notification'] == 1) {

                    $this->common->sendNotification($noti_array);
                }

                return response()->json(['status' => 200, 'success' => __('label.success_add_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.music_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = music::where('id', $id)->first();
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            // JAILAOI FIX: removed type=3 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['city'] = City::orderBy('sort_order', 'asc')->where('status', 1)->get();

            // Image Name to URL
            $this->common->imageNameToUrl(array($params['data']), 'portrait_img', $this->folder_img);
            $this->common->imageNameToUrl(array($params['data']), 'ogtag_img', $this->folder_img);
            $this->common->imageNameToUrl(array($params['data']), 'landscape_img', $this->folder_img);
            if ($params['data']['upload_type'] == 1) {
                $params['data']['music'] = $this->common->Get_Song($this->folder, $params['data']['music']);
            }
            if ($params['data'] != null) {
                return view('admin.music.edit', $params);
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

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'is_premium' => 'required',
                    'upload_type' => 'required',
                    'portrait_img' => 'image|mimes:jpg,png,jpeg|max:2048',
                    'ogtag_img' => 'image|mimes:jpg,png,jpeg|max:2048',
                    'landscape_img' => 'image|mimes:jpg,png,jpeg|max:2048',
                ]
            );

            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if ($request->upload_type == 1) {
                $validator2 = Validator::make(
                    $request->all(),
                    [
                        'music' => 'required',
                    ]
                );
            } else {
                $validator2 = Validator::make(
                    $request->all(),
                    [
                        'url' => 'required|url',
                    ]
                );
            }

            if ($validator2->fails()) {
                $errs = $validator2->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            if (isset($requestData['artist_id'])) {
                $requestData['artist_id'] = implode(',', $requestData['artist_id']);
            }

            $artistSlug = 'various';
            $firstArtistId = intval(explode(',', $requestData['artist_id'] ?? '')[0] ?? 0);
            if ($firstArtistId > 0) {
                $artist = \App\Models\Artist::find($firstArtistId);
                if ($artist) $artistSlug = \Illuminate\Support\Str::slug($artist->name, '-') ?: 'various';
            }

            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);

            $requestData['description'] = isset($requestData['description'])  ? $requestData['description'] : "";
            $requestData['album_name'] = isset($requestData['album_name'])  ? $requestData['album_name'] : "";
            $requestData['language_id'] = isset($requestData['language_id'])  ? $requestData['language_id'] : 0;
            $requestData['category_id'] = isset($requestData['category_id'])  ? $requestData['category_id'] : 0;
            $requestData['release_year'] = !empty($requestData['release_year']) ? (int) $requestData['release_year'] : null;
            $requestData['tags'] = !empty($requestData['tags']) ? trim($requestData['tags']) : null;
            // potrait_image
            if (isset($requestData['portrait_img'])) {
                $files = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);

                $this->common->deleteImageToFolder($this->folder_img, $requestData['old_portrait_img']);
            }
            // ogtag_image
            if (isset($requestData['ogtag_img'])) {
                $files = $requestData['ogtag_img'];
                $requestData['ogtag_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);

                $this->common->deleteImageToFolder($this->folder_img, $requestData['old_ogtag_img']);
            }
            // landscape_image
            if (isset($requestData['landscape_img'])) {
                $files = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($files, $this->folder_img, "music_", $artistSlug);

                $this->common->deleteImageToFolder($this->folder_img, $requestData['old_landscape_img']);
            }
            // music URL
            if ($requestData['upload_type'] == 1) {
                if ($requestData['upload_type'] == $requestData['old_upload_type']) {
                    if ($requestData['music']) {
                        $requestData['music'] = $requestData['music'];
                        if ($requestData['music'] != basename($requestData['old_music'])) {
                            $this->common->deleteImageToFolder($this->folder, $requestData['old_music']);
                        }
                    } else {
                        $requestData['music'] = basename($requestData['old_music']);
                    }
                } else {
                    if ($requestData['music']) {
                        $requestData['music'] = $requestData['music'];
                        $this->common->deleteImageToFolder($this->folder, $requestData['old_music']);
                    } else {
                        $requestData['music'] = "";
                        $this->common->deleteImageToFolder($this->folder, $requestData['old_music']);
                    }
                }
            } else {
                $this->common->deleteImageToFolder($this->folder, $requestData['old_music']);
                $requestData['music'] = "";
                if ($requestData['url']) {
                    $requestData['music'] = $requestData['url'];
                }
            }
            unset($requestData['old_image'], $requestData['url'], $requestData['old_music'], $requestData['old_upload_type'], $requestData['old_portrait_img'], $requestData['old_ogtag_img'], $requestData['old_landscape_img']);

            $music_data = Music::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($music_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.music_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Music::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                // JAILAOI: delete audio from Bunny/R2 before removing DB record
                if ($data['music'] && getAudioStorageDriver() == 'bunny') {
                    $this->common->deleteFileFromBunny('music/' . $data['music']);
                }
                $this->common->deleteImageToFolder($this->folder, $data['music']);
                $data->delete();

                Favorite::where('content_id', $id)->delete();
                Banner::where('content_id', $id)->delete();
            }

            return redirect()->route('music.index')->with('success', __('label.success_delete_music'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus($id)
    {
        try {

            $data = Music::where('id', $id)->first();
            if ($data) {
                $data->status = $data->status == 1 ?  0 : 1;
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

        $targetDir = storage_path('/app/public/music');
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
            $newFileName = 'music' . date('_d_m_Y_') . rand(1111, 9999) . '.' . $extension;

            // JAILAOI: Organize into artist subfolder (music/artist-slug/filename.mp3)
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
            // This is stored in DB and used in URL: e.g. "minlun-hangmi/music_08_06_2026_1234.mp3"
            $remoteFileName = $artistSlug . '/' . $newFileName;

            // Rename the uploaded file to the artist subfolder
            rename($filePath, $newFilePath);

            // JAILAOI: Upload assembled chunk file to CDN if driver is bunny or r2.
            $audioDriver = getAudioStorageDriver();
            if ($audioDriver == 'bunny') {
                try {
                    (new \App\Models\Common)->uploadFileToBunny($newFilePath, 'music/' . $remoteFileName);
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Bunny upload failed for music: ' . $e->getMessage());
                }
            } elseif ($audioDriver == 'r2') {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->put(
                        'music/' . $remoteFileName,
                        file_get_contents($newFilePath)
                    );
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('R2 upload failed for music: ' . $e->getMessage());
                }
            }

            // Send artist-slug/filename back to client — stored in DB as music field
            die(json_encode(array('jsonrpc' => '2.0', 'result' => $remoteFileName, 'id' => 'id')));
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
