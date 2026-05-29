<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Common;
use App\Models\Rent_Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class RentSectionController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['category'] = Category::orderBy('sort_order', 'asc')->latest()->get();
            $params['data'] = Rent_Section::orderBy('sort_order', 'asc')->with('category')->latest()->get();

            return view('admin.rent_section.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'required',
                'no_of_content' => 'required|numeric|min:1',
                'view_all' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $requestData['sort_order'] = 0;
            $requestData['status'] = 1;

            $data = Rent_Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_section')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_section')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function section_edit(Request $request)
    {
        try {

            $data = Rent_Section::where('id', $request['id'])->first();
            return response()->json(['status' => 200, 'result' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'required',
                'no_of_content' => 'required|numeric|min:1',
                'view_all' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $data = Rent_Section::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
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

            Rent_Section::where('id', $id)->delete();
            return response()->json(['status' => 200, 'success' => __('label.section_delete')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function section_sort_order(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Rent_Section::where('id', $id_array[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.sort_order_saved')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
