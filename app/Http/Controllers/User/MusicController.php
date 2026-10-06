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
    private $folder     = "music";           // audio folder on CDN/disk
    private $folder_img = "images/music";    // JAILAOI: image folder on Bunny CDN
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

                $params['data'][$i]['portrait_img'] = $this->common->getImage($this->folder_img, $params['data'][$i]['portrait_img'], $params['data'][$i]['portrait_img_storage_type']);
                $params['data'][$i]['landscape_img'] = $this->common->getImage($this->folder_img, $params['data'][$i]['landscape_img'], $params['data'][$i]['landscape_img_storage_type']);
                if ($params['data'][$i]['content_upload_type'] == 'server_video') {
                    $params['data'][$i]['content'] = $this->common->getVideo($this->folder, $params['data'][$i]['content'], $params['data'][$i]['content_storage_type']);
                }
            }
            return view('user.music.index', $params);
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
        }
    }
    public function create()
    {
        try {
            $params['category'] = Category::orderBy('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->latest()->get();

            return view('user.music.add', $params);
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
        }
    }
    // Pre-upload audio file immediately on select — returns stored filename.
    // store()/update() then just save the filename string; no second upload needed.
    public function uploadAudio(Request $request)
    {
        // Large audio files can take several minutes to relay to Bunny CDN
        set_time_limit(300);

        try {
            $validator = Validator::make($request->all(), [
                'audio' => 'required|file|mimes:mp3,m4a,aac,flac,wav,ogg|max:307200',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }
            $user = User_Data();
            $artist = \App\Models\Artist::where('user_id', $user['id'] ?? 0)->first();
            $artistSlug = $artist ? (Str::slug($artist->name, '-') ?: 'various') : 'various';
            $filename = $this->common->saveAudioFile($request->file('audio'), $this->folder, 'music_', $artistSlug);
            \Illuminate\Support\Facades\Cache::put('artist-upload:' . $user['id'] . ':' . hash('sha256', (string) $filename), true, now()->addDay());
            return response()->json(['status' => 200, 'filename' => $filename]);
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = User_Data();

            $youtubeImport = null;
            $youtubeImportId = $request->input('youtube_audio_import_id') ?: \Illuminate\Support\Facades\Cache::get('artist-youtube-upload:'.$user['id'].':'.hash('sha256', (string)$request->music));
            if ($youtubeImportId) {
                $youtubeImport = \App\Models\YouTubeAudioImport::where('user_id', $user['id'])->find($youtubeImportId);
                if ($request->content_upload_type !== 'server_video' || !$youtubeImport || $youtubeImport->status !== 'ready' || $youtubeImport->expires_at->isPast() || $youtubeImport->content_id || $youtubeImport->filename !== $request->music || $youtubeImport->storage_driver !== getAudioStorageDriver()) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['music'=>'Imported audio is unavailable or already published. Import again or upload manually.']);
                }
                $seconds = $youtubeImport->duration_seconds;
                $request->merge(['content_duration'=>sprintf('%02d:%02d:%02d', intdiv($seconds,3600), intdiv($seconds%3600,60), $seconds%60)]);
            }
            $rules = [
                'title' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
                'content_upload_type' => 'required',
                'content_duration' => 'required|after_or_equal:00:00:01',
                'is_comment' => 'required',
                'is_download' => 'required',
                'is_like' => 'required',
            ];
            if ($request['content_upload_type'] == 'server_video') {
                $rules['music'] = 'required|string'; // pre-uploaded filename from uploadAudio()
            } else {
                $rules['url'] = 'required|url';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            if ($request->content_upload_type === 'server_video' && !\Illuminate\Support\Facades\Cache::get('artist-upload:' . $user['id'] . ':' . hash('sha256', (string) $request->music))) {
                return response()->json(['status'=>422, 'errors'=>'Please upload your audio again. Uploads expire after 24 hours.'], 422);
            }
            $requestData = $request->only(['id', 'title', 'description', 'lyrics', 'album_id', 'category_id', 'language_id', 'hashtag_id', 'portrait_img', 'landscape_img', 'content_upload_type', 'content_duration', 'content', 'url', 'is_comment', 'is_like', 'is_download', 'is_rent', 'rent_price', 'rent_day', 'old_portrait_img', 'old_landscape_img', 'old_content', 'old_hashtag_id', 'old_portrait_img_storage_type', 'old_landscape_img_storage_type', 'old_content_storage_type', 'old_content_upload_type', 'music']);
            $storage_type = Storage_Type();
            $requestData['portrait_img_storage_type'] = $storage_type;
            $requestData['landscape_img_storage_type'] = $storage_type;
            $requestData['content_storage_type'] = $storage_type;

            $requestData['channel_id'] = $user['channel_id'];
            $requestData['content_type'] = 2;
            $requestData['description'] = $requestData['description'] ?? "";
            $requestData['lyrics'] = $requestData['lyrics'] ?? '';
            $requestData['album_id'] = ($requestData['album_id'] ?? null) ?: null;
            $hashtag_id = $this->common->checkHashTag($requestData['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            $artistObj = \App\Models\Artist::where('user_id', $user['id'] ?? 0)->first();
            $artistSlug = $artistObj ? (Str::slug($artistObj->name, '-') ?: 'various') : 'various';
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($file1, $this->folder_img, 'port_', $artistSlug);
            } else {
                $requestData['portrait_img'] = "";
            }
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($file2, $this->folder_img, 'land_', $artistSlug);
            } else {
                $requestData['landscape_img'] = "";
            }
            if ($requestData['content_upload_type'] == 'server_video') {
                // music is a pre-uploaded filename returned by uploadAudio()
                $requestData['content'] = $requestData['music'];
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

            unset($requestData['id']);
            foreach (array_keys($requestData) as $field) {
                if (str_starts_with($field, 'old_')) unset($requestData[$field]);
            }
            $data = \Illuminate\Support\Facades\DB::transaction(function () use ($requestData, $youtubeImport) {
                $lockedImport = null;
                if ($youtubeImport) {
                    $lockedImport = \App\Models\YouTubeAudioImport::whereKey($youtubeImport->id)->lockForUpdate()->first();
                    if (!$lockedImport || $lockedImport->content_id || $lockedImport->status !== 'ready' || $lockedImport->expires_at->isPast()) throw \Illuminate\Validation\ValidationException::withMessages(['music'=>'This import is unavailable or already published.']);
                }
                $content = Content::create($requestData);
                $this->mirrorToMusic($content, $requestData);
                if ($lockedImport) $lockedImport->update(['content_id'=>$content->id]);
                return $content;
            });
            if (isset($data->id)) {
                // JAILAOI: Mirror to tbl_music so the Flutter app can play it immediately

                return response()->json(['status' => 200, 'success' => __('label.success_add_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_music')]);
            }
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
        }
    }
    public function edit($id)
    {
        try {
            $params['data'] = Content::where('id', $id)->where('content_type', 2)->where('channel_id', User_Data()['channel_id'])->first();
            if ($params['data'] != null) {

                $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
                $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

                $params['data']['portrait_img'] = $this->common->getImage($this->folder_img, $params['data']['portrait_img'], $params['data']['portrait_img_storage_type']);
                $params['data']['landscape_img'] = $this->common->getImage($this->folder_img, $params['data']['landscape_img'], $params['data']['landscape_img_storage_type']);
                if ($params['data']['content_upload_type'] == 'server_video') {
                    $params['data']['content'] = $this->common->getVideo($this->folder, $params['data']['content'], $params['data']['content_storage_type']);
                }
                return view('user.music.edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
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
                'portrait_img' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
                'content_upload_type' => 'required',
                'content_duration' => 'required|after_or_equal:00:00:01',
                'is_comment' => 'required',
                'is_like' => 'required',
                'is_download' => 'required',
            ];
            if ($request['content_upload_type'] != 'server_video') {
                $rules['url'] = 'required|url';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->only(['id', 'title', 'description', 'lyrics', 'album_id', 'category_id', 'language_id', 'hashtag_id', 'portrait_img', 'landscape_img', 'content_upload_type', 'content_duration', 'content', 'url', 'is_comment', 'is_like', 'is_download', 'is_rent', 'rent_price', 'rent_day', 'old_portrait_img', 'old_landscape_img', 'old_content', 'old_hashtag_id', 'old_portrait_img_storage_type', 'old_landscape_img_storage_type', 'old_content_storage_type', 'old_content_upload_type', 'music']);
            $ownedContent = Content::where('id', $request->id)->where('content_type', 2)->where('channel_id', $user['channel_id'])->first();
            if (!$ownedContent) {
                return response()->json(['status' => 404, 'message' => 'Music not found.'], 404);
            }
            if ($request->content_upload_type === 'server_video' && $ownedContent->content_upload_type !== 'server_video' && !$request->music) {
                return response()->json(['status'=>422, 'errors'=>'Please upload an audio file before changing upload type.'], 422);
            }
            if ($request->music && $request->music !== $ownedContent->content && !\Illuminate\Support\Facades\Cache::get('artist-upload:' . $user['id'] . ':' . hash('sha256', (string) $request->music))) {
                return response()->json(['status'=>422, 'errors'=>'Please upload your audio again. Uploads expire after 24 hours.'], 422);
            }
            $requestData['content'] = $ownedContent->content;
            $requestData['music'] = $requestData['music'] ?? '';
            $obsoleteMedia = [];
            foreach (['portrait_img', 'landscape_img', 'content', 'content_upload_type', 'hashtag_id', 'portrait_img_storage_type', 'landscape_img_storage_type', 'content_storage_type'] as $field) {
                $requestData['old_' . $field] = $ownedContent->$field;
            }
            $storage_type = Storage_Type();

            $requestData['channel_id'] = $user['channel_id'];
            $requestData['content_type'] = 2;
            $requestData['description'] = $requestData['description'] ?? "";
            $requestData['lyrics'] = $requestData['lyrics'] ?? $ownedContent->lyrics ?? '';
            $requestData['album_id'] = array_key_exists('album_id', $requestData) ? ($requestData['album_id'] ?: null) : $ownedContent->album_id;
            $old_hashtag = explode(',', $requestData['old_hashtag_id']);
            Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);
            $hashtag_id = $this->common->checkHashTag($requestData['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            $artistObj = \App\Models\Artist::where('user_id', $user['id'] ?? 0)->first();
            $artistSlug = $artistObj ? (Str::slug($artistObj->name, '-') ?: 'various') : 'various';
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $requestData['portrait_img_storage_type'] = $storage_type;
                $requestData['portrait_img'] = $this->common->saveImage($file1, $this->folder_img, 'port_', $artistSlug);

                $obsoleteMedia[] = [$this->folder_img, $requestData['old_portrait_img']];
            }
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $requestData['landscape_img_storage_type'] = $storage_type;
                $requestData['landscape_img'] = $this->common->saveImage($file2, $this->folder_img, 'land_', $artistSlug);

                $obsoleteMedia[] = [$this->folder_img, $requestData['old_landscape_img']];
            }
            if ($requestData['content_upload_type'] == 'server_video') {

                if ($requestData['content_upload_type'] == $requestData['old_content_upload_type']) {

                    if ($requestData['music']) {

                        $requestData['content_storage_type'] = $storage_type;
                        // music is a pre-uploaded filename from uploadAudio()
                        $requestData['content'] = $requestData['music'];
                        $obsoleteMedia[] = [$this->folder, $requestData['old_content']];
                    }
                } else {

                    $requestData['content_storage_type'] = $storage_type;
                    if ($requestData['music']) {

                        // music is a pre-uploaded filename from uploadAudio()
                        $requestData['content'] = $requestData['music'];
                        $obsoleteMedia[] = [$this->folder, $requestData['old_content']];
                    } else {
                        $requestData['content'] = '';
                    }
                }
            } else {

                $requestData['content_storage_type'] = $storage_type;
                $obsoleteMedia[] = [$this->folder, $requestData['old_content']];

                $requestData['content'] = "";
                if ($requestData['url']) {
                    $requestData['content'] = $requestData['url'];
                }
            }
            unset($requestData['music'], $requestData['url'], $requestData['old_content_upload_type'], $requestData['old_hashtag_id'], $requestData['old_content'], $requestData['old_portrait_img'], $requestData['old_landscape_img'], $requestData['old_portrait_img_storage_type'], $requestData['old_landscape_img_storage_type'], $requestData['old_content_storage_type']);
            $requestData['content_duration'] = $this->common->time_to_milliseconds($requestData['content_duration']);

            $data = \Illuminate\Support\Facades\DB::transaction(function () use ($requestData) {
                $content = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
                $this->mirrorToMusic($content, $requestData);
                return $content;
            });
            foreach ($obsoleteMedia as $media) {
                // Never delete a file still referenced by the saved track.
                if (in_array($media[1], [$data->content, $data->portrait_img, $data->landscape_img], true)) continue;
                try { $this->common->deleteImageToFolder(...$media); }
                catch (Exception $cleanupError) { Log::warning('Old artist media cleanup failed.'); }
            }
            if (isset($data->id)) {
                // JAILAOI: Keep tbl_music mirror in sync

                return response()->json(['status' => 200, 'success' => __('label.success_edit_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_music')]);
            }
        } catch (Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
        }
    }
    public function show($id)
    {
        try {

            $data = Content::where('id', $id)->where('content_type', 2)->where('channel_id', User_Data()['channel_id'])->first();
            if (isset($data)) {

                $old_hashtag = explode(',', $data['hashtag_id']);
                Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);

                $this->common->deleteImageToFolder($this->folder_img, $data['portrait_img'], $data['portrait_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder_img, $data['landscape_img'], $data['landscape_img_storage_type']);
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
            if ($e instanceof \Illuminate\Validation\ValidationException) return response()->json(['status'=>422, 'errors'=>$e->validator->errors()->all()], 422);
            \Illuminate\Support\Facades\Log::warning('Artist portal operation failed', ['controller'=>static::class, 'exception'=>get_class($e)]);
            return response()->json(['status'=>500, 'errors'=>'Unable to complete this action. Please try again or contact support.'], 500);
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
            if (!$artist) throw new \RuntimeException('Artist profile is required to synchronize music.');

            $artistId  = (string) $artist->id;
            $uploadType = (($req['content_upload_type'] ?? '') === 'server_video') ? 1 : 2;
            $duration   = $content->content_duration ?? 0;

            $existingMirror = Music::where('jailaoi_content_id', $content->id)->first();
            Music::updateOrCreate(
                ['jailaoi_content_id' => $content->id],
                [
                    'title'        => $content->title ?? '',
                    'artist_id'    => $artistId,
                    'album_name'   => $content->album_id ? (\App\Models\Album::find($content->album_id)->name ?? '') : '',
                    'category_id'  => $content->category_id ?? 0,
                    'language_id'  => $content->language_id ?? 0,
                    'duration'     => $duration,
                    'upload_type'  => $uploadType,
                    'music'        => $content->content ?? '',
                    'lyrics'       => $content->lyrics ?? '',
                    'description'  => $content->description ?? '',
                    'portrait_img' => $content->portrait_img ?? '',
                    'landscape_img'=> $content->landscape_img ?? '',
                    'ogtag_img'    => '',
                    'status'       => $existingMirror ? $existingMirror->status : ($content->status ?? 1),
                ]
            );
        } catch (Exception $e) {
            Log::error('mirrorToMusic failed for content#' . $content->id . ': ' . $e->getMessage());
            throw $e;
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
