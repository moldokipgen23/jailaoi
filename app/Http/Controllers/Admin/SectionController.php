<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Common;
use App\Models\Language;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

// Content Type : 1- Music, 2- Podcasts, 3- Radio, 4- Playlist, 5- Category, 6- Language, 7- Artist
class SectionController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['data'] = [];
            $params['language'] = Language::orderBy('sort_order', 'asc')->latest()->get();
            $params['category'] = Category::orderBy('sort_order', 'asc')->latest()->get();

            return view('admin.section.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'is_home_screen' => 'required',
                'title' => 'required|min:2',
                'content_type' => 'required',
                'screen_layout' => 'required',
            ];
            if ($request['content_type'] == 1 || $request['content_type'] == 2 || $request['content_type'] == 3 || $request['content_type'] == 4) {
                $rules['no_of_content'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['short_title'] = $request['short_title'] ?? '';
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $requestData['no_of_content'] = 0;
            $requestData['order_by_upload'] = 0;
            $requestData['order_by_view'] = 0;
            $requestData['order_by_like'] = 0;
            $requestData['view_all'] = 0;
            if ($requestData['content_type'] == 1 || $requestData['content_type'] == 2) {

                $requestData['category_id'] = $request['category_id'] ?? 0;
                $requestData['language_id'] = $request['language_id'] ?? 0;
                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['order_by_view'] = $request['order_by_view'] ?? 0;
                $requestData['order_by_like'] = $request['order_by_like'] ?? 0;
                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            } elseif ($requestData['content_type'] == 3) {

                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            } elseif ($requestData['content_type'] == 4) {

                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            }
            $requestData['sort_order'] = 0;
            $requestData['status'] = 1;
            $requestData['is_fixed'] = $request['is_fixed'] ?? 0;

            $data = Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_section')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_section')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function GetSectionData(Request $request)
    {
        try {
            if ($request['is_home_screen'] == 1) {
                $data = Section::where('is_home_screen', 1)->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
            } else if ($request['is_home_screen'] == 2) {

                if ($request['content_type'] == 1 || $request['content_type'] == 4) {
                    $data = Section::where('is_home_screen', 2)->whereIn('content_type', [1, 4])->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
                } else {
                    $data = Section::where('is_home_screen', 2)->where('content_type', $request['content_type'])->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
                }
            }
            return response()->json(['status' => 200, 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function SectionDataEdit(Request $request)
    {
        try {

            $data = Section::where('id', $request['id'])->first();
            return response()->json(['status' => 200, 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $rules = [
                'is_home_screen' => 'required',
                'title' => 'required|min:2',
                'content_type' => 'required',
                'screen_layout' => 'required',
            ];
            if ($request['content_type'] == 1 || $request['content_type'] == 2 || $request['content_type'] == 3 || $request['content_type'] == 4) {
                $rules['no_of_content'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['short_title'] = $request['short_title'] ?? '';
            $requestData['category_id'] = 0;
            $requestData['language_id'] = 0;
            $requestData['no_of_content'] = 0;
            $requestData['order_by_upload'] = 0;
            $requestData['order_by_view'] = 0;
            $requestData['order_by_like'] = 0;
            $requestData['view_all'] = 0;
            if ($requestData['content_type'] == 1 || $requestData['content_type'] == 2) {

                $requestData['category_id'] = $request['category_id'] ?? 0;
                $requestData['language_id'] = $request['language_id'] ?? 0;
                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['order_by_view'] = $request['order_by_view'] ?? 0;
                $requestData['order_by_like'] = $request['order_by_like'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            } elseif ($requestData['content_type'] == 3) {

                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            } elseif ($requestData['content_type'] == 4) {

                $requestData['no_of_content'] = $request['no_of_content'] ?? 0;
                $requestData['order_by_upload'] = $request['order_by_upload'] ?? 0;
                $requestData['view_all'] = $request['view_all'] ?? 0;
            }

            $section_data = Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($section_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_section')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_section')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            Section::where('id', $id)->delete();
            return response()->json(['status' => 200, 'success' => __('label.section_delete')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function SectionStatus($id)
    {
        try {

            $data = Section::where('id', $id)->first();
            if (isset($data)) {

                $data->status = $data->status === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data->status]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function SectionPin($id)
    {
        try {

            $data = Section::where('id', $id)->first();
            if (isset($data)) {

                $data->is_fixed = $data->is_fixed === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'is_fixed' => $data->is_fixed]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // Sort Order
    public function sort_order(Request $request)
    {
        try {
            if ($request['is_home_screen'] == 1) {
                $data = Section::select('id', 'title', 'is_fixed')->where('is_home_screen', 1)->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
            } else if ($request['is_home_screen'] == 2) {

                if ($request['content_type'] == 1 || $request['content_type'] == 4) {
                    $data = Section::select('id', 'title', 'is_fixed')->where('is_home_screen', 2)->whereIn('content_type', [1, 4])->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
                } else {
                    $data = Section::select('id', 'title', 'is_fixed')->where('is_home_screen', 2)->where('content_type', $request['content_type'])->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc')->latest()->get();
                }
            }
            return response()->json(['status' => 200, 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function sort_order_save(Request $request)
    {
        try {
            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Section::where('id', $id_array[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.sort_order_saved')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
