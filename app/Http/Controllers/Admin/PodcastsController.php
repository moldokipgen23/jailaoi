<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Content;
use App\Models\Content_Like;
use App\Models\Content_Report;
use App\Models\Content_View;
use App\Models\Episode;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Language;
use App\Models\Notification;
use App\Models\User;
use App\Models\Watch_later;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PodcastsController extends Controller
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
            $params['data'] = [];
            $params['channel'] = User::latest()->get();
            $params['category'] = Category::orderby('sort_order', 'asc')->latest()->get();
            $params['language'] = Language::orderby('sort_order', 'asc')->latest()->get();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_channel = $request['input_channel'];

                $query = Content::where('content_type', 4);
                if ($input_search) {
                    $query->where('title', 'LIKE', "%{$input_search}%");
                }
                if ($input_channel != 0) {
                    $query->where('channel_id', $input_channel);
                }
                $params['data'] = $query->with('channel')->latest()->get();

                $this->common->imageNameToUrl($params['data'], 'portrait_img', $this->folder, 'portrait_img_storage_type');
                $this->common->imageNameToUrl($params['data'], 'landscape_img', $this->folder, 'landscape_img_storage_type');

                return DataTables()::of($params['data'])
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $podcasts_delete = __('label.delete_podcasts');

                        $delete = '<form onsubmit="return confirm(\'' . $podcasts_delete . '\');" method="POST" action="' . route('admin.podcasts.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a class="edit-delete-btn mr-2 edit_podcasts" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-channel_id="' . $row->channel_id . '" data-hashtag_id="' . $row->hashtag_id . '" data-title="' . $row->title . '" data-portrait_img="' . $row->portrait_img . '" data-landscape_img="' . $row->landscape_img . '" data-description="' . $row->description . '" data-category_id="' . $row->category_id . '" data-language_id="' . $row->language_id . '" data-portrait_img_storage_type="' . $row->portrait_img_storage_type . '" data-landscape_img_storage_type="' . $row->landscape_img_storage_type . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            $showLabel = __('label.show');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.hide');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->addColumn('episode', function ($row) {
                        $Label = __('label.episode_list');
                        $btn = "<a href='" . route('admin.podcast.episode.index', $row->id) . "' class='info-btn p-2'>$Label</a>";
                        return $btn;
                    })
                    ->rawColumns(['action', 'episode', 'status'])
                    ->make(true);
            }
            return view('admin.podcasts.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $storage_type = Storage_Type();
            $requestData['portrait_img_storage_type'] = $storage_type;
            $requestData['landscape_img_storage_type'] = $storage_type;
            $requestData['content_storage_type'] = 0;

            $requestData['content_type'] = 4;
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
            $requestData['content_upload_type'] = '';
            $requestData['content'] = '';
            $requestData['content_duration'] = 0;
            $requestData['is_rent'] = 0;
            $requestData['rent_price'] = 0;
            $requestData['rent_day'] = 0;
            $requestData['is_comment'] = 0;
            $requestData['is_download'] = 0;
            $requestData['is_like'] = 0;
            $requestData['total_view'] = 0;
            $requestData['total_like'] = 0;
            $requestData['total_dislike'] = 0;
            $requestData['playlist_type'] = 0;
            $requestData['total_watch_time'] = 0;
            $requestData['status'] = 1;

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_podcasts')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_podcasts')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();
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
            unset($requestData['old_portrait_img'], $requestData['old_landscape_img'], $requestData['old_hashtag_id'], $requestData['old_portrait_img_storage_type'], $requestData['old_landscape_img_storage_type']);

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_podcasts')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_podcasts')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Content::where('id', $id)->first();
            if (isset($data)) {

                $old_hashtag = explode(',', $data['hashtag_id']);
                Hashtag::whereIn('id', $old_hashtag)->decrement('total_used', 1);

                $this->common->deleteImageToFolder($this->folder, $data['portrait_img'], $data['portrait_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img'], $data['landscape_img_storage_type']);
                $data->delete();

                $episode = Episode::where('podcasts_id', $id)->get();
                for ($i = 0; $i < count($episode); $i++) {
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['portrait_img'], $episode[$i]['portrait_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['landscape_img'], $episode[$i]['landscape_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder, $episode[$i]['episode_audio'], $episode[$i]['episode_storage_type']);
                    $episode[$i]->delete();
                }

                // Content Releted Data Delete
                Comment::where('content_id', $id)->delete();
                Content_Report::where('content_id', $id)->delete();
                History::where('content_id', $id)->delete();
                Notification::where('content_id', $id)->delete();
                Content_Like::where('content_id', $id)->delete();
                Content_View::where('content_id', $id)->delete();
                Watch_later::where('content_id', $id)->delete();
            }
            return redirect()->route('admin.podcasts.index')->with('success', __('label.podcasts_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Content::where('id', $id)->first();
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

    // Episode
    public function ep_index($id, Request $request)
    {
        try {

            $params['data'] = [];
            $params['podcasts_id'] = $id;
            $params['sort_order_data'] = Episode::where('podcasts_id', $id)->orderBy('sort_order', 'asc')->get();

            $input_search = $request['input_search'];

            $query = Episode::where('podcasts_id', $id);
            if ($input_search) {
                $query->where('name', 'LIKE', "%{$input_search}%");
            }
            $params['data'] = $query->orderBy('sort_order', 'asc')->paginate(20);

            for ($i = 0; $i < count($params['data']); $i++) {

                $params['data'][$i]['portrait_img'] = $this->common->getImage($this->folder, $params['data'][$i]['portrait_img'], $params['data'][$i]['portrait_img_storage_type']);
                $params['data'][$i]['landscape_img'] = $this->common->getImage($this->folder, $params['data'][$i]['landscape_img'], $params['data'][$i]['landscape_img_storage_type']);
                if ($params['data'][$i]['episode_upload_type'] == 'server_audio') {
                    $params['data'][$i]['episode_audio'] = $this->common->getVideo($this->folder, $params['data'][$i]['episode_audio'], $params['data'][$i]['episode_storage_type']);
                }
            }
            return view('admin.podcasts.ep_index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_add($id)
    {
        try {
            $params['podcasts_id'] = $id;
            return view('admin.podcasts.ep_add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_save(Request $request)
    {
        try {
            $rules = [
                'podcasts_id' => 'required',
                'name' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'episode_upload_type' => 'required',
                'is_download' => 'required',
                'is_comment' => 'required',
                'is_like' => 'required',
            ];
            if ($request['episode_upload_type'] == 'server_audio') {
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
            $requestData['episode_storage_type'] = $storage_type;
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
            if ($requestData['episode_upload_type'] == 'server_audio') {

                if ($requestData['episode_storage_type'] == 1) {
                    $requestData['episode_audio'] = $requestData['music'];
                } else {
                    $requestData['episode_audio'] = $this->common->saveImage($requestData['music'], $this->folder, 'ep_vid_', $requestData['episode_storage_type']);
                }
            } else {
                $requestData['episode_audio'] = $requestData['url'];
            }
            unset($requestData['music'], $requestData['url']);
            $requestData['description'] = $requestData['description'] ?? "";
            $requestData['total_view'] = 0;
            $requestData['total_like'] = 0;
            $requestData['total_dislike'] = 0;
            $requestData['sort_order'] = 0;
            $requestData['status'] = 1;

            $data = Episode::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_episode')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_episode')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_edit($podcasts_id, $id)
    {
        try {

            $params['data'] = Episode::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['podcasts_id'] = $podcasts_id;

                $params['data']['portrait_img'] = $this->common->getImage($this->folder, $params['data']['portrait_img'], $params['data']['portrait_img_storage_type']);
                $params['data']['landscape_img'] = $this->common->getImage($this->folder, $params['data']['landscape_img'], $params['data']['landscape_img_storage_type']);
                if ($params['data']['content_upload_type'] == 'server_audio') {
                    $params['data']['episode_audio'] = $this->common->getVideo($this->folder, $params['data']['episode_audio'], $params['data']['episode_storage_type']);
                }
                return view('admin.podcasts.ep_edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_update(Request $request)
    {
        try {
            $rules = [
                'podcasts_id' => 'required',
                'name' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'episode_upload_type' => 'required',
                'is_download' => 'required',
                'is_comment' => 'required',
                'is_like' => 'required',
            ];
            if ($request['episode_upload_type'] != 'server_audio') {
                $rules['url'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

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
            if ($requestData['episode_upload_type'] == 'server_audio') {
                if ($requestData['episode_upload_type'] == $requestData['old_episode_upload_type']) {
                    if ($requestData['music']) {

                        $requestData['episode_storage_type'] = $storage_type;
                        if ($storage_type == 1) {
                            $requestData['episode_audio'] = $requestData['music'];
                        } else {
                            $requestData['episode_audio'] = $this->common->saveImage($requestData['music'], $this->folder, 'ep_vid_', $storage_type);
                        }
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']), $request['old_episode_storage_type']);
                    }
                } else {

                    $requestData['episode_storage_type'] = $storage_type;
                    if ($requestData['music']) {

                        if ($storage_type == 1) {
                            $requestData['episode_audio'] = $requestData['music'];
                        } else {
                            $requestData['episode_audio'] = $this->common->saveImage($requestData['music'], $this->folder, 'ep_vid_', $storage_type);
                        }
                        $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']), $request['old_episode_storage_type']);
                    } else {
                        $requestData['episode_audio'] = '';
                    }
                }
            } else {

                $requestData['episode_storage_type'] = $storage_type;
                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_episode_audio']), $requestData['old_episode_storage_type']);

                $requestData['episode_audio'] = "";
                if ($requestData['url']) {
                    $requestData['episode_audio'] = $requestData['url'];
                }
            }
            unset($requestData['music'], $requestData['url'], $requestData['old_episode_upload_type'], $requestData['old_episode_audio'], $requestData['old_portrait_img'], $requestData['old_landscape_img'], $requestData['old_portrait_img_storage_type'], $requestData['old_landscape_img_storage_type'], $requestData['old_episode_storage_type']);
            $requestData['description'] = $requestData['description'] ?? "";

            $data = Episode::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_episode')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_episode')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_delete($podcasts_id, $id)
    {
        try {

            $data = Episode::where('id', $id)->first();
            if (isset($data)) {

                $this->common->deleteImageToFolder($this->folder, $data['portrait_img'], $data['portrait_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img'], $data['landscape_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['episode_audio'], $data['episode_storage_type']);
                $data->delete();

                // Content Releted Data Delete
                Comment::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
                Content_Report::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
                History::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
                Content_Like::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
                Content_View::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
                Watch_later::where('content_id', $podcasts_id)->where('episode_id', $id)->delete();
            }
            return redirect()->route('admin.podcast.episode.index', $podcasts_id)->with('success', __('label.episode_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ep_status($id)
    {
        try {

            $data = Episode::where('id', $id)->first();
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
    public function ep_sort_order(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Episode::where('id', $id_array[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.sort_order_saved')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
