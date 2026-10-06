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
    private $folder_user = "images/user";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    private function authenticatedResponse(User $user, string $message)
    {
        if ($user->status !== 1) {
            return response()->json(['status' => 403, 'message' => 'This account is disabled.'], 403);
        }
        $expiresAt = now()->addDays(30);
        $response = $this->common->API_Response(200, $message, [$user]);
        $response['token'] = $user->createToken('listener', ['listener'], $expiresAt)->plainTextToken;
        $response['token_expires_at'] = $expiresAt->toIso8601String();
        return $response;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['status' => 200, 'message' => 'Signed out successfully.']);
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
                        Rule::unique('tbl_user')->where(function ($query) use ($request) {
                            return $query->where('country_code', $request->country_code)
                                ->where('mobile_number', $request->mobile_number);
                        }),
                    ],
                    'country_name' => 'required',
                    'password' => 'required|min:8',
                    'gender' => 'required',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $data = User::where('email', $request->email)->first();
            if (isset($data)) {
                return $this->common->API_Response(400, __('api_msg.email_already_exists'));
            } else {

                $email = $request->email;
                $data['type'] = 4;
                $data['full_name'] = $request->full_name;
                $data['country_code'] = $request->country_code;
                $data['mobile_number'] = $request->mobile_number;
                $data['country_name'] = $request->country_name;
                $data['email'] = $email;
                $data['password'] = Hash::make($request->password);
                $data['gender'] = $request->gender;
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

                        $check = $this->common->basic_notification_configuration('login');
                        if ($check['status'] == 1 && $check['send_mail'] == 1) {
                            $this->common->Send_Mail(1, $user->email, '', '', 0, '', '');
                        }

                        $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);

                        return $this->authenticatedResponse($user, __('api_msg.register_successfully'));
                    }
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 422, 'message' => $e->validator->errors()->first()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Account API request failed', ['exception' => get_class($e)]);
            return response()->json(['status' => 500, 'message' => 'Unable to complete this request. Please try again.'], 500);
        }
    }
    public function login(Request $request)
    {
        try {

            $request->validate([
                'type' => 'required|integer|in:1,2,3,4',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'identity_token' => 'required_if:type,1,2,3|string|max:16384',
            ]);
            if (in_array((int) $request->type, [1, 2, 3], true)) {
                $claims = app(\App\Services\FirebaseIdentityVerifier::class)->verify($request->identity_token);
                $provider = [1 => 'phone', 2 => 'google.com', 3 => 'apple.com'][(int) $request->type];
                if (($claims['firebase']['sign_in_provider'] ?? '') !== $provider) {
                    return response()->json(['status' => 422, 'message' => 'Please verify your sign-in method.'], 422);
                }
                if ((int) $request->type === 1) {
                    $submitted = preg_replace('/\D/', '', $request->country_code . $request->mobile_number);
                    if ($submitted !== preg_replace('/\D/', '', $claims['phone_number'] ?? '')) {
                        return response()->json(['status' => 422, 'message' => 'Phone verification does not match.'], 422);
                    }
                } else {
                    if (empty($claims['email']) || ($claims['email_verified'] ?? false) !== true) {
                        return response()->json(['status' => 422, 'message' => 'A verified email is required.'], 422);
                    }
                    $request->merge(['email' => $claims['email']]);
                }
            }

            if ($request->type == 1) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'country_code' => 'required',
                        'mobile_number' => 'required|numeric',
                        'country_name' => 'required',
                    ],
                );
            } elseif ($request->type == 2 || $request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required',
                    ],
                );
            } elseif ($request->type == 4) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'email' => 'required|email',
                        'password' => 'required|min:4',
                    ],
                );
            } else {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                );
            }
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
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
            $gender = isset($request->gender) ? $request->gender : 1;

            $image = '';
            if (isset($request['image']) && $request['image'] != null) {

                $file = $request->file('image');
                $image = $this->common->saveImage($file, $this->folder_user, "user_");
            }

            // OTP
            if ($type == 1) {

                $user = User::where('mobile_number', $mobile_number)->where('country_code', $country_code)->first();
                if (isset($user) && $user != null) {
                    // A phone typed into a password/social profile is not verified account-linking proof.
                    if ((int)$user->type !== 1) return response()->json(['status'=>403,'message'=>'Please use your original password or social sign-in. Linking phone sign-in requires account verification.'],403);
                    if ($user->status !== 1) return response()->json(['status' => 403, 'message' => 'This account is disabled.'], 403);

                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    return $this->authenticatedResponse($user, __('api_msg.login_successfully'));
                } else {

                    $insert = [
                        'user_name' => $this->common->user_name($mobile_number),
                        'full_name' => $full_name,
                        'country_code' => $country_code,
                        'mobile_number' => $mobile_number,
                        'country_name' => $country_name,
                        'email' => "",
                        'password' => "",
                        'gender' => $gender,
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

                        return $this->authenticatedResponse($user, __('api_msg.login_successfully'));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                }
            }

            // Google || Apple
            if ($type == 2 || $type == 3) {

                $user = User::where('email', $email)->first();
                if (isset($user) && $user != null) {
                    if ($user->status !== 1) return response()->json(['status' => 403, 'message' => 'This account is disabled.'], 403);

                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    return $this->authenticatedResponse($user, __('api_msg.login_successfully'));
                } else {

                    $email_array = explode('@', $request->email);
                    $user_name  = $this->common->user_name($email_array[0]);
                    $insert = [
                        'user_name' => $user_name,
                        'full_name' => $full_name,
                        'country_code' => $country_code,
                        'mobile_number' => $mobile_number,
                        'country_name' => $country_name,
                        'email' => $email,
                        'password' => $password,
                        'gender' => $gender,
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

                        $check = $this->common->basic_notification_configuration('login');
                        if ($check['status'] == 1 && $check['send_mail'] == 1) {
                            $this->common->Send_Mail(3, $user->email, '', '', 0, '', '');
                        }

                        return $this->authenticatedResponse($user, __('api_msg.login_successfully'));
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

                        return $this->authenticatedResponse($user, __('api_msg.login_successfully'));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                    }
                } else {
                    return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 422, 'message' => $e->validator->errors()->first()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Account API request failed', ['exception' => get_class($e)]);
            return response()->json(['status' => 500, 'message' => 'Unable to complete this request. Please try again.'], 500);
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
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 422, 'message' => $e->validator->errors()->first()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Account API request failed', ['exception' => get_class($e)]);
            return response()->json(['status' => 500, 'message' => 'Unable to complete this request. Please try again.'], 500);
        }
    }
    public function update_profile(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                    'password' => 'nullable|string|min:8',
                    'current_password' => 'required_with:password',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            if ($request->filled('password') && !Hash::check($request->current_password, $request->user()->password)) {
                return response()->json(['status' => 422, 'message' => 'Current password is incorrect.'], 422);
            }
            $user_id = $request['user_id'];
            $array = array();

            $data = User::where('id', $user_id)->first();
            if (!empty($data) && isset($data) && $data != null) {

                // Profile edits cannot change sign-in identifiers without a separate verified change flow.
                foreach (['email', 'mobile_number', 'country_code'] as $field) {
                    if ($request->filled($field)) {
                        $submitted=(string)$request->input($field);$existing=(string)$data->{$field};
                        $normalize=$field==='email' ? fn($v)=>strtolower(trim($v)) : fn($v)=>preg_replace('/\D/','',$v);
                        if($normalize($submitted)!==$normalize($existing))return response()->json(['status'=>422,'message'=>'Changing sign-in details requires a separate verification flow.'],422);
                    }
                }
                if (isset($request->user_name) && $request->user_name != '') {

                    $check = User::where('user_name', $request->user_name)->first();
                    if (isset($check) && $check != null) {
                        if ($check->id == $data->id) {
                            $array['user_name'] = $request->user_name;
                        } else {
                            return $this->common->API_Response(400, __('api_msg.user_name_already_exists'));
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
                        return $this->common->API_Response(400, __('api_msg.email_already_exists'));
                    } else {
                        $array['email'] = $email;
                    }
                }
                if (isset($request->password) && $request->password != '') {
                    $array['password'] = Hash::make($request->password);
                }
                if (isset($request->gender) && $request->gender != '') {
                    $array['gender'] = $request->gender;
                }
                if ((isset($request->mobile_number) && $request->mobile_number != '') && (isset($request->country_code) && $request->country_code != '')) {

                    $mobile_number = User::where('id', '!=', $user_id)->where('country_code', $request->country_code)->where('mobile_number', $request->mobile_number)->first();
                    if ($mobile_number != null) {
                        return $this->common->API_Response(400, __('api_msg.mobile_number_already_exists'));
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
                if ($request->filled('password')) {
                    $currentId=$request->user()->currentAccessToken()?->id;
                    $data->tokens()->when($currentId,fn($q)=>$q->where('id','!=',$currentId))->delete();
                }

                $user = User::where('id', $user_id)->first();
                $this->common->imageNameToUrl(array($user), 'image', $this->folder_user);

                return $this->common->API_Response(200, __('api_msg.profile_update'), array($user));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 422, 'message' => $e->validator->errors()->first()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Account API request failed', ['exception' => get_class($e)]);
            return response()->json(['status' => 500, 'message' => 'Unable to complete this request. Please try again.'], 500);
        }
    }
}
