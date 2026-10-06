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
    private $folder = "images/user";
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

                $query = User::query();

                if (!empty($input_search)) {
                    // Search across name, email AND mobile — previously only
                    // name was searched, so looking up a user by email always
                    // returned "no data" even when the account existed.
                    $query->where(function ($q) use ($input_search) {
                        $q->where('full_name', 'LIKE', "%{$input_search}%")
                            ->orWhere('email', 'LIKE', "%{$input_search}%")
                            ->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                    });
                }

                if ($input_login_type != 'all') {
                    $query->where('type', $input_login_type);
                }

                if ($input_type == 'today') {
                    $query->whereDate('created_at', date('Y_m-d'));
                } elseif ($input_type == 'month') {
                    $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'));
                } elseif ($input_type == 'year') {
                    $query->whereYear('created_at', date('Y'));
                }

                $data = $query->latest()->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_user') . '\');" method="POST"  action="' . route('user.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn"  title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-center" >';
                        $btn .= '<button type="button" class="edit-delete-btn mr-4 send-reset-btn" data-id="' . $row->id . '" title="Send password reset"><i class="fa-solid fa-key fa-xl"></i></button>';
                        $btn .= '<a href="' . route('user.edit', [$row->id]) . '" class="edit-delete-btn mr-4" title=' . __('label.edit') . '>';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("d M Y", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.user.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            return view('admin.user.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
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
                'gender' => 'required',
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

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "user_");
            }

            $user_data = User::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($user_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_user')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.user_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
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
                return redirect()->back()->with('error', __('label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {

            // Only EMAIL (and password, handled below) are required to update — so an
            // admin can recover an artist's lost email/password without being forced
            // to re-fill mobile, country, gender, etc. Other fields are optional and,
            // when left blank, keep their existing values (see strip-blanks below).
            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable|min:2',
                'mobile_number' => [
                    'nullable',
                    'numeric',
                    Rule::unique('tbl_user')->where(function ($query) use ($request,) {
                        return $query->where('country_code', $request->country_code)
                            ->where('mobile_number', $request->mobile_number)
                            ->where('id', '!=', $request->id);
                    }),
                ],
                'country_code' => 'nullable',
                'country_name' => 'nullable',
                'email' => 'required|email|unique:tbl_user,email,' . $id,
                'gender' => 'nullable',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            // Don't overwrite existing values with blanks when the admin only
            // changes the email/password.
            foreach (['full_name', 'mobile_number', 'country_code', 'country_name'] as $optKey) {
                if (!isset($requestData[$optKey]) || $requestData[$optKey] === '' || $requestData[$optKey] === null) {
                    unset($requestData[$optKey]);
                }
            }

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, "user_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }

            if (isset($requestData['password']) && !empty($requestData['password'])) {
                $requestData['password'] = Hash::make($requestData['password']);
            } else {
                unset($requestData['password']);
            }
            unset($requestData['old_image']);

            // Keep only real tbl_user columns so stray form fields (gender, _token,
            // _method, ...) that have no DB column can't break the update.
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('tbl_user');
            $requestData = array_intersect_key($requestData, array_flip($columns));

            $User_data = User::updateOrCreate(['id' => $id], $requestData);
            if (isset($User_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_user')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.user_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // Admin-triggered password reset: emails the user a secure reset link so
    // they can set their own password (works for regular users and artists,
    // since an artist is just a user). Same token flow as the public forgot-
    // password page.
    public function sendResetLink($id)
    {
        try {
            $user = User::find($id);
            if (!$user || empty($user->email)) {
                return response()->json(['status' => 400, 'errors' => 'User or email not found']);
            }

            $token = \Illuminate\Support\Str::random(64);
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $user->email)->delete();
            \Illuminate\Support\Facades\DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => \Carbon\Carbon::now(),
            ]);

            $resetUrl = url('/user/password/reset?' . http_build_query([
                'token' => $token,
                'email' => $user->email,
            ]));

            try {
                $this->common->SetSmtpConfig();
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\ForgotPasswordMail($user->full_name ?: 'there', $resetUrl)
                );
            } catch (\Exception $e) {
                return response()->json(['status' => 400, 'errors' => 'Could not send email: ' . $e->getMessage()]);
            }

            return response()->json(['status' => 200, 'success' => 'Password reset link sent to ' . $user->email]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
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
            return redirect()->route('user.index')->with('success', __('label.success_delete_user'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
