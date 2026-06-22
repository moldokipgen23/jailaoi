<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Category;
use App\Models\City;
use App\Models\Language;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{

    public function index()
    {
        try {

            $params['artist'] = Artist::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['category'] = Category::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['language'] = Language::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['city'] = City::orderBy('sort_order', 'asc')->where('status', 1)->get();
            $params['users'] = User::where('status', 1)->get();

            return view('admin.section.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if ($request['type'] == 1) {
                $validator1 = Validator::make($request->all(), [
                    'screen_layout' => 'required',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator1->fails()) {
                    $errs = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }

            if ($request['type'] == 2) {
                $validator2 = Validator::make($request->all(), [
                    'artist_id' => 'required',
                    'screen_layout' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator2->fails()) {
                    $errs = $validator2->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }

            if ($request['type'] == 3 || $request['type'] == 4 || $request['type'] == 5 || $request['type'] == 6 || $request['type'] == 7) {
                $rules = [
                    'screen_layout' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                ];

                if ($request['type'] == 3) {
                    $rules['is_paid'] = 'required';
                } else {
                    $rules['order_by_upload'] = 'required';
                }

                $validator3 = Validator::make($request->all(), $rules);

                if ($validator3->fails()) {
                    $errs = $validator3->errors()->all();
                    return response()->json(['status' => 400, 'errors' => $errs]);
                }
            }
            if ($request['type'] == 8) {
                $validator2 = Validator::make($request->all(), [
                    'screen_layout' => 'required',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator2->fails()) {
                    $errs = $validator2->errors()->all();
                    return response()->json(['status' => 400, 'errors' => $errs]);
                }
            }

            $requestData = $request->all();
            $requestData['sortable'] = 1;
            $requestData['status'] = 1;
            $requestData['section_type'] = $request->section_type;
            $requestData['artist_id'] = 0;
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $requestData['city_id'] = 0;
            $requestData['sub_title'] = $request['sub_title'] != null  ? $request['sub_title'] : "";
            $requestData['no_of_content'] = 1;
            $requestData['view_all'] = 0;
            $requestData['order_by_upload'] = 1;
            $requestData['order_by_play'] = 1;
            $requestData['time_window_days'] = isset($request['time_window_days']) ? (int) $request['time_window_days'] : 0;
            $requestData['is_paid'] = 0;
            $requestData['is_premium'] = 0;
            $requestData['user_id'] = isset($request['user_id'])  ? $request['user_id'] : 0;

            if ($requestData['type'] == 1) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['city_id'] = isset($request['city_id'])  ? $request['city_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            } else if ($requestData['type'] == 2) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            } elseif ($requestData['type'] == 3 || $requestData['type'] == 4 || $requestData['type'] == 5 || $requestData['type'] == 6 || $requestData['type'] == 7) {

                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;

                if ($requestData['type'] == 3) {
                    $requestData['is_paid'] = isset($request['is_paid']) ? $request['is_paid'] : 0;
                } else {
                    $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                }
            } else if ($requestData['type'] == 8) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            }

            $section_data = Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($section_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_section')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.section_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function GetSectionData(Request $request)
    {
        try {

            $data = Section::where('section_type', $request->section_type)->where('user_id', $request->user_id)->orderBy('sortable', 'asc')->get();
            return response()->json(['status' => 200, 'success' => __('label.data_get_successfully'), 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function SectionDataEdit(Request $request)
    {
        try {

            $data = Section::where('id', $request['id'])->first();
            return response()->json(['status' => 200, 'success' => __('label.data_get_successfully'), 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {

            Section::where('id', $id)->delete();
            return response()->json(['status' => 200, 'success' => __('label.success_delete_section')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            if ($request['type'] == 1) {
                $validator1 = Validator::make($request->all(), [
                    'screen_layout' => 'required',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'city_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator1->fails()) {
                    $errs = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }
            if ($request['type'] == 2) {
                $validator2 = Validator::make($request->all(), [
                    'artist_id' => 'required',
                    'screen_layout' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator2->fails()) {
                    $errs = $validator2->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }

            if ($request['type'] == 3 || $request['type'] == 4 || $request['type'] == 5 || $request['type'] == 6 || $request['type'] == 7) {
                $rules = [
                    'screen_layout' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                ];

                if ($request['type'] == 3) {
                    $rules['is_paid'] = 'required';
                } else {
                    $rules['order_by_upload'] = 'required';
                }

                $validator3 = Validator::make($request->all(), $rules);

                if ($validator3->fails()) {
                    $errs = $validator3->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }

            if ($request['type'] == 8) {
                $validator2 = Validator::make($request->all(), [
                    'screen_layout' => 'required',
                    'artist_id' => 'required',
                    'category_id' => 'required',
                    'language_id' => 'required',
                    'no_of_content' => 'required|integer|gt:0',
                    'view_all' => 'required',
                    'is_premium' => 'required',
                    'order_by_upload' => 'required',
                    'order_by_play' => 'required',
                ]);
                if ($validator2->fails()) {
                    $errs = $validator2->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs));
                }
            }

            $requestData = $request->all();
            $requestData['sortable'] = 1;
            $requestData['status'] = 1;
            $requestData['artist_id'] = 0;
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $requestData['city_id'] = 0;
            $requestData['sub_title'] = $request['sub_title'] != null  ? $request['sub_title'] : "";
            $requestData['no_of_content'] = 1;
            $requestData['view_all'] = 0;
            $requestData['order_by_upload'] = 1;
            $requestData['order_by_play'] = 1;
            $requestData['time_window_days'] = isset($request['time_window_days']) ? (int) $request['time_window_days'] : 0;
            $requestData['is_paid'] = 0;
            $requestData['is_premium'] = 0;
            $requestData['user_id'] = isset($request['user_id'])  ? $request['user_id'] : 0;

            if ($requestData['type'] == 1) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['city_id'] = isset($request['city_id'])  ? $request['city_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            } else if ($requestData['type'] == 2) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            } elseif ($requestData['type'] == 3 || $requestData['type'] == 4 || $requestData['type'] == 5 || $requestData['type'] == 6 || $requestData['type'] == 7) {

                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;

                if ($requestData['type'] == 3) {
                    $requestData['is_paid'] = isset($request['is_paid']) ? $request['is_paid'] : 0;
                } else {
                    $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                }
            } else if ($requestData['type'] == 8) {

                $requestData['artist_id'] = isset($request['artist_id'])  ? $request['artist_id'] : 0;
                $requestData['category_id'] = isset($request['category_id'])  ? $request['category_id'] : 0;
                $requestData['language_id'] = isset($request['language_id'])  ? $request['language_id'] : 0;
                $requestData['no_of_content'] = isset($request['no_of_content'])  ? $request['no_of_content'] : 1;
                $requestData['view_all'] = isset($request['view_all'])  ? $request['view_all'] : 0;
                $requestData['is_premium'] = isset($request['is_premium'])  ? $request['is_premium'] : 0;
                $requestData['order_by_upload'] = isset($request['order_by_upload']) ? $request['order_by_upload'] : 1;
                $requestData['order_by_play'] = isset($request['order_by_play']) ? $request['order_by_play'] : 1;
                $requestData['is_title'] = isset($request['is_title']) ? $request['is_title'] : 1;
                $requestData['is_category'] = isset($request['is_category']) ? $request['is_category'] : 1;
                $requestData['is_artist_name'] = isset($request['is_artist_name']) ? $request['is_artist_name'] : 1;
            }

            $section_data = Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($section_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_section')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.section_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {

            $data = Section::where('id', $request->id)->first();
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

    public function togglePin(Request $request)
    {
        try {
            $data = Section::where('id', $request->id)->first();
            if ($data) {
                $data->is_pinned = $data->is_pinned == 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'is_pinned' => $data->is_pinned]);
            }
            return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function SectionSortable(Request $request)
    {
        try {
            $data = Section::select('id', 'title')->where('section_type', $request->section_type)->where('user_id', $request->user_id)->orderBy('sortable', 'asc')->get();
            return response()->json(['status' => 200, 'success' => __('label.data_get_successfully'), 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function SectionSortableSave(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Section::where('id', $id_array[$i])->update(['sortable' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_edit_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
