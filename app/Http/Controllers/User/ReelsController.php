<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Hashtag;
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

class ReelsController extends Controller
{
    private $folder = "content";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $user = User_Data();

            $input_search = $request['input_search'];

            $query = Content::where('content_type', 3)->where('channel_id', $user['channel_id']);
            if ($input_search) {
                $query->where('title', 'LIKE', "%{$input_search}%");
            }
            $params['data'] = $query->orderBy('id', 'DESC')->paginate(20);

            for ($i = 0; $i < count($params['data']); $i++) {

                $params['data'][$i]['portrait_img'] = $this->common->getImage($this->folder, $params['data'][$i]['portrait_img'], $params['data'][$i]['portrait_img_storage_type']);
                if ($params['data'][$i]['content_upload_type'] == 'server_video') {
                    $params['data'][$i]['content'] = $this->common->getVideo($this->folder, $params['data'][$i]['content'], $params['data'][$i]['content_storage_type']);
                }
            }
            return view('user.reels.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            return view('user.reels.add', $params);
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
                'portrait_img' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'is_comment' => 'required',
                'is_like' => 'required',
                'is_download' => 'required',
                'video' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $storage_type = Storage_Type();
            $requestData['portrait_img_storage_type'] = $storage_type;
            $requestData['content_storage_type'] = $storage_type;
            $requestData['landscape_img_storage_type'] = 0;

            $requestData['channel_id'] = $user['channel_id'];
            $requestData['content_type'] = 3;
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $hashtag_id = $this->common->checkHashTag($requestData['title']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            $requestData['description'] = "";
            $file1 = $requestData['portrait_img'];
            $requestData['portrait_img'] = $this->common->saveImage($file1, $this->folder, 'port_', $requestData['portrait_img_storage_type']);
            $requestData['landscape_img'] = "";
            $requestData['content_upload_type'] = "server_video";
            if ($requestData['content_storage_type'] == 1) {
                $requestData['content'] = $requestData['video'];
            } else {
                $requestData['content'] = $this->common->saveImage($requestData['video'], $this->folder, 'reels_', $requestData['content_storage_type']);
            }
            unset($requestData['video']);
            $requestData['content_duration'] = 0;
            $requestData['is_rent'] = 0;
            $requestData['rent_price'] = 0;
            $requestData['rent_day'] = 0;
            $requestData['total_view'] = 0;
            $requestData['total_like'] = 0;
            $requestData['total_dislike'] = 0;
            $requestData['playlist_type'] = 0;
            $requestData['total_watch_time'] = 0;
            $requestData['status'] = 1;

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_reels')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_reels')]);
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

                $params['data']['portrait_img'] = $this->common->getImage($this->folder, $params['data']['portrait_img'], $params['data']['portrait_img_storage_type']);
                if ($params['data']['content_upload_type'] == 'server_video') {
                    $params['data']['content'] = $this->common->getVideo($this->folder, $params['data']['content'], $params['data']['content_storage_type']);
                }
                return view('user.reels.edit', $params);
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
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'is_comment' => 'required',
                'is_like' => 'required',
                'is_download' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $requestData['channel_id'] = $user['channel_id'];
            $requestData['content_type'] = 3;
            $old_hashtag = explode(',', $requestData['old_hashtag_id']);
            Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);
            $hashtag_id = $this->common->checkHashTag($requestData['title']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $requestData['hashtag_id'] = $hashtagId;
            if (isset($requestData['portrait_img'])) {

                $file = $requestData['portrait_img'];
                $requestData['portrait_img_storage_type'] = $storage_type;
                $requestData['portrait_img'] = $this->common->saveImage($file, $this->folder, 'port_', $requestData['portrait_img_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_portrait_img']), $request['old_portrait_img_storage_type']);
            }
            if ($requestData['video']) {

                $requestData['content_storage_type'] = $storage_type;
                if ($storage_type == 1) {
                    $requestData['content'] = $requestData['video'];
                } else {
                    $requestData['content'] = $this->common->saveImage($requestData['video'], $this->folder, 'reels_', $storage_type);
                }
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_content']), $request['old_content_storage_type']);
            }
            unset($requestData['video'], $requestData['old_hashtag_id'], $requestData['old_content'], $requestData['old_portrait_img'], $requestData['old_portrait_img_storage_type'], $requestData['old_content_storage_type']);

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_reels')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_reels')]);
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
            return redirect()->route('user.reels.index')->with('success', __('label.reels_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
