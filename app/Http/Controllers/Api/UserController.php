<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Common;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Login Type : 1- OTP, 2- Goggle, 3- Apple, 4- Normal
class UserController extends Controller
{
    private $folder_user = "user";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function register(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'full_name' => 'required|min:2',
                    'email' => 'required|unique:tbl_user|email',
                    'country_code' => 'required',
                    'mobile_number' => [
                        'required',
                        'numeric',
                        Rule::unique('tbl_user')->where(function ($query) use ($request,) {
                            return $query->where('country_code', $request->country_code)
                                ->where('mobile_number', $request->mobile_number);
                        }),
                    ],
                    'country_name' => 'required',
                    'password' => 'required|min:4',
                ],
                [
                    'full_name.required' => __('api_msg.full_name_is_required'),
                    'email.required' => __('api_msg.email_is_required'),
                    'country_code.required' => __('api_msg.country_code_is_required'),
                    'mobile_number.required' => __('api_msg.mobile_number_is_required'),
                    'country_name.required' => __('api_msg.country_name_is_required'),
                    'password.required' => __('api_msg.password_is_required'),
                ]
            );
            if ($validation->fails()) {

                $errors = $validation->errors()->first();
                $data['status'] = 400;
                if ($errors) {
                    $data['message'] = $errors;
                }
                return $data;
            }

            $data = User::where('email', $request->email)->first();
            if (isset($data)) {
                return $this->common->API_Response(400, __('api_msg.email_name_already_exists'));
            } else {

                $email = $request->email;
                $data['type'] = 4;
                $data['full_name'] = $request->full_name;
                $data['country_code'] = $request->country_code;
                $data['mobile_number'] = $request->mobile_number;
                $data['country_name'] = $request->country_name;
                $data['email'] = $email;
                $data['password'] = Hash::make($request->password);
                $data['image'] = "";
                $data['device_type'] = isset($request->device_type) ? $request->device_type : 0;
                $data['device_token'] = isset($request->device_token) ? $request->device_token : "";
                $data['status'] = 1;
                // create username 
                $name = explode("@", $email);
                $data['user_name'] = $this->common->user_name($name[0]);

                $user_id = User::insertGetId($data);

                if (isset($user_id)) {
                    $user = User::where('id', $user_id)->first();
                    if (isset($user)) {
                        // Send Mail (Type = 1- Register Mail, 2 Transaction Mail, 3 Login Mail)
                        $this->common->Send_Mail(1, $user->email);
                        $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);

                        return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function login(Request $request)
    {
        try {

            if ($request->type == 1) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'country_code' => 'required',
                        'mobile_number' => 'required|numeric',
                        'country_name' => 'required',
                    ],
                    [
                        'country_code.required' => __('api_msg.country_code_is_required'),
                        'mobile_number.required' => __('api_msg.mobile_number_is_required'),
                        'country_name.required' => __('api_msg.country_name_is_required'),
                    ]
                );
            } elseif ($request->type == 2 || $request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required',
                    ],
                    [
                        'email.required' => __('api_msg.email_is_required'),
                    ]
                );
            } elseif ($request->type == 4) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required|email',
                        'password' => 'required|min:4',
                    ],
                    [
                        'email.required' => __('api_msg.email_is_required'),
                        'password.required' => __('api_msg.password_is_required'),
                    ]
                );
            } else {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                    [
                        'type.required' => __('api_msg.type_is_required'),
                    ]
                );
            }
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $type = isset($request->type) ? $request->type : 0;
            $email = isset($request->email) ? $request->email : '';
            $password = isset($request->password) ? Hash::make($request->password) : '';
            $full_name = isset($request->full_name) ? $request->full_name : '';
            $mobile_number = isset($request->mobile_number) ? $request->mobile_number : '';
            $country_code = isset($request->country_code) ? $request->country_code : "";
            $country_name = isset($request->country_name) ? $request->country_name : "";
            $device_token = isset($request->device_token) ? $request->device_token : "";
            $device_type = isset($request->device_type) ? $request->device_type : 0;

            $image = '';
            if (isset($request['image']) && $request['image'] != null) {

                $file = $request->file('image');
                $image = $this->common->saveImage($file, $this->folder_user, "user_");
            }

            // OTP
            if ($type == 1) {

                $user = User::where('mobile_number', $mobile_number)->where('country_code', $country_code)->first();
                if (isset($user) && $user != null) {

                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                } else {

                    $insert = [
                        'user_name' => $this->common->user_name($mobile_number),
                        'full_name' => $full_name,
                        'country_code' => $country_code,
                        'mobile_number' => $mobile_number,
                        'country_name' => $country_name,
                        'email' => "",
                        'password' => "",
                        'image' => "",
                        'type' => $type,
                        'device_type' => $device_type,
                        'device_token' => $device_token,
                        'status' => 1
                    ];
                    $user_id = User::insertGetId($insert);

                    if (isset($user_id)) {

                        $user = User::where('id', $user_id)->first();

                        $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                        $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                        return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                }
            }

            // Google || Apple
            if ($type == 2 || $type == 3) {

                $user = User::where('email', $email)->first();
                if (isset($user) && $user != null) {

                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                } else {

                    $email_array = explode('@', $request->email);
                    $user_name  = $this->common->user_name($email_array[0]);
                    $insert = [
                        'user_name' => $user_name,
                        'full_name' => $full_name,
                        'country_code' => $country_code,
                        'mobile_number' => $mobile_number,
                        'country_name' => $country_code,
                        'email' => $email,
                        'password' => $password,
                        'image' => $image,
                        'type' => $type,
                        'device_type' => $device_type,
                        'device_token' => $device_token,
                        'status' => 1
                    ];
                    $user_id = User::insertGetId($insert);

                    if (isset($user_id)) {

                        $user = User::where('id', $user_id)->first();
                        $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                        $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                        // Send Mail (Type = 1- Register Mail, 2 Transaction Mail, 3-Login Mail)
                        if ($type == 2 || $type == 3) {
                            $this->common->Send_Mail(3, $user->email);
                        }

                        return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                }
            }

            // Normal
            if ($type == 4) {

                $user = User::where('email', $email)->first();
                if (isset($user)) {

                    if (Hash::check($request->password, $user->password)) {

                        User::where('id', $user['id'])->update(['device_type' => $device_type]);
                        User::where('id', $user['id'])->update(['device_token' => $device_token]);
                        $user['device_type'] = $device_type;
                        $user['device_token'] = $device_token;

                        $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                        $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                        return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                    }
                } else {
                    return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                }
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_profile(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $user_id = $request['user_id'];

            $user_data = User::where('id', $user_id)->first();
            if (!empty($user_data) && isset($user_data)) {

                $this->common->imageNameToUrl(array($user_data), 'image', $this->folder_user);
                $user_data->is_buy = $this->common->is_any_package_buy($user_data->id);

                $transaction = Transaction::where('user_id', $user_data->id)->where('status', 1)->with('package')->first();
                if (isset($transaction) &&  $transaction != null) {
                    $user_data['package_name'] =  $transaction['package']['name'];
                    $user_data['package_price'] = $transaction['price'];
                } else {
                    $user_data['package_name'] =  "";
                    $user_data['package_price'] = "";
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), array($user_data));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update_profile(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                ],
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $user_id = $request['user_id'];
            $array = array();

            $data = User::where('id', $user_id)->first();
            if (!empty($data) && isset($data) && $data != null) {

                if (isset($request->user_name) && $request->user_name != '') {

                    $check = User::where('user_name', $request->user_name)->first();
                    if (isset($check) && $check != null) {
                        if ($check->id == $data->id) {
                            $array['user_name'] = $request->user_name;
                        } else {
                            return $this->common->API_Response(400, __('api_msg.user_name_exists'));
                        }
                    } else {
                        $array['user_name'] = $request->user_name;
                    }
                }
                if (isset($request->full_name) && $request->full_name != '') {
                    $array['full_name'] = $request->full_name;
                }
                if (isset($request->email) && $request->email != '') {
                    
                    $email = $request->email;
                    $email_data = User::where('id', '!=', $user_id)->where('email', $email)->first();
                    if ($email_data != null) {
                        return $this->common->API_Response(400, __('api_msg.email_already_exits'));
                    } else {
                        $array['email'] = $email;
                    }
                }
                if (isset($request->password) && $request->password != '') {
                    $array['password'] = hash::make($request->password);
                }
                if ((isset($request->mobile_number) && $request->mobile_number != '') && (isset($request->country_code) && $request->country_code != '')) {

                    $mobile_number = User::where('id', '!=', $user_id)->where('country_code', $request->country_code)->where('mobile_number', $request->mobile_number)->first();
                    if ($mobile_number != null) {
                        return $this->common->API_Response(400, __('api_msg.mobile_number_already_exits'));
                    } else {
                        $array['mobile_number'] = $request->mobile_number;
                        $array['country_code'] = $request->country_code;
                    }
                }
                if (isset($request->country_name) && $request->country_name != '') {
                    $array['country_name'] = $request->country_name;
                }
                if (isset($request->image) && $request->file('image') != '') {

                    $image = $request->file('image');
                    $old_image = $data['image'];
                    $array['image'] = $this->common->saveImage($image, $this->folder_user, "user_");
                    $this->common->deleteImageToFolder($this->folder_user, $old_image);
                }

                User::where('id', $user_id)->update($array);

                $user = User::where('id', $user_id)->first();
                $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);

                return $this->common->API_Response(200, __('api_msg.profile_update'), array($user));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
