<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Feed;
use App\Models\General_Setting;
use App\Models\Feed_Comment;
use App\Models\Feed_Content;
use App\Models\Feed_Like;
use App\Models\Feed_Report;
use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    private $folder = "feed";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        if ((General_Setting::where('key', 'feed_status')->value('value') ?? '1') === '0') {
            return redirect()->route('admin.dashboard');
        }
        try {
            $params['data'] = [];
            $params['channel'] = User::latest()->get();

            $input_search = $request['input_search'];
            $input_channel = $request['input_channel'];

            $query = Feed::query();
            if ($input_search) {
                $query->where('description', 'LIKE', "%{$input_search}%");
            }
            if ($input_channel != 0) {
                $query->where('channel_id', $input_channel);
            }
            $params['data'] = $query->orderBy('id', 'DESC')->paginate(20);

            for ($i = 0; $i < count($params['data']); $i++) {

                $feed_content = Feed_Content::where('feed_id', $params['data'][$i]['id'])->first();
                if ($feed_content) {

                    $params['data'][$i]['content_type'] = $feed_content['content_type'];
                    $params['data'][$i]['image_storage_type'] = $feed_content['image_storage_type'];
                    $params['data'][$i]['video_storage_type'] = $feed_content['video_storage_type'];
                    $params['data'][$i]['image'] = $this->common->getImage($this->folder, $feed_content['image'], $feed_content['image_storage_type']);
                    if ($feed_content['content_type'] == 2) {
                        $params['data'][$i]['video'] = $this->common->getVideo($this->folder, $feed_content['video'], $feed_content['video_storage_type']);
                    }
                } else {

                    $params['data'][$i]['content_type'] = 1;
                    $params['data'][$i]['image_storage_type'] = 0;
                    $params['data'][$i]['video_storage_type'] = 0;
                    $params['data'][$i]['image'] = asset('assets/imgs/no_img.png');
                    $params['data'][$i]['video'] = "";
                }
            }
            return view('admin.feed.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {
            $params['data'] = [];
            $params['channel'] = User::latest()->get();

            return view('admin.feed.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'channel_id' => 'required',
                'description' => 'required',
                'is_like' => 'required',
                'is_comment' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $storage_type = Storage_Type();

            $insert = new Feed();
            $insert['channel_id'] = $request['channel_id'];
            $hashtag_id = $this->common->checkHashTag($request['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {

                $hashtagId = implode(',', $hashtag_id);
            }
            $insert['hashtag_id'] = $hashtagId;
            $insert['description'] = $request['description'];
            $insert['is_like'] = $request['is_like'];
            $insert['is_comment'] = $request['is_comment'];
            $insert['total_like'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {

                foreach ($request['content_type'] as $key => $value) {
                    if ($value == 1) { // image

                        if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                            $img_insert = new Feed_Content();
                            $img_insert['feed_id'] = $insert['id'];
                            $img_insert['content_type'] = 1;
                            $img_insert['image_storage_type'] = $storage_type;
                            $img_insert['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                            $img_insert['video_storage_type'] = $storage_type;
                            $img_insert['video'] = "";
                            $img_insert['status'] = 1;
                            $img_insert->save();
                        }
                    } else if ($value == 2) { // video

                        if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null && isset($request['content_video']) && isset($request['content_video'][$key]) && $request['content_video'][$key] != null) {

                            $img_insert = new Feed_Content();
                            $img_insert['feed_id'] = $insert['id'];
                            $img_insert['content_type'] = 2;
                            $img_insert['image_storage_type'] = $storage_type;
                            $img_insert['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                            $img_insert['video_storage_type'] = $storage_type;
                            $img_insert['video'] = $this->common->saveImage($request['content_video'][$key], $this->folder, 'vid_', $storage_type);
                            $img_insert['status'] = 1;
                            $img_insert->save();
                        }
                    }
                }
                return response()->json(['status' => 200, 'success' => __('label.success_add_feed')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_feed')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus(Request $request)
    {
        try {

            $data = Feed::where('id', $request['id'])->first();
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
    public function edit($id)
    {
        try {

            $params['data'] = Feed::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['channel'] = User::latest()->get();

                $params['feed_content'] = Feed_Content::where('feed_id', $id)->get();
                for ($i = 0; $i < count($params['feed_content']); $i++) {

                    $params['feed_content'][$i]['image'] = $this->common->getImage($this->folder, $params['feed_content'][$i]['image'], $params['feed_content'][$i]['image_storage_type']);
                    if ($params['feed_content'][$i]['content_type'] == 2) {
                        $params['feed_content'][$i]['video'] = $this->common->getVideo($this->folder, $params['feed_content'][$i]['video'], $params['feed_content'][$i]['video_storage_type']);
                    }
                }

                return view('admin.feed.edit', $params);
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

            $validator = Validator::make($request->all(), [
                'channel_id' => 'required',
                'description' => 'required',
                'is_like' => 'required',
                'is_comment' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $update = Feed::where('id', $request['id'])->first();
            if ($update) {

                $update['channel_id'] = $request['channel_id'];
                $old_hashtag = explode(',', $request['old_hashtag_id']);
                Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);

                $hashtag_id = $this->common->checkHashTag($request['description']);
                $hashtagId = 0;
                if (count($hashtag_id) > 0) {
                    $hashtagId = implode(',', $hashtag_id);
                }
                $update['hashtag_id'] = $hashtagId;
                $update['description'] = $request['description'];
                $update['is_like'] = $request['is_like'];
                $update['is_comment'] = $request['is_comment'];
                if ($update->save()) {

                    $storage_type = Storage_Type();

                    $existing = Feed_Content::where('feed_id', $request['id'])->get();
                    foreach ($existing as $value) {

                        if (!in_array($value['id'], $request['old_content_id'] ?? [])) {

                            $this->common->deleteImageToFolder($this->folder, $value['image'], $value['image_storage_type']);
                            if ($value['content_type'] == 2) {
                                $this->common->deleteImageToFolder($this->folder, $value['video'], $value['video_storage_type']);
                            }
                            $value->delete();
                        }
                    }

                    foreach ($request['old_content_id'] as $key => $value) {

                        if ($value == null && !isset($value)) {

                            if (isset($request['content_type']) && isset($request['content_type'][$key]) && $request['content_type'][$key] != null) {

                                if ($request['content_type'][$key] == 1) { // image

                                    if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                                        $img_insert = new Feed_Content();
                                        $img_insert['feed_id'] = $request['id'];
                                        $img_insert['content_type'] = 1;
                                        $img_insert['image_storage_type'] = $storage_type;
                                        $img_insert['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                        $img_insert['video_storage_type'] = $storage_type;
                                        $img_insert['video'] = "";
                                        $img_insert['status'] = 1;
                                        $img_insert->save();
                                    }
                                } else if ($request['content_type'][$key] == 2) { // video

                                    if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null && isset($request['content_video']) && isset($request['content_video'][$key]) && $request['content_video'][$key] != null) {

                                        $img_insert = new Feed_Content();
                                        $img_insert['feed_id'] = $request['id'];
                                        $img_insert['content_type'] = 2;
                                        $img_insert['image_storage_type'] = $storage_type;
                                        $img_insert['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                        $img_insert['video_storage_type'] = $storage_type;
                                        $img_insert['video'] = $this->common->saveImage($request['content_video'][$key], $this->folder, 'vid_', $storage_type);
                                        $img_insert['status'] = 1;
                                        $img_insert->save();
                                    }
                                }
                            }
                        } else {

                            if (
                                isset($request['content_type']) && isset($request['content_type'][$key]) && $request['content_type'][$key] != null &&
                                isset($request['old_content_type']) && isset($request['old_content_type'][$key]) && $request['old_content_type'][$key] != null
                            ) {

                                if ($request['content_type'][$key] == $request['old_content_type'][$key]) {

                                    if ($request['content_type'][$key] == 1) { // image

                                        $fetch_data = Feed_Content::where('id', $value)->first();
                                        if ($fetch_data) {

                                            if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                                                $old_image_storage_type = $fetch_data['image_storage_type'];
                                                $fetch_data['image_storage_type'] = $storage_type;
                                                $fetch_data['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_image'][$key] ?? "", $old_image_storage_type);
                                                $fetch_data->save();
                                            }
                                        }
                                    } else if ($request['content_type'][$key] == 2) { // video

                                        $fetch_data = Feed_Content::where('id', $value)->first();
                                        if ($fetch_data) {

                                            if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                                                $old_image_storage_type = $fetch_data['image_storage_type'];
                                                $fetch_data['image_storage_type'] = $storage_type;
                                                $fetch_data['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_image'][$key] ?? "", $old_image_storage_type);
                                            }
                                            if (isset($request['content_video']) && isset($request['content_video'][$key]) && $request['content_video'][$key] != null) {

                                                $old_video_storage_type = $fetch_data['video_storage_type'];
                                                $fetch_data['video_storage_type'] = $storage_type;
                                                $fetch_data['video'] = $this->common->saveImage($request['content_video'][$key], $this->folder, 'vid_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_video'][$key] ?? "", $old_video_storage_type);
                                            }
                                            $fetch_data->save();
                                        }
                                    }
                                } else {

                                    if ($request['content_type'][$key] == 1) { // image

                                        $this->common->deleteImageToFolder($this->folder, $request['old_content_video'][$key] ?? "", $request['old_video_storage_type'][$key] ?? 0);

                                        $fetch_data = Feed_Content::where('id', $value)->first();
                                        if ($fetch_data) {

                                            $fetch_data['content_type'] = 1;
                                            $fetch_data['video'] = "";
                                            $fetch_data['video_storage_type'] = $storage_type;
                                            if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                                                $old_image_storage_type = $fetch_data['image_storage_type'];
                                                $fetch_data['image_storage_type'] = $storage_type;
                                                $fetch_data['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_image'][$key] ?? "", $old_image_storage_type);
                                            }
                                            $fetch_data->save();
                                        }
                                    } else if ($request['content_type'][$key] == 2) { // video

                                        $fetch_data = Feed_Content::where('id', $value)->first();
                                        if ($fetch_data) {

                                            $fetch_data['content_type'] = 2;
                                            if (isset($request['content_image']) && isset($request['content_image'][$key]) && $request['content_image'][$key] != null) {

                                                $old_image_storage_type = $fetch_data['image_storage_type'];
                                                $fetch_data['image_storage_type'] = $storage_type;
                                                $fetch_data['image'] = $this->common->saveImage($request['content_image'][$key], $this->folder, 'img_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_image'][$key] ?? "", $old_image_storage_type);
                                            }
                                            if (isset($request['content_video']) && isset($request['content_video'][$key]) && $request['content_video'][$key] != null) {

                                                $old_video_storage_type = $fetch_data['video_storage_type'];
                                                $fetch_data['video_storage_type'] = $storage_type;
                                                $fetch_data['video'] = $this->common->saveImage($request['content_video'][$key], $this->folder, 'vid_', $storage_type);
                                                $this->common->deleteImageToFolder($this->folder, $request['old_content_video'][$key] ?? "", $old_video_storage_type);
                                            }
                                            $fetch_data->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return response()->json(['status' => 200, 'success' => __('label.success_edit_feed')]);
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.error_edit_feed')]);
                }
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_feed')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show(string $id)
    {
        try {

            $data = Feed::where('id', $id)->first();
            if (isset($data)) {

                $old_hashtag = explode(',', $data['hashtag_id']);
                Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);

                $post_content = Feed_Content::where('feed_id', $data['id'])->get();
                foreach ($post_content as $post) {
                    $this->common->deleteImageToFolder($this->folder, $post['image'], $post['image_storage_type']);
                    $this->common->deleteImageToFolder($this->folder, $post['video'], $post['video_storage_type']);
                    $post->delete();
                }
                $data->delete();

                // Releted Data
                Feed_Report::where('feed_id', $id)->delete();
                Feed_Like::where('feed_id', $id)->delete();
                Feed_Comment::where('feed_id', $id)->delete();
            }
            return redirect(route('admin.feed.index'))->with('success', __('label.feed_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
