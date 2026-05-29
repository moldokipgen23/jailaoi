<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badges_Bonus;
use App\Models\Common;
use App\Models\User_Badges_Bonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class BadgesBonusController extends Controller
{
    private $folder = "badges_bonus";
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
                $input_type = $request['input_type'];
                $input_condition_type = $request['input_condition_type'];

                $query = Badges_Bonus::query();
                if ($input_search != null) {
                    $query->where('name', 'LIKE', "%{$input_search}%");
                }
                if ($input_type !== 'all') {
                    $query->where('type', $input_type);
                }
                if ($input_condition_type !== 'all') {
                    $query->where('condition_type', $input_condition_type);
                }
                $data = $query->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $badges_bonus_delete = __('label.delete_badges_bonus');

                        $delete = '<form onsubmit="return confirm(\'' . $badges_bonus_delete . '\');" method="POST" action="' . route('admin.badgesbonus.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a href="' . route('admin.badgesbonus.edit', [$row->id]) . '" class="edit-delete-btn mr-2">';
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
            return view('admin.badges_bonus.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {
            $params['data'] = [];
            return view('admin.badges_bonus.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'type' => 'required',
                'condition_type' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ];
            if ($request['type'] != null && $request['type'] == 0 || $request['type'] == 2) {
                $rules['bonus_coin'] = 'required|numeric|min:1';
            }
            if ($request['condition_type'] == "content_views" || $request['condition_type'] == "content_likes") {
                $rules['x_number'] = 'required|numeric|min:1';
                $rules['x_content'] = 'required|numeric|min:1';
            } else {
                $rules['x_number'] = 'required|numeric|min:1';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            $requestData['storage_type'] = Storage_Type();
            $file = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'badge_', $requestData['storage_type']);
            $requestData['bonus_coin'] = $request['bonus_coin'] ?? 0;
            $requestData['x_number'] = $request['x_number'] ?? 0;
            $requestData['x_content'] = $request['x_content'] ?? 0;
            $requestData['status'] = 1;

            $data = Badges_Bonus::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_badges_bonus')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_badges_bonus')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = Badges_Bonus::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['data']['image'] = $this->common->getImage($this->folder, $params['data']['image'], $params['data']['storage_type']);
                return view('admin.badges_bonus.edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'type' => 'required',
                'condition_type' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:5120',
            ];
            if ($request['type'] != null && $request['type'] == 0 || $request['type'] == 2) {
                $rules['bonus_coin'] = 'required|numeric|min:1';
            }
            if ($request['condition_type'] == "content_views" || $request['condition_type'] == "content_likes") {
                $rules['x_number'] = 'required|numeric|min:1';
                $rules['x_content'] = 'required|numeric|min:1';
            } else {
                $rules['x_number'] = 'required|numeric|min:1';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            if (isset($request['image'])) {
                $file = $request['image'];
                $requestData['storage_type'] = Storage_Type();
                $requestData['image'] = $this->common->saveImage($file, $this->folder, 'badge_', $requestData['storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']), $requestData['old_storage_type']);
            }
            $requestData['bonus_coin'] = $request['bonus_coin'] ?? 0;
            $requestData['x_number'] = $request['x_number'] ?? 0;
            $requestData['x_content'] = $request['x_content'] ?? 0;

            unset($requestData['old_image'], $requestData['old_storage_type']);

            $data = Badges_Bonus::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_badges_bonus')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_badges_bonus')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Badges_Bonus::where('id', $id)->first();
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
    public function destroy($id)
    {
        try {

            $data = Badges_Bonus::where('id', $id)->first();
            if (isset($data)) {

                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['storage_type']);
                $data->delete();

                User_Badges_Bonus::where('badges_bonus_id', $id)->delete();
            }
            return redirect()->route('admin.badgesbonus.index')->with('success', __('label.badges_bonus_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
