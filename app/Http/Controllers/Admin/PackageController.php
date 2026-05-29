<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Package;
use App\Models\Package_Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PackageController extends Controller
{
    private $folder = "package";
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

                $input_search = $request['input_search'];
                $query = Package::query();
                if ($input_search != null) {
                    $query->where('name', 'LIKE', "%{$input_search}%");
                }
                $data = $query->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $package_delete = __('label.delete_package');

                        $delete = '<form onsubmit="return confirm(\'' . $package_delete . '\');" method="POST" action="' . route('admin.package.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a href="' . route('admin.package.edit', [$row->id]) . '" class="edit-delete-btn mr-2">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</div>';
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
            return view('admin.package.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {
            $params['data'] = [];
            return view('admin.package.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required|numeric',
                'type' => 'required',
                'time' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'ads_free' => 'required',
                'download_content' => 'required',
                'background_play' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $requestData['storage_type'] = Storage_Type();
            $file = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'pack_', $requestData['storage_type']);
            $requestData['android_product_package'] = $request['android_product_package'] ?? "";
            $requestData['ios_product_package'] = $request['ios_product_package'] ?? "";
            $requestData['web_product_package'] = $request['web_product_package'] ?? "";
            $requestData['status'] = 1;

            $data = Package::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {

                // Package Details
                Package_Detail::where('package_id', $data['id'])->delete();
                Package_Detail::insert([
                    ['package_id' => $data['id'], 'package_key' => 'Ad-free Content', 'package_value' => $data['ads_free']],
                    ['package_id' => $data['id'], 'package_key' => 'Download Content (Offline View)', 'package_value' => $data['download_content']],
                    ['package_id' => $data['id'], 'package_key' => 'Background Content Play', 'package_value' => $data['background_play']],
                ]);

                return response()->json(['status' => 200, 'success' => __('label.success_add_package')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_package')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = Package::where('id', $id)->first();
            if ($params['data'] != null) {

                $this->common->imageNameToUrl(array($params['data']), 'image', $this->folder);

                return view('admin.package.edit', $params);
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
                'name' => 'required',
                'price' => 'required|numeric',
                'type' => 'required',
                'time' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:5120',
                'ads_free' => 'required',
                'download_content' => 'required',
                'background_play' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            if (isset($requestData['image'])) {

                $requestData['storage_type'] = Storage_Type();
                $file = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($file, $this->folder, 'pack_', $requestData['storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']), $request['old_storage_type']);
            }
            unset($requestData['old_image'], $requestData['old_storage_type']);

            $requestData['android_product_package'] = $request['android_product_package'] ?? "";
            $requestData['ios_product_package'] = $request['ios_product_package'] ?? "";
            $requestData['web_product_package'] = $request['web_product_package'] ?? "";

            $data = Package::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {

                // Package Details
                Package_Detail::where('package_id', $data['id'])->delete();
                Package_Detail::insert([
                    ['package_id' => $data['id'], 'package_key' => 'Ad-free Content', 'package_value' => $data['ads_free']],
                    ['package_id' => $data['id'], 'package_key' => 'Download Content (Offline View)', 'package_value' => $data['download_content']],
                    ['package_id' => $data['id'], 'package_key' => 'Background Content Play', 'package_value' => $data['background_play']],
                ]);

                return response()->json(['status' => 200, 'success' => __('label.success_edit_package')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_package')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Package::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['storage_type']);
                $data->delete();

                Package_Detail::where('package_id', $data['id'])->delete();
            }
            return redirect()->route('admin.package.index')->with('success', __('label.package_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Package::where('id', $id)->first();
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
