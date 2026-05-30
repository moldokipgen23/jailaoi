<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Content;
use App\Models\Content_View;
use App\Models\Playlist_Content;
use App\Models\User;
use App\Models\Watch_later;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PlaylistController extends Controller
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
            $params['channel'] = User::where('role', 'artist')->latest()->get();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_channel = $request['input_channel'];
                $input_type = $request['input_type'];

                $query = Content::where('content_type', 5);
                if ($input_search) {
                    $query->where('title', 'LIKE', "%{$input_search}%");
                }
                if ($input_channel != 0) {
                    $query->where('channel_id', $input_channel);
                }
                if ($input_type != 0) {
                    $query->where('playlist_type', $input_type);
                }
                $data = $query->with('channel')->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $playlist_delete = __('label.delete_playlist');

                        $delete = '<form onsubmit="return confirm(\'' . $playlist_delete . '\');" method="POST" action="' . route('admin.playlist.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a class="edit-delete-btn mr-2 edit_playlist" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-channel_id="' . $row->channel_id . '" data-title="' . $row->title . '" data-description="' . $row->description . '" data-playlist_type="' . $row->playlist_type . '">';
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
                    ->addColumn('content', function ($row) {
                        $Label = __('label.content_list');
                        $btn = "<a href='" . route('admin.playlist.content.index', $row->id) . "' class='info-btn p-2'>$Label</a>";
                        return $btn;
                    })
                    ->rawColumns(['action', 'content', 'status'])
                    ->make(true);
            }
            return view('admin.playlist.index', $params);
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
                'playlist_type' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['content_type'] = 5;
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $requestData['hashtag_id'] = 0;
            $requestData['description'] = $requestData['description'] ?? '';
            $requestData['portrait_img'] = '';
            $requestData['landscape_img'] = '';
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
            $requestData['total_watch_time'] = 0;
            $requestData['status'] = 1;

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_playlist')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_playlist')]);
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
                'playlist_type' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['description'] = $requestData['description'] ?? '';

            $data = Content::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_playlist')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_playlist')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            Content::where('id', $id)->delete();
            Playlist_Content::where('playlist_id', $id)->delete();

            // Content Releted Data Delete
            Content_View::where('content_id', $id)->delete();
            Watch_later::where('content_id', $id)->delete();

            return redirect()->route('admin.playlist.index')->with('success', __('label.playlist_delete'));
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

    // Content
    public function pl_index($id, Request $request)
    {
        try {

            $params['data'] = [];
            $params['playlist_id'] = $id;

            $params['data'] = Playlist_Content::where('playlist_id', $id)
                ->with(['content' => function ($query) {
                    $query->select('id', 'title', 'portrait_img_storage_type', 'portrait_img', 'channel_id');
                }])
                ->orderBy('sort_order', 'asc')->latest()->get();

            for ($i = 0; $i < count($params['data']); $i++) {
                if ($params['data'][$i]['content'] != null) {
                    $params['data'][$i]['content']['portrait_img'] = $this->common->getImage($this->folder, $params['data'][$i]['content']['portrait_img'], $params['data'][$i]['content']['portrait_img_storage_type']);
                }
            }

            $check = Content::select('id', 'title')->where('id', $id)->first();
            $params['playlist_name'] = $check['title'] ?? "";

            return view('admin.playlist.ct_index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function pl_content_data(Request $request)
    {
        try {
            $content_type = $request['content_type'];
            $playlist_id = $request['playlist_id'];

            $ids_array = Playlist_Content::select('content_id')->where('playlist_id', $playlist_id)->where('content_type', $content_type)->get()->toArray();
            $data = Content::select('id', 'title')->whereNotIn('id', $ids_array)->where('content_type', $content_type)->where('status', 1)->where('is_rent', 0)->latest()->get();

            if (isset($data) && $data != null) {
                return response()->json(['status' => 200, 'data' => $data]);
            } else {
                return response()->json(['status' => 400, 'data' => []]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function pl_save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content_type' => 'required',
                'content' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $content = Content::select('channel_id')->where('id', $requestData['playlist_id'])->first();
            for ($i = 0; $i < count($requestData['content']); $i++) {

                $insert = new Playlist_Content();
                $insert['channel_id'] = $content['channel_id'];
                $insert['playlist_id'] = $requestData['playlist_id'];
                $insert['content_type'] = $requestData['content_type'];
                $insert['content_id'] = $requestData['content'][$i];
                $insert['sort_order'] = 0;
                $insert['status'] = 1;
                $insert->save();
            }
            return response()->json(['status' => 200, 'success' => __('label.success_add_content')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function pl_delete(Request $request)
    {
        try {

            Playlist_Content::where('id', $request['id'])->delete();
            return response()->json(['status' => 200, 'success' => __('label.content_removed'), 'id' => $request['id']]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function pl_sort_order(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                for ($i = 0; $i < count($ids); $i++) {
                    Playlist_Content::where('id', $ids[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.sort_order_saved')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
