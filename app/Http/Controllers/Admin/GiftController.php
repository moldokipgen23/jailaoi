<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Common;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class GiftController extends Controller
{
    private $folder = "gift";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            if ($request->ajax()) {

                $query = Gift::query();

                $input_search = $request['input_search'];
                if ($input_search != null) {
                    $query->where('name', 'LIKE', "%{$input_search}%");
                }
                $data = $query->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $gift_delete = __('label.delete_gift');

                        $delete = '<form onsubmit="return confirm(\'' . $gift_delete . '\');" method="POST" action="' . route('admin.gift.destroy', [$row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a class="edit-delete-btn mr-2 edit_gift" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-name="' . $row->name . '" data-price="' . $row->price . '" data-image="' . $row->image . '" data-storage_type="' . $row->storage_type . '">';
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
            return view('admin.gift.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'price' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $requestData['storage_type'] = Storage_Type();
            $files = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($files, $this->folder, 'gift_', $requestData['storage_type']);
            $requestData['status'] = 1;

            $data = Gift::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_gift')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_gift')]);
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
                'price' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            if (isset($requestData['image'])) {

                $requestData['storage_type'] = Storage_Type();
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, 'gift_', $requestData['storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']), $requestData['old_storage_type']);
            }
            unset($requestData['old_image'], $requestData['old_storage_type']);

            $data = Gift::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_gift')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_gift')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Gift::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['storage_type']);
                $data->delete();
            }
            return redirect()->route('admin.gift.index')->with('success', __('label.gift_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Gift::where('id', $id)->first();
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
}
