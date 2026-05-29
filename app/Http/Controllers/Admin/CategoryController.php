<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Common;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $folder = "category";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = Category::orderby('sort_order', 'asc')->latest()->get();
            if ($request->ajax()) {

                $query = Category::query();

                $input_search = $request['input_search'];
                if ($input_search != null) {
                    $query->where('name', 'LIKE', "%{$input_search}%");
                }
                $data = $query->orderby('sort_order', 'asc')->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $category_delete = __('label.delete_category');

                        $delete = '<form onsubmit="return confirm(\'' . $category_delete . '\');" method="POST" action="' . route('admin.category.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a class="edit-delete-btn mr-2 edit_category" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-name="' . $row->name . '" data-image="' . $row->image . '" data-storage_type="' . $row->storage_type . '">';
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
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.category.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $requestData['storage_type'] = Storage_Type();
            $file = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'cat_', $requestData['storage_type']);
            $requestData['sort_order'] = 0;
            $requestData['status'] = 1;

            $data = Category::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_category')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_category')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'image' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            if (isset($requestData['image'])) {
                $file = $requestData['image'];
                $requestData['storage_type'] = Storage_Type();
                $requestData['image'] = $this->common->saveImage($file, $this->folder, 'cat_', $requestData['storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']), $request['old_storage_type']);
            }
            unset($requestData['old_image'], $requestData['old_storage_type']);

            $data = Category::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_category')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_category')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Category::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['storage_type']);
                $data->delete();
            }
            return redirect()->route('admin.category.index')->with('success', __('label.category_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Category::where('id', $id)->first();
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
    public function sort_order_save(Request $request)
    {
        try {

            $ids = $request['ids'];
            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Category::where('id', $id_array[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.sort_order_saved')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
