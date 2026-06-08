<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Language;
use App\Models\Hashtag;
use App\Models\Content;
use App\Models\Content_Like;
use App\Models\Content_Report;
use App\Models\Content_View;
use App\Models\History;
use App\Models\Music;
use App\Models\Notification;
use App\Models\Watch_later;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class MusicController extends Controller
{
    private $folder = "music";
    private $folder_ffmpeg = "/app/public/music/";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $user = User_Data();
            $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

            $input_search = $request['input_search'];
            $input_category = $request['input_category'];
            $input_language = $request['input_language'];

            $query = Content::where('content_type', 2)->where('channel_id', $user['channel_id']);
            if ($input_search) {
                $query->where('title', 'LIKE', "%{$input_search}%");
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
            return view('user.music.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {
            $params['category'] = Category::orderBy('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->latest()->get();

            return view('user.music.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $user = User_Data();

            $rules = [
                'title' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'content_upload_type' => 'required',
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

            $requestData['channel_id'] = $user['channel_id'];
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
                    // JAILAOI: Pass artist slug so file is stored in artist subfolder on CDN
                    $artistSlug = \Illuminate\Support\Str::slug(User_Data()['name'] ?? 'various', '-');
                    $requestData['content'] = $this->common->saveAudioFile($requestData['music'], $this->folder, 'music_', $artistSlug);
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
                // JAILAOI: Mirror to tbl_music so the Flutter app can play it immediately
                $this->mirrorToMusic($data, $requestData);
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

                $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
                $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

                $params['data']['portrait_img'] = $this->common->getImage($this->folder, $params['data']['portrait_img'], $params['data']['portrait_img_storage_type']);
                $params['data']['landscape_img'] = $this->common->getImage($this->folder, $params['data']['landscape_img'], $params['data']['landscape_img_storage_type']);
                if ($params['data']['content_upload_type'] == 'server_video') {
                    $params['data']['content'] = $this->common->getVideo($this->folder, $params['data']['content'], $params['data']['content_storage_type']);
                }
                return view('user.music.edit', $params);
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
            $user = User_Data();

            $rules = [
                'title' => 'required',
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

            $requestData['channel_id'] = $user['channel_id'];
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
                            $requestData['content'] = $this->common->saveAudioFile($requestData['music'], $this->folder, 'music_');
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
                            $requestData['content'] = $this->common->saveAudioFile($requestData['music'], $this->folder, 'music_');
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
                // JAILAOI: Keep tbl_music mirror in sync
                $this->mirrorToMusic($data, $requestData);
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
                // JAILAOI: Also remove the tbl_music mirror
                $this->deleteMusicMirror($data);
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
            return redirect()->route('user.music.index')->with('success', __('label.music_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // JAILAOI: Mirror a Content (artist portal) record into tbl_music so the Flutter app can play it.
    // Writes to tbl_music with status=1 (auto-live) — no admin approval needed for audio uploads.
    // Uses content_id as the stable key so edit/re-upload stays in sync.
    private function mirrorToMusic(Content $content, array $req): void
    {
        try {
            $user   = User_Data();
            $artist = Artist::where('user_id', $user['id'])->first();
            if (!$artist) return; // Not a registered artist — skip

            $artistId  = (string) $artist->id;
            $uploadType = (($req['content_upload_type'] ?? '') === 'server_video') ? 1 : 2;
            $duration   = $content->content_duration ?? 0;

            Music::updateOrCreate(
                ['jailaoi_content_id' => $content->id],
                [
                    'title'        => $content->title ?? '',
                    'artist_id'    => $artistId,
                    'album_name'   => '',
                    'categroy_id'  => $content->category_id ?? 0,
                    'language_id'  => $content->language_id ?? 0,
                    'is_premium'   => 0,
                    'duration'     => $duration,
                    'upload_type'  => $uploadType,
                    'music'        => $content->content ?? '',
                    'description'  => $content->description ?? '',
                    'portrait_img' => $content->portrait_img ?? '',
                    'landscape_img'=> '',
                    'ogtag_img'    => '',
                    'total_play'   => 0,
                    'status'       => 1,
                ]
            );
        } catch (Exception $e) {
            Log::error('mirrorToMusic failed for content#' . $content->id . ': ' . $e->getMessage());
            // Never throw — don't let mirror failure break the artist portal
        }
    }

    // JAILAOI: Remove the tbl_music mirror when artist deletes from their portal.
    private function deleteMusicMirror(Content $content): void
    {
        try {
            $mirror = Music::where('jailaoi_content_id', $content->id)->first();
            if ($mirror) {
                if ($mirror->music && getAudioStorageDriver() == 'bunny') {
                    $this->common->deleteFileFromBunny('music/' . $mirror->music);
                }
                $mirror->delete();
            }
        } catch (Exception $e) {
            Log::error('deleteMusicMirror failed for content#' . $content->id . ': ' . $e->getMessage());
        }
    }
}
