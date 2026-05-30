<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Language;
use App\Models\Hashtag;
use App\Models\User;
use App\Models\Content;
use App\Models\Content_Like;
use App\Models\Content_Report;
use App\Models\Content_View;
use App\Models\History;
use App\Models\Notification;
use App\Models\Watch_later;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class MusicController extends Controller
{
    private $folder = "content";
    private $folder_ffmpeg = "/app/public/content/";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['data'] = [];
            $params['channel'] = User::latest()->get();
            $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

            $input_search = $request['input_search'];
            $input_channel = $request['input_channel'];
            $input_category = $request['input_category'];
            $input_language = $request['input_language'];

            $query = Content::where('content_type', 2);
            if ($input_search) {
                $query->where('title', 'LIKE', "%{$input_search}%");
            }
            if ($input_channel != 0) {
                $query->where('channel_id', $input_channel);
            }
            if ($input_category != 0) {
                $query->where('category_id', $input_category);
            }
            if ($input_language != 0) {
                $query->where('language_id', $input_language);
            }
            $params['data'] = $query->orderBy('id', 'DESC')->paginate(20);

            for ($i = 0; $i < count($params['data']); $i++) {

                $params['data'][$i]['portrait_img'] = $this->common->getImage($this->folder, $params['data'][$i]['portrait_img'], $params['data'][$i]['portrait_img_storage_type']);
                $params['data'][$i]['landscape_img'] = $this->common->getImage($this->folder, $params['data'][$i]['landscape_img'], $params['data'][$i]['landscape_img_storage_type']);
                if ($params['data'][$i]['content_upload_type'] == 'server_video') {
                    $params['data'][$i]['content'] = $this->common->getVideo($this->folder, $params['data'][$i]['content'], $params['data'][$i]['content_storage_type']);
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
            $params['channel'] = User::latest()->get();
            $params['category'] = Category::orderBy('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->latest()->get();

            return view('admin.music.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'content_upload_type' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'content_duration' => 'required|after_or_equal:00:00:01',
                'is_comment' => 'required',
                'is_download' => 'required',
                'is_like' => 'required',
            ];
            if ($request['content_upload_type'] == 'server_video') {
                $rules['music'] = 'required';
            } else {
                $rules['url'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();
            $requestData['portrait_img_storage_type'] = $storage_type;
            $requestData['landscape_img_storage_type'] = $storage_type;
            $requestData['content_storage_type'] = $storage_type;

            $requestData['content_type'] = 2;
            $requestData['description'] = $requestData['description'] ?? "";
            $hashtag_id = $this->common->checkHashTag($requestData['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($file1, $this->folder, 'port_', $requestData['portrait_img_storage_type']);
            } else {
                $requestData['portrait_img'] = "";
            }
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($file2, $this->folder, 'land_', $requestData['landscape_img_storage_type']);
            } else {
                $requestData['landscape_img'] = "";
            }
            if ($requestData['content_upload_type'] == 'server_video') {

                if ($requestData['content_storage_type'] == 1) {
                    $requestData['content'] = $requestData['music'];
                    if (empty($requestData['content_duration']) || $requestData['content_duration'] === '00:00:00') {
                        $dur = $this->common->ExtractDuration($requestData['music'], $this->folder_ffmpeg);
                        if ($dur > 0) $requestData['content_duration'] = $dur;
                    }
                    $wf = $this->common->generateWaveform($requestData['music'], $this->folder_ffmpeg);
                    if ($wf) $requestData['waveform_data'] = $wf;
                } else {
                    $requestData['content'] = $this->common->saveImage($requestData['music'], $this->folder, 'music_', $requestData['content_storage_type']);
                }
            } else {
                $requestData['content'] = $requestData['url'];
            }
            unset($requestData['music'], $requestData['url']);

            $requestData['is_rent'] = 0;
            $requestData['rent_price'] = 0;
            $requestData['rent_day'] = 0;
            $requestData['content_duration'] = $this->common->time_to_milliseconds($requestData['content_duration']);
            $requestData['total_view'] = 0;
            $requestData['total_like'] = 0;
            $requestData['total_dislike'] = 0;
            $requestData['playlist_type'] = 0;
            $requestData['total_watch_time'] = 0;
            $requestData['status'] = 1;

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_music')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {
            $params['data'] = Content::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['channel'] = User::latest()->get();
                $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
                $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

                $params['data']['portrait_img'] = $this->common->getImage($this->folder, $params['data']['portrait_img'], $params['data']['portrait_img_storage_type']);
                $params['data']['landscape_img'] = $this->common->getImage($this->folder, $params['data']['landscape_img'], $params['data']['landscape_img_storage_type']);
                if ($params['data']['content_upload_type'] == 'server_video') {
                    $params['data']['content'] = $this->common->getVideo($this->folder, $params['data']['content'], $params['data']['content_storage_type']);
                }
                return view('admin.music.edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        try {
            $rules = [
                'title' => 'required',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'content_upload_type' => 'required',
                'content_duration' => 'required|after_or_equal:00:00:01',
                'is_comment' => 'required',
                'is_like' => 'required',
                'is_download' => 'required',
            ];
            if ($request['content_upload_type'] != 'server_video') {
                $rules['url'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $requestData['content_type'] = 2;
            $requestData['description'] = $requestData['description'] ?? "";
            $old_hashtag = explode(',', $requestData['old_hashtag_id']);
            Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);
            $hashtag_id = $this->common->checkHashTag($requestData['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $requestData['portrait_img_storage_type'] = $storage_type;
                $requestData['portrait_img'] = $this->common->saveImage($file1, $this->folder, 'port_', $requestData['portrait_img_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_portrait_img']), $request['old_portrait_img_storage_type']);
            }
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $requestData['landscape_img_storage_type'] = $storage_type;
                $requestData['landscape_img'] = $this->common->saveImage($file2, $this->folder, 'land_', $requestData['landscape_img_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_landscape_img']), $request['old_landscape_img_storage_type']);
            }
            if ($requestData['content_upload_type'] == 'server_video') {

                if ($requestData['content_upload_type'] == $requestData['old_content_upload_type']) {

                    if ($requestData['music']) {

                        $requestData['content_storage_type'] = $storage_type;
                        if ($storage_type == 1) {
                            $requestData['content'] = $requestData['music'];
                            if (empty($requestData['content_duration']) || $requestData['content_duration'] === '00:00:00') {
                                $dur = $this->common->ExtractDuration($requestData['music'], $this->folder_ffmpeg);
                                if ($dur > 0) $requestData['content_duration'] = $dur;
                            }
                            $wf = $this->common->generateWaveform($requestData['music'], $this->folder_ffmpeg);
                            if ($wf) $requestData['waveform_data'] = $wf;
                        } else {
                            $requestData['content'] = $this->common->saveImage($requestData['music'], $this->folder, 'music_', $storage_type);
                        }
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_content']), $request['old_content_storage_type']);
                    }
                } else {

                    $requestData['content_storage_type'] = $storage_type;
                    if ($requestData['music']) {

                        if ($storage_type == 1) {
                            $requestData['content'] = $requestData['music'];
                            if (empty($requestData['content_duration']) || $requestData['content_duration'] === '00:00:00') {
                                $dur = $this->common->ExtractDuration($requestData['music'], $this->folder_ffmpeg);
                                if ($dur > 0) $requestData['content_duration'] = $dur;
                            }
                            $wf = $this->common->generateWaveform($requestData['music'], $this->folder_ffmpeg);
                            if ($wf) $requestData['waveform_data'] = $wf;
                        } else {
                            $requestData['content'] = $this->common->saveImage($requestData['music'], $this->folder, 'music_', $storage_type);
                        }
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_content']), $request['old_content_storage_type']);
                    } else {
                        $requestData['content'] = '';
                    }
                }
            } else {

                $requestData['content_storage_type'] = $storage_type;
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_content']), $requestData['old_content_storage_type']);

                $requestData['content'] = "";
                if ($requestData['url']) {
                    $requestData['content'] = $requestData['url'];
                }
            }
            unset($requestData['music'], $requestData['url'], $requestData['old_content_upload_type'], $requestData['old_hashtag_id'], $requestData['old_content'], $requestData['old_portrait_img'], $requestData['old_landscape_img'], $requestData['old_portrait_img_storage_type'], $requestData['old_landscape_img_storage_type'], $requestData['old_content_storage_type']);
            $requestData['content_duration'] = $this->common->time_to_milliseconds($requestData['content_duration']);

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_music')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Content::where('id', $id)->first();
            if (isset($data)) {

                $old_hashtag = explode(',', $data['hashtag_id']);
                Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);

                $this->common->deleteImageToFolder($this->folder, $data['portrait_img'], $data['portrait_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img'], $data['landscape_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['content'], $data['content_storage_type']);
                $data->delete();

                // Content Releted Data Delete
                Comment::where('content_id', $id)->delete();
                Content_Report::where('content_id', $id)->delete();
                History::where('content_id', $id)->delete();
                Notification::where('content_id', $id)->delete();
                Content_Like::where('content_id', $id)->delete();
                Content_View::where('content_id', $id)->delete();
                Watch_later::where('content_id', $id)->delete();
            }
            return redirect()->route('admin.music.index')->with('success', __('label.music_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // Status Change
    public function changeStatus(Request $request)
    {
        try {

            $data = Content::where('id', $request['id'])->first();
            if (isset($data)) {

                $data['status'] = $data['status'] === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data['status']]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // Save Chunk
    public function saveChunk()
    {
        @set_time_limit(5 * 60);

        $targetDir = storage_path('/app/public/content');
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
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

        // Remove old temp files
        if ($cleanupTargetDir && is_dir($targetDir) && $dir = opendir($targetDir)) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // Remove temp file if it is older than the max age and not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
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
            $newFileName = 'music' . date('_Y_m_d_') . uniqid() . '.' . $extension; // Use the extracted extension
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
