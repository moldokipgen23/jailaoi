<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Podcast_Section;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class PodcastSectionController extends Controller
{

    public function index()
    {
        try {

            $params['category'] = Category::latest()->get();
            $params['language'] = Language::latest()->get();

            return view('admin.podcast_section.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'category_id' => 'required',
                'language_id' => 'required',
                'screen_layout' => 'required',
                'no_of_content' => 'required|integer|gt:0',
                'view_all' => 'required',
                'is_premium' => 'required',
                'order_by_upload' => 'required',
                'order_by_play' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            $requestData['sortable'] = 1;
            $requestData['status'] = 1;

            $requestData['sub_title'] = $request['sub_title'] != null  ? $request['sub_title'] : "";
            $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
            $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
            $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
            $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
            $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
            $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
            $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;

            $section_data = Podcast_Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($section_data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function GetSectionData(Request $request)
    {
        try {

            $data = Podcast_Section::orderBy('sortable', 'asc')->get();
            return response()->json(array('status' => 200, 'success' => __('Label.data_get_successfully'), 'result' => $data));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function SectionDataEdit(Request $request)
    {
        try {

            $data = Podcast_Section::where('id', $request['id'])->first();
            return response()->json(array('status' => 200, 'success' => __('Label.data_get_successfully'), 'result' => $data));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function show(string $id)
    {
        try {

            Podcast_Section::where('id', $id)->delete();
            return response()->json(array('status' => 200, 'success' => __('Label.data_delete_successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'category_id' => 'required',
                'language_id' => 'required',
                'screen_layout' => 'required',
                'no_of_content' => 'required|integer|gt:0',
                'view_all' => 'required',
                'is_premium' => 'required',
                'order_by_upload' => 'required',
                'order_by_play' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            $requestData['sortable'] = 1;
            $requestData['status'] = 1;

            $requestData['sub_title'] = $request['sub_title'] != null  ? $request['sub_title'] : "";
            $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
            $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
            $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
            $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
            $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
            $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
            $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;

            $section_data = Podcast_Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($section_data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function changeStatus(Request $request)
    {
        try {

            $data = Podcast_Section::where('id', $request->id)->first();
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


    public function SectionSortable(Request $request)
    {
        try {

            $data = Podcast_Section::select('id', 'title')->orderBy('sortable', 'asc')->get();
            return response()->json(array('status' => 200, 'success' => __('Label.data_get_successfully'), 'result' => $data));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function SectionSortableSave(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Podcast_Section::where('id', $id_array[$i])->update(['sortable' => $i + 1]);
                }
            }
            return response()->json(array('status' => 200, 'success' => __('Label.data_edit_successfully')));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
