<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Language;
use App\Models\Podcast;
use App\Models\Episode;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PodcastController extends Controller
{
    private $folder = "podcast";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            // JAILAOI FIX: removed type=2 filter — all JailaOi artists are type=0
            $params['artist'] = Artist::where('status', 1)->orderBy('sort_order', 'asc')->get();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                if ($input_search != null && isset($input_search)) {
                    $data = Podcast::withCount(['episodes'])->where('title', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Podcast::withCount(['episodes'])->latest()->get();
                }

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder);
                foreach ($data as $podcast) {
                    if ($podcast['trailer_upload_type'] == 1) {
                        $podcast['trailer_audio'] =  $this->common->Get_Song($this->folder, $podcast['trailer_audio']);
                    }
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_podcast') . '\');" method="POST" action="' . route('podcast.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn"  title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around" >';
                        $btn .= '<a class="edit-delete-btn edit_podcasts" title=' . __('label.edit') . ' data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-title="' . $row->title . '" data-portrait_img="' . $row->portrait_img . '" data-landscape_img="' . $row->landscape_img . '" data-description="' . $row->description . '" data-category_id="' . $row->category_id . '" data-language_id="' . $row->language_id . '" data-is_premium="' . $row->is_premium . '"data-duration="' . $row->duration . '"data-trailer_audio="' . $row->trailer_audio . '"data-trailer_upload_type="' . $row->trailer_upload_type . '"data-artist_id="' . $row->artist_id . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        $status = $row->status == 1 ? "checked" : "";
                        return '<div class="switch">
                                    <input class="status-checkbox" id="checkbox' . $row->id . '" data-id="' . $row->id . '" type="checkbox" ' . $status . '>
                                    <label for="checkbox' . $row->id . '"></label>
                                      <span class="toggle-text"
                                        data-on="' . __('label.show') . '"
                                        data-off="' . __('label.hide') . '"></span>
                                    </div>';
                    })
                    ->addColumn('episode', function ($row) {
                        $btn = '<a href="' . route('podcast.episode.index', $row->id) . '" class="btn text-white p-1 font-weight-bold bg-primary-color"> Episode List - ' . $row->episodes_count . '</a> ';
                        return $btn;
                    })
                    ->rawColumns(['action', 'episode', 'status'])
                    ->make(true);
            }
            return view('admin.podcast.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'artist_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'description' => 'required',
                'is_premium' => 'required',
                'portrait_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if (!empty($request['url'])) {
                $validator1 = Validator::make($request->all(), [
                    'url' => 'url',
                ]);
                if ($validator1->fails()) {
                    $errs1 = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs1));
                }
            }

            $requestData = $request->all();

            $files = $requestData['portrait_img'];
            $files1 = $requestData['landscape_img'];
            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);
            $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder, "podcast_");
            $requestData['landscape_img'] = $this->common->saveImage($files1, $this->folder, "podcast_");
            $requestData['total_play'] = 0;
            if ($requestData['trailer_upload_type'] == 1) {
                $requestData['trailer_audio'] = $requestData['trailer_audio'] ?? '';
            } else {
                $requestData['trailer_audio'] = $requestData['url'] ?? '';
            }
            unset($requestData['url']);


            $podcast_data = Podcast::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($podcast_data->id)) {
                // Send Notification
                $imageURL = $this->common->Get_Image($this->folder, $requestData['portrait_img']);

                $noti_array = array(
                    'title' => __('label.new_podcast_added'),
                    'description' => $podcast_data->title,
                    'image' => $podcast_data->portrait_img,
                    'image_url' => $imageURL,
                );

                $check = $this->common->basic_notification_configuration('add-podcast');
                if ($check['status'] == 1 && $check['send_notification'] == 1) {
                    $this->common->sendNotification($noti_array);
                }

                return response()->json(['status' => 200, 'success' => __('label.success_add_podcast')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.podcast_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'artist_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'description' => 'required',
                'is_premium' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if (!empty($request['url'])) {
                $validator1 = Validator::make($request->all(), [
                    'url' => 'url',
                ]);
                if ($validator1->fails()) {
                    $errs1 = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs1));
                }
            }

            $requestData = $request->all();

            if ($requestData['trailer_upload_type'] == 1) {
                if ($requestData['trailer_upload_type'] == $requestData['old_trailer_upload_type']) {
                    if ($requestData['trailer_audio']) {
                        $requestData['trailer_audio'] = $requestData['trailer_audio'];
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_trailer_audio']));
                    } else {
                        $requestData['trailer_audio'] = basename($requestData['old_trailer_audio']);
                    }
                } else {
                    if ($requestData['trailer_audio']) {
                        $requestData['trailer_audio'] = $requestData['trailer_audio'];
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_trailer_audio']));
                    } else {
                        $requestData['trailer_audio'] = "";
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_trailer_audio']));
                    }
                }
            } else {
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_trailer_audio']));
                $requestData['trailer_audio'] = "";
                if ($requestData['url']) {
                    $requestData['trailer_audio'] = $requestData['url'];
                }
            }

            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);
            if (isset($requestData['portrait_img'])) {
                $files = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder, "podcast_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_portrait_img']));
            }
            if (isset($requestData['landscape_img'])) {
                $files = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($files, $this->folder, "podcast_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_landscape_img']));
            }
            unset($requestData['old_portrait_img'], $requestData['old_landscape_img'], $requestData['old_trailer_upload_type'], $requestData['old_trailer_audio'], $requestData['url'], $requestData['old_url']);

            $podcast_data = Podcast::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($podcast_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_podcast')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.podcast_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Podcast::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['portrait_img']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img']);
                $data->delete();

                $episode = Episode::where('podcasts_id', $id)->get();
                for ($i = 0; $i < count($episode); $i++) {
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['portrait_img']);
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['landscape_img']);
                    // JAILAOI: delete episode audio from Bunny before removing DB record
                    if ($episode[$i]['episode_audio'] && getAudioStorageDriver() == 'bunny') {
                        $this->common->deleteFileFromBunny('podcast/' . $episode[$i]['episode_audio']);
                    }
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['episode_audio']);
                    $episode[$i]->delete();
                }

                Banner::where('content_id', $id)->delete();
            }

            return redirect()->route('podcast.index')->with('success', __('label.success_delete_podcast'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Podcast::where('id', $id)->first();
            if ($data) {
                $data->status = $data->status == 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // Episode
    public function PodcastIndex($id, Request $request)
    {
        try {

            $params['data'] = [];
            $params['podcasts_id'] = $id;
            $input_search = $request['input_search'];

            if ($input_search != null && isset($input_search)) {
                $params['data'] = Episode::where('name', 'LIKE', "%{$input_search}%")->where('podcasts_id', $id)->orderBy('sortable', 'asc')->paginate(15);
            } else {
                $params['data'] = Episode::where('podcasts_id', $id)->orderBy('sortable', 'asc')->paginate(15);
            }

            $this->common->imageNameToUrl($params['data'], 'portrait_img', $this->folder);
            $this->common->imageNameToUrl($params['data'], 'landscape_img', $this->folder);
            for ($i = 0; $i < count($params['data']); $i++) {
                if ($params['data'][$i]['episode_upload_type'] == 1) {
                    $this->common->videoNameToUrl(array($params['data'][$i]), 'episode_audio', $this->folder);
                }
            }

            return view('admin.podcast.ep_index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastAdd($id)
    {
        try {

            $params['podcasts_id'] = $id;
            return view('admin.podcast.ep_add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastSave(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'podcasts_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'portrait_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'episode_upload_type' => 'required',
                'duration' => 'required|after_or_equal:00:00:01',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            if ($request->episode_upload_type == 1) {
                $validator2 = Validator::make($request->all(), [
                    'episode_audio' => 'required',
                ]);
            } else {
                $validator2 = Validator::make($request->all(), [
                    'url' => 'required|url',
                ]);
            }
            if ($validator2->fails()) {
                $errs2 = $validator2->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs2));
            }

            $requestData = $request->all();
            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);
            $requestData['total_play'] = 0;

            $files1 = $requestData['portrait_img'];
            $files2 = $requestData['landscape_img'];
            $requestData['portrait_img'] = $this->common->saveImage($files1, $this->folder, "episode_");
            $requestData['landscape_img'] = $this->common->saveImage($files2, $this->folder, "episode_");

            if ($requestData['episode_upload_type'] == 1) {
                $requestData['episode_audio'] = $requestData['episode_audio'];
            } else {
                $requestData['episode_audio'] = $requestData['url'];
            }
            unset($requestData['url']);

            $episode_data = Episode::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($episode_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_episode')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.episode_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastEdit($podcasts_id, $id)
    {
        try {

            $params['data'] = Episode::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['podcasts_id'] = $podcasts_id;

                $this->common->imageNameToUrl(array($params['data']), 'portrait_img', $this->folder);
                $this->common->imageNameToUrl(array($params['data']), 'landscape_img', $this->folder);
                if ($params['data']['episode_upload_type'] == 1) {
                    $this->common->videoNameToUrl(array($params['data']), 'episode_audio', $this->folder);
                }

                return view('admin.podcast.ep_edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastUpdate(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'podcasts_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:2048',
                'episode_upload_type' => 'required',
                'duration' => 'required|after_or_equal:00:00:01',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if ($request->episode_upload_type == 1) {
                $validator2 = Validator::make($request->all(), [
                    'episode_audio' => 'required',
                ]);
            } else {
                $validator2 = Validator::make($request->all(), [
                    'url' => 'required|url',
                ]);
            }
            if ($validator2->fails()) {
                $errs2 = $validator2->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs2));
            }

            $requestData = $request->all();
            $requestData['duration'] = TimeToMilliseconds($requestData['duration']);

            if (isset($requestData['portrait_img'])) {
                $files = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder, 'episode_');
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_portrait_img']));
            }
            if (isset($requestData['landscape_img'])) {
                $files1 = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($files1, $this->folder, 'episode_');
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_landscape_img']));
            }

            if ($requestData['episode_upload_type'] == 1) {

                if ($requestData['episode_upload_type'] == $requestData['old_episode_upload_type']) {

                    if ($requestData['episode_audio']) {

                        $requestData['episode_audio'] = $requestData['episode_audio'];
                        if ($requestData['episode_audio'] != basename($requestData['old_episode_audio'])) {
                            $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']));
                        }
                    } else {
                        $requestData['episode_audio'] = basename($requestData['old_episode_audio']);
                    }
                } else {

                    if ($requestData['episode_audio']) {

                        $requestData['episode_audio'] = $requestData['episode_audio'];
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']));
                    } else {
                        $requestData['episode_audio'] = '';
                    }
                }
            } else {
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']));

                $requestData['episode_audio'] = "";
                if ($requestData['url']) {
                    $requestData['episode_audio'] = $requestData['url'];
                }
            }
            unset($requestData['url'], $requestData['old_episode_upload_type'], $requestData['old_episode_audio'], $requestData['old_portrait_img'], $requestData['old_landscape_img']);

            $episode_data = Episode::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($episode_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_episode')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.episode_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastDelete($podcasts_id, $id)
    {
        try {

            $data = Episode::where('id', $id)->first();
            if (isset($data)) {

                $this->common->deleteImageToFolder($this->folder, $data['portrait_img']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img']);
                $this->common->deleteImageToFolder($this->folder, $data['episode_audio']);
                $data->delete();
            }

            return redirect()->route('podcast.episode.index', $podcasts_id)->with('success', __('label.success_delete_episode'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function PodcastSortable(Request $request)
    {
        try {

            $ids = $request['ids'];

            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Episode::where('id', $id_array[$i])->update(['sortable' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_edit_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function saveChunk()
    {
        @set_time_limit(5 * 60);

        $targetDir = storage_path('/app/public/podcast');
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
            $newFileName = 'podcast' . date('_d_m_Y_') . rand(1111, 9999) . '.' . $extension;

            // JAILAOI: Organize into artist subfolder (podcast/artist-slug/filename.mp3)
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
                    (new \App\Models\Common)->uploadFileToBunny($newFilePath, 'podcast/' . $remoteFileName);
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Bunny upload failed for podcast: ' . $e->getMessage());
                }
            } elseif ($audioDriver == 'r2') {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->put(
                        'podcast/' . $remoteFileName,
                        file_get_contents($newFilePath)
                    );
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('R2 upload failed for podcast: ' . $e->getMessage());
                }
            }

            die(json_encode(array('jsonrpc' => '2.0', 'result' => $remoteFileName, 'id' => 'id')));
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
    public function saveChunkEpisode()
    {
        @set_time_limit(5 * 60);

        $targetDir = storage_path('/app/public/podcast');
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
            $newFileName = 'episode' . date('_d_m_Y_') . rand(1111, 9999) . '.' . $extension; // Use the extracted extension
            $newFilePath = $targetDir . DIRECTORY_SEPARATOR . $newFileName;

            // Rename the uploaded file to the new filename
            rename($filePath, $newFilePath);

            // JAILAOI: Upload to R2 if audio storage driver is r2
            if (getAudioStorageDriver() == 'r2') {
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->put(
                        'podcast/' . $newFileName,
                        file_get_contents($newFilePath)
                    );
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::error('R2 upload failed for episode: ' . $e->getMessage());
                }
            }

            // Send the new file name back to the client
            die(json_encode(array('jsonrpc' => '2.0', 'result' => $newFileName, 'id' => 'id')));
        }
        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
