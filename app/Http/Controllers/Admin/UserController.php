<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Login Type : 1- OTP, 2- Goggle, 3- Apple, 4- Normal
class UserController extends Controller
{
    private $folder = "user";
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
                $input_login_type = $request['input_login_type'];

                if ($input_search != null && isset($input_search)) {

                    if ($input_login_type == "all") {

                        if ($input_type == "today") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->whereYear('created_at', date('Y'))->latest()->get();
                        } else {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->latest()->get();
                        }
                    } else {

                        if ($input_type == "today") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)->whereYear('created_at', date('Y'))->latest()->get();
                        } else {

                            $data = User::where(function ($query) use ($input_search) {
                                $query->where('full_name', 'LIKE', "%{$input_search}%")->orWhere('email', 'LIKE', "%{$input_search}%")->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                                ->where('type', $input_login_type)->latest()->get();
                        }
                    }
                } else {

                    if ($input_login_type == "all") {

                        if ($input_type == "today") {
                            $data = User::whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {
                            $data = User::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {
                            $data = User::whereYear('created_at', date('Y'))->latest()->get();
                        } else {
                            $data = User::latest()->get();
                        }
                    } else {

                        if ($input_type == "today") {
                            $data = User::where('type', $input_login_type)->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "month") {
                            $data = User::where('type', $input_login_type)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                        } else if ($input_type == "year") {
                            $data = User::where('type', $input_login_type)->whereYear('created_at', date('Y'))->latest()->get();
                        } else {
                            $data = User::where('type', $input_login_type)->latest()->get();
                        }
                    }
                }

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('role_badge', function ($row) {
                        if ($row->role == 'artist') {
                            return '<span class="badge badge-success">Artist</span>';
                        } elseif ($row->role == 'admin') {
                            return '<span class="badge badge-primary">Admin</span>';
                        }
                        return '<span class="badge badge-secondary">User</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'Are you sure !!! You want to Delete this User ?\');" method="POST"  action="' . route('user.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn" style="outline: none;" title="Delete"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around" title="Edit">';
                        $btn .= '<a href="' . route('user.edit', [$row->id]) . '" class="edit-delete-btn">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action', 'role_badge'])
                    ->make(true);
            }
            return view('admin.user.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            return view('admin.user.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|min:2',
                'mobile_number' => [
                    'required',
                    'numeric',
                    Rule::unique('tbl_user')->where(function ($query) use ($request,) {
                        return $query->where('country_code', $request->country_code)
                            ->where('mobile_number', $request->mobile_number);
                    }),
                ],
                'country_code' => 'required',
                'country_name' => 'required',
                'email' => 'required|unique:tbl_user|email',
                'password' => 'required|min:4',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            $email_array = explode('@', $request->email);
            $requestData['user_name'] =  $this->common->user_name($email_array[0]);
            $requestData['password'] = Hash::make($request->password);
            $requestData['type'] = 4;
            $requestData['device_type'] = 0;
            $requestData['device_token'] = "";
            $requestData['role'] = $requestData['role'] ?? 'user';

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "user_");
            }

            $user_data = User::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($user_data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = User::where('id', $id)->first();

            $this->common->imageNameToUrl(array($params['data']), 'image', $this->folder);

            if ($params['data'] != null) {
                return view('admin.user.edit', $params);
            } else {
                return redirect()->back()->with('error', __('Label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|min:2',
                'mobile_number' => [
                    'required',
                    'numeric',
                    Rule::unique('tbl_user')->where(function ($query) use ($request,) {
                        return $query->where('country_code', $request->country_code)
                            ->where('mobile_number', $request->mobile_number)
                            ->where('id', '!=', $request->id);
                    }),
                ],
                'country_code' => 'required',
                'country_name' => 'required',
                'email' => 'required|email|unique:tbl_user,email,' . $id,
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "user_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }
            unset($requestData['old_image']);

            $User_data = User::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($User_data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_edit_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_updated')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function destroy($id)
    {
        try {

            $data = User::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $data->delete();
            }
            return redirect()->route('user.index')->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
