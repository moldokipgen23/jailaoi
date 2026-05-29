<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block_Channel;
use Illuminate\Http\Request;
use App\Models\Common;
use App\Models\Interests;
use App\Models\Live_User;
use App\Models\Refer_Earn;
use App\Models\Subscriber;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Login Type = 1 = OTP, 2 = Goggle, 3 = Apple, 4 = Normal
class UserController extends Controller
{
    private $folder_user = "user";
    private $folder_package = "package";
    public $common;
    public $page_limit;
    public function __construct()
    {
        try {
            $this->common = new Common();
            $this->page_limit = env('PAGE_LIMIT');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function login(Request $request) // 1- OTP, 2- Goggle, 3- Apple, 4- Normal
    {
        try {

            if ($request['type'] == 1) {

                $validation = Validator::make($request->all(), [
                    'country_code' => 'required',
                    'mobile_number' => 'required|numeric',
                    'country_name' => 'required',
                ]);
            } elseif ($request['type'] == 2 || $request['type'] == 3) {

                $validation = Validator::make($request->all(), [
                    'email' => 'required',
                ]);
            } elseif ($request['type'] == 4) {

                $validation = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required|min:4',
                ]);
            } else {

                $validation = Validator::make($request->all(), [
                    'type' => 'required|numeric',
                ]);
            }
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $request['email'] = Str::lower($request['email']);
            $request['mobile_number'] = Str::lower($request['mobile_number']);

            $type = $request['type'];
            $full_name = $request['full_name'] ?? '';
            $email = $request['email'] ?? '';
            $password = Hash::make($request['password']) ?? '';
            $country_code = $request['country_code'] ?? '';
            $mobile_number = $request['mobile_number'] ?? '';
            $country_name = $request['country_name'] ?? '';
            $device_type = $request['device_type'] ?? 0;
            $device_token = $request['device_token'] ?? '';
            $storage_type = Storage_Type();
            $image = '';
            if (isset($request['image']) && $request['image'] != null) {
                $file = $request->file('image');
                $image = $this->common->saveImage($file, $this->folder_user, $storage_type);
            }
            $reference_code = $request['reference_code'] ?? '';

            // OTP
            if ($type == 1) {

                $user = User::where('mobile_number', $mobile_number)->where('country_code', $country_code)->latest()->first();
                if ($user) {

                    // Update Device Token && Type
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                    $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                    $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                    $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    $package_data = Transaction::where('user_id', $user['id'])->where('status', 1)->with('package')->latest()->first();
                    $user['package_name'] = $package_data['package']['name'] ?? '';
                    $user['package_price'] = $package_data['package']['price'] ?? '';
                    $user['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                    $user['ads_free'] = $package_data['package']['ads_free'] ?? '';
                    $user['download_content'] = $package_data['package']['download_content'] ?? '';
                    $user['background_play'] = $package_data['package']['background_play'] ?? '';

                    return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                } else {

                    $insert = array(
                        'channel_id' => Str::random(8),
                        'channel_name' => $this->common->createChannelName('@channel_' . $mobile_number),
                        'full_name' => $full_name,
                        'email' => "",
                        'password' => "",
                        'country_code' => $country_code,
                        'mobile_number' => $mobile_number,
                        'country_name' => $country_name,
                        'type' => $type,
                        'image_storage_type' => $storage_type,
                        'image' => $image,
                        'cover_img_storage_type' => $storage_type,
                        'cover_img' => "",
                        'description' => $this->common->user_tag_line(),
                        'device_type' => $device_type,
                        'device_token' => $device_token,
                        'website' => "",
                        'facebook_url' => "",
                        'instagram_url' => "",
                        'twitter_url' => "",
                        'wallet_balance' => 0,
                        'wallet_earning' => 0,
                        'is_account_verify' => 0,
                        'bank_name' => "",
                        'bank_code' => "",
                        'bank_address' => "",
                        'ifsc_no' => "",
                        'account_no' => "",
                        'front_id_proof_storage_type' => $storage_type,
                        'front_id_proof' => "",
                        'back_id_proof_storage_type' => $storage_type,
                        'back_id_proof' => "",
                        'address' => "",
                        'city' => "",
                        'state' => "",
                        'country' => "",
                        'pincode' => 0,
                        'user_penal_status' => 0,
                        'reference_code' => Str::random(6),
                        'push_notification_status' => 1,
                        'send_mail_status' => 1,
                        'status' => 1,
                    );
                    $user_id = User::insertGetId($insert);

                    if (isset($user_id)) {

                        $user = User::where('id', $user_id)->first();
                        if ($user) {

                            if (isset($reference_code) && $reference_code != "" && $reference_code != null) {

                                $parent_user = User::where('reference_code', $reference_code)->first();
                                if (!$parent_user) {
                                    return $this->common->API_Response(400, __('api_msg.reference_code_is_worng'));
                                    $user->delete();
                                }

                                $setting = Setting_Data();

                                $refer_insert = new Refer_Earn();
                                $refer_insert['parent_user_id'] = $parent_user['id'];
                                $refer_insert['reference_code'] = $reference_code;
                                $refer_insert['child_user_id'] = $user['id'];
                                $refer_insert['parent_earn'] = $setting['parent_user_earn'];
                                $refer_insert['child_earn'] = $setting['child_user_earn'];
                                $refer_insert['status'] = 1;
                                $refer_insert->save();

                                $parent_user['wallet_earning'] = $parent_user['wallet_earning'] + $setting['parent_user_earn'];
                                $parent_user->save();

                                $user['wallet_earning'] = $user['wallet_earning'] + $setting['child_user_earn'];
                                $user->save();
                            }

                            $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                            $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                            $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                            $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                            $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                            $package_data = Transaction::where('user_id', $user['id'])->where('status', 1)->with('package')->latest()->first();
                            $user['package_name'] = $package_data['package']['name'] ?? '';
                            $user['package_price'] = $package_data['package']['price'] ?? '';
                            $user['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                            $user['ads_free'] = $package_data['package']['ads_free'] ?? '';
                            $user['download_content'] = $package_data['package']['download_content'] ?? '';
                            $user['background_play'] = $package_data['package']['background_play'] ?? '';

                            return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                        } else {
                            return $this->common->API_Response(400, __('api_msg.data_not_found'));
                        }
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                }
            }

            // Google || Apple
            if ($type == 2 || $type == 3) {

                $user = User::where('email', $email)->where('type', $type)->latest()->first();
                if ($user) {

                    // Update Device Token && Type
                    User::where('id', $user['id'])->update(['device_token' => $device_token]);
                    User::where('id', $user['id'])->update(['device_type' => $device_type]);
                    $user['device_type'] = $device_type;
                    $user['device_token'] = $device_token;

                    $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                    $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                    $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                    $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                    $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                    $package_data = Transaction::where('user_id', $user['id'])->where('status', 1)->with('package')->latest()->first();
                    $user['package_name'] = $package_data['package']['name'] ?? '';
                    $user['package_price'] = $package_data['package']['price'] ?? '';
                    $user['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                    $user['ads_free'] = $package_data['package']['ads_free'] ?? '';
                    $user['download_content'] = $package_data['package']['download_content'] ?? '';
                    $user['background_play'] = $package_data['package']['background_play'] ?? '';

                    return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                } else {

                    $channel_name = explode('@', $email);
                    $insert = array(
                        'channel_id' => Str::random(8),
                        'channel_name' => $this->common->createChannelName('@channel_' . $channel_name[0]),
                        'full_name' => $full_name,
                        'email' => $email,
                        'password' => "",
                        'country_code' => "",
                        'mobile_number' => "",
                        'country_name' => "",
                        'type' => $type,
                        'image_storage_type' => $storage_type,
                        'image' => $image,
                        'cover_img_storage_type' => $storage_type,
                        'cover_img' => "",
                        'description' => $this->common->user_tag_line(),
                        'device_type' => $device_type,
                        'device_token' => $device_token,
                        'website' => "",
                        'facebook_url' => "",
                        'instagram_url' => "",
                        'twitter_url' => "",
                        'wallet_balance' => 0,
                        'wallet_earning' => 0,
                        'is_account_verify' => 0,
                        'bank_name' => "",
                        'bank_code' => "",
                        'bank_address' => "",
                        'ifsc_no' => "",
                        'account_no' => "",
                        'front_id_proof_storage_type' => $storage_type,
                        'front_id_proof' => "",
                        'back_id_proof_storage_type' => $storage_type,
                        'back_id_proof' => "",
                        'address' => "",
                        'city' => "",
                        'state' => "",
                        'country' => "",
                        'pincode' => 0,
                        'user_penal_status' => 0,
                        'reference_code' => Str::random(6),
                        'push_notification_status' => 1,
                        'send_mail_status' => 1,
                        'status' => 1,
                    );
                    $user_id = User::insertGetId($insert);

                    if (isset($user_id)) {

                        $user = User::where('id', $user_id)->first();
                        if (isset($user)) {

                            if (isset($reference_code) && $reference_code != "" && $reference_code != null) {

                                $parent_user = User::where('reference_code', $reference_code)->first();
                                if (!$parent_user) {
                                    return $this->common->API_Response(400, __('api_msg.reference_code_is_worng'));
                                    $user->delete();
                                }

                                $setting = Setting_Data();

                                $refer_insert = new Refer_Earn();
                                $refer_insert['parent_user_id'] = $parent_user['id'];
                                $refer_insert['reference_code'] = $reference_code;
                                $refer_insert['child_user_id'] = $user['id'];
                                $refer_insert['parent_earn'] = $setting['parent_user_earn'];
                                $refer_insert['child_earn'] = $setting['child_user_earn'];
                                $refer_insert['status'] = 1;
                                $refer_insert->save();

                                $parent_user['wallet_earning'] = $parent_user['wallet_earning'] + $setting['parent_user_earn'];
                                $parent_user->save();

                                $user['wallet_earning'] = $user['wallet_earning'] + $setting['child_user_earn'];
                                $user->save();
                            }

                            $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                            $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                            $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                            $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                            $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                            $package_data = Transaction::where('user_id', $user['id'])->where('status', 1)->with('package')->latest()->first();
                            $user['package_name'] = $package_data['package']['name'] ?? '';
                            $user['package_price'] = $package_data['package']['price'] ?? '';
                            $user['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                            $user['ads_free'] = $package_data['package']['ads_free'] ?? '';
                            $user['download_content'] = $package_data['package']['download_content'] ?? '';
                            $user['background_play'] = $package_data['package']['background_play'] ?? '';

                            // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                            if ($type == 2) {
                                $this->common->Send_Mail(1, $user->email);
                            }
                            return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                        } else {
                            return $this->common->API_Response(400, __('api_msg.data_not_found'));
                        }
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                }
            }

            // Normal
            if ($type == 4) {

                $user = User::where('email', $email)->latest()->first();
                if (isset($user)) {

                    if (Hash::check($request['password'], $user['password'])) {

                        // Update Device Token && Type
                        User::where('id', $user['id'])->update(['device_token' => $device_token]);
                        User::where('id', $user['id'])->update(['device_type' => $device_type]);
                        $user['device_type'] = $device_type;
                        $user['device_token'] = $device_token;

                        $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                        $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                        $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                        $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                        $user['is_buy'] = $this->common->is_any_package_buy($user['id']);

                        $package_data = Transaction::where('user_id', $user['id'])->where('status', 1)->with('package')->latest()->first();
                        $user['package_name'] = $package_data['package']['name'] ?? '';
                        $user['package_price'] = $package_data['package']['price'] ?? '';
                        $user['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                        $user['ads_free'] = $package_data['package']['ads_free'] ?? '';
                        $user['download_content'] = $package_data['package']['download_content'] ?? '';
                        $user['background_play'] = $package_data['package']['background_play'] ?? '';

                        return $this->common->API_Response(200, __('api_msg.login_successfully'), array($user));
                    } else {
                        return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                    }
                } else {
                    return $this->common->API_Response(400, __('api_msg.email_pass_worng'));
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_profile(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'to_user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $to_user_id = $request['to_user_id'];

            $user_data = User::where('id', $to_user_id)->first();
            if ($user_data) {

                $user_data['image'] = $this->common->getImage($this->folder_user, $user_data['image'], $user_data['image_storage_type']);
                $user_data['cover_img'] = $this->common->getImage($this->folder_user, $user_data['cover_img'], $user_data['cover_img_storage_type']);
                $user_data['front_id_proof'] = $this->common->getImage($this->folder_user, $user_data['front_id_proof'], $user_data['front_id_proof_storage_type']);
                $user_data['back_id_proof'] = $this->common->getImage($this->folder_user, $user_data['back_id_proof'], $user_data['back_id_proof_storage_type']);
                $user_data['is_buy'] = $this->common->is_any_package_buy($to_user_id);
                $user_data['is_block'] = $this->common->is_block($user_id, $to_user_id);
                $user_data['total_content'] = $this->common->is_total_content($user_data['channel_id']);
                $user_data['total_subscriber'] = $this->common->total_subscriber($to_user_id);
                $user_data['is_subscribe'] = $this->common->is_subscribe($user_id, $to_user_id);

                $package_data = Transaction::where('user_id', $user_data['id'])->where('status', 1)->with('package')->latest()->first();
                $user_data['package_name'] = $package_data['package']['name'] ?? '';
                $user_data['package_price'] = $package_data['package']['price'] ?? '';
                $user_data['package_image'] = isset($package_data['package']) ? $this->common->getImage($this->folder_package, $package_data['package']['image'], $package_data['package']['storage_type']) : asset('assets/imgs/no_img.png');
                $user_data['ads_free'] = $package_data['package']['ads_free'] ?? '';
                $user_data['download_content'] = $package_data['package']['download_content'] ?? '';
                $user_data['background_play'] = $package_data['package']['background_play'] ?? '';

                return $this->common->API_Response(200, __('api_msg.data_retrieved'), array($user_data));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update_profile(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $array = array();

            $data = User::where('id', $user_id)->first();
            if ($data) {

                if (isset($request['channel_name']) && $request['channel_name'] != '') {

                    $check = User::where('channel_name', $request['channel_name'])->first();
                    if ($check) {
                        if ($check['id'] == $data['id']) {
                            $array['channel_name'] = $request['channel_name'];
                        } else {
                            return $this->common->API_Response(400, __('api_msg.channel_name_exists'));
                        }
                    } else {
                        $array['channel_name'] = $request['channel_name'];
                    }
                }
                if (isset($request['full_name']) && $request['full_name'] != '') {
                    $array['full_name'] = $request['full_name'];
                }
                if (isset($request['email']) && $request['email'] != '') {

                    $request['email'] = Str::lower($request['email']);
                    $check_email = User::where('id', '!=', $user_id)->where('email', $request['email'])->first();
                    if ($check_email) {
                        return $this->common->API_Response(400, __('api_msg.this_email_is_already_register'));
                    }
                    $array['email'] = $request['email'];
                }
                if (isset($request['password']) && $request['password'] != '') {
                    $array['password'] = hash::make($request['password']);
                }
                if (isset($request['country_code']) && $request['country_code'] != '' && isset($request['mobile_number']) && $request['mobile_number'] != '') {

                    $request['mobile_number'] = Str::lower($request['mobile_number']);
                    $check_mobile = User::where('id', '!=', $user_id)->where('country_code', $request['country_code'])->where('mobile_number', $request['mobile_number'])->first();
                    if ($check_mobile) {
                        return $this->common->API_Response(400, __('api_msg.this_mobile_number_is_already_register'));
                    }

                    $array['country_code'] = $request['country_code'];
                    $array['mobile_number'] = $request['mobile_number'];
                }
                if (isset($request['country_name']) && $request['country_name'] != '') {
                    $array['country_name'] = $request['country_name'];
                }
                $storage_type = Storage_Type();
                if (isset($request['image']) && $request->file('image') != '') {

                    $array['image_storage_type'] = $storage_type;
                    $image = $request->file('image');
                    $array['image'] = $this->common->saveImage($image, $this->folder_user, $storage_type);

                    $old_image = $data['image'];
                    $old_storage_type = $data['image_storage_type'];
                    $this->common->deleteImageToFolder($this->folder_user, $old_image, $old_storage_type);
                }
                if (isset($request['cover_img']) && $request->file('cover_img') != '') {

                    $array['cover_img_storage_type'] = $storage_type;
                    $cover_img = $request->file('cover_img');
                    $array['cover_img'] = $this->common->saveImage($cover_img, $this->folder_user, $storage_type);

                    $old_cover_img = $data['cover_img'];
                    $old_cover_img_storage_type = $data['cover_img_storage_type'];
                    $this->common->deleteImageToFolder($this->folder_user, $old_cover_img, $old_cover_img_storage_type);
                }
                if (isset($request['description']) && $request['description'] != '') {
                    $array['description'] = $request['description'];
                }
                if (isset($request['device_type']) && $request['device_type'] != '') {
                    $array['device_type'] = $request['device_type'];
                }
                if (isset($request['device_typedevice_type']) && $request['device_token'] != '') {
                    $array['device_token'] = $request['device_token'];
                }
                if (isset($request['website']) && $request['website'] != '') {
                    $array['website'] = $request['website'];
                }
                if (isset($request['facebook_url']) && $request['facebook_url'] != '') {
                    $array['facebook_url'] = $request['facebook_url'];
                }
                if (isset($request['instagram_url']) && $request['instagram_url'] != '') {
                    $array['instagram_url'] = $request['instagram_url'];
                }
                if (isset($request['twitter_url']) && $request['twitter_url'] != '') {
                    $array['twitter_url'] = $request['twitter_url'];
                }
                if (isset($request['is_account_verify']) && $request['is_account_verify'] != '') {
                    $array['is_account_verify'] = $request['is_account_verify'];
                }
                if (isset($request['bank_name']) && $request['bank_name'] != '') {
                    $array['bank_name'] = $request['bank_name'];
                }
                if (isset($request['bank_code']) && $request['bank_code'] != '') {
                    $array['bank_code'] = $request['bank_code'];
                }
                if (isset($request['bank_address']) && $request['bank_address'] != '') {
                    $array['bank_address'] = $request['bank_address'];
                }
                if (isset($request['ifsc_no']) && $request['ifsc_no'] != '') {
                    $array['ifsc_no'] = $request['ifsc_no'];
                }
                if (isset($request['account_no']) && $request['account_no'] != '') {
                    $array['account_no'] = $request['account_no'];
                }
                if (isset($request['front_id_proof']) && $request->file('front_id_proof') != '') {

                    $array['front_id_proof_storage_type'] = $storage_type;
                    $front_id_proof = $request->file('front_id_proof');
                    $array['front_id_proof'] = $this->common->saveImage($front_id_proof, $this->folder_user, $storage_type);

                    $old_front_id_proof = $data['front_id_proof'];
                    $old_front_id_proof_storage_type = $data['front_id_proof_storage_type'];
                    $this->common->deleteImageToFolder($this->folder_user, $old_front_id_proof, $old_front_id_proof_storage_type);
                }
                if (isset($request['back_id_proof']) && $request->file('back_id_proof') != '') {

                    $array['back_id_proof_storage_type'] = $storage_type;
                    $back_id_proof = $request->file('back_id_proof');
                    $array['back_id_proof'] = $this->common->saveImage($back_id_proof, $this->folder_user, $storage_type);

                    $old_back_id_proof = $data['back_id_proof'];
                    $old_back_id_proof_storage_type = $data['back_id_proof_storage_type'];
                    $this->common->deleteImageToFolder($this->folder_user, $old_back_id_proof, $old_back_id_proof_storage_type);
                }
                if (isset($request['address']) && $request['address'] != '') {
                    $array['address'] = $request['address'];
                }
                if (isset($request['city']) && $request['city'] != '') {
                    $array['city'] = $request['city'];
                }
                if (isset($request['state']) && $request['state'] != '') {
                    $array['state'] = $request['state'];
                }
                if (isset($request['country']) && $request['country'] != '') {
                    $array['country'] = $request['country'];
                }
                if (isset($request['pincode']) && $request['pincode'] != '') {
                    $array['pincode'] = $request['pincode'];
                }
                if (isset($request['user_penal_status']) && $request['user_penal_status'] != '') {
                    $array['user_penal_status'] = $request['user_penal_status'];
                }
                if (isset($request['push_notification_status']) && $request['push_notification_status'] != '') {
                    $array['push_notification_status'] = $request['push_notification_status'];
                }
                if (isset($request['send_mail_status']) && $request['send_mail_status'] != '') {
                    $array['send_mail_status'] = $request['send_mail_status'];
                }
                User::where('id', $user_id)->update($array);

                $user = User::where('id', $user_id)->first();

                $user['image'] = $this->common->getImage($this->folder_user, $user['image'], $user['image_storage_type']);
                $user['cover_img'] = $this->common->getImage($this->folder_user, $user['cover_img'], $user['cover_img_storage_type']);
                $user['front_id_proof'] = $this->common->getImage($this->folder_user, $user['front_id_proof'], $user['front_id_proof_storage_type']);
                $user['back_id_proof'] = $this->common->getImage($this->folder_user, $user['back_id_proof'], $user['back_id_proof_storage_type']);
                $user['is_buy'] = $this->common->is_any_package_buy($user_id);

                if ($data['user_penal_status'] == 0 && $user['user_penal_status'] == 1 && $user['email'] != null && isset($user['email'])) {

                    // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                    $this->common->Send_Mail(4, $user['email']);
                }
                return $this->common->API_Response(200, __('api_msg.profile_updated'), array($user));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_subscribe(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'to_user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $to_user_id = $request['to_user_id'];

            $subcribe = Subscriber::where('user_id', $user_id)->where('to_user_id', $to_user_id)->where('status', 1)->first();
            if ($subcribe) {

                Subscriber::where('id', $subcribe['id'])->delete();
                return $this->common->API_Response(200, __('api_msg.unsubscribe_successfully'));
            } else {

                $insert['user_id'] = $user_id;
                $insert['to_user_id'] = $to_user_id;
                $insert['status'] = 1;
                Subscriber::insertGetId($insert);

                // Send Notification
                $user = User::find($user_id);
                $from_user = User::where('id', $to_user_id)->where('push_notification_status', 1)->first();
                if ($user && $from_user && $user->id != $from_user->id) {

                    $title = $user['channel_name'] . ' Subscribe to You.';
                    $this->common->save_notification(4, $title, $user_id, $to_user_id, 0);
                }

                return $this->common->API_Response(200, __('api_msg.subscribe_successfully'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_subscribe_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $data = Subscriber::where('user_id', $user_id)->where('status', 1)->with('to_user')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {

                $content_data = [];
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['to_user'] != null) {

                        $data[$i]['to_user']['image'] = $this->common->getImage($this->folder_user, $data[$i]['to_user']['image'], $data[$i]['to_user']['image_storage_type']);
                        $data[$i]['to_user']['cover_img'] = $this->common->getImage($this->folder_user, $data[$i]['to_user']['cover_img'], $data[$i]['to_user']['cover_img_storage_type']);
                        $data[$i]['to_user']['front_id_proof'] = $this->common->getImage($this->folder_user, $data[$i]['to_user']['front_id_proof'], $data[$i]['to_user']['front_id_proof_storage_type']);
                        $data[$i]['to_user']['back_id_proof'] = $this->common->getImage($this->folder_user, $data[$i]['to_user']['back_id_proof'], $data[$i]['to_user']['back_id_proof_storage_type']);
                        $data[$i]['to_user']['is_buy'] = $this->common->is_any_package_buy($data[$i]['to_user']['id']);
                        $data[$i]['to_user']['total_subscriber'] = $this->common->total_subscriber($data[$i]['to_user']['id']);

                        $content_data[] = $data[$i]['to_user'];
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $content_data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_subscriber_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $data = Subscriber::where('to_user_id', $user_id)->where('status', 1)->with('user')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {

                $content_data = [];
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['user'] != null) {

                        $data[$i]['user']['image'] = $this->common->getImage($this->folder_user, $data[$i]['user']['image'], $data[$i]['user']['image_storage_type']);
                        $data[$i]['user']['cover_img'] = $this->common->getImage($this->folder_user, $data[$i]['user']['cover_img'], $data[$i]['user']['cover_img_storage_type']);
                        $data[$i]['user']['front_id_proof'] = $this->common->getImage($this->folder_user, $data[$i]['user']['front_id_proof'], $data[$i]['user']['front_id_proof_storage_type']);
                        $data[$i]['user']['back_id_proof'] = $this->common->getImage($this->folder_user, $data[$i]['user']['back_id_proof'], $data[$i]['user']['back_id_proof_storage_type']);
                        $data[$i]['user']['is_buy'] = $this->common->is_any_package_buy($data[$i]['user']['id']);
                        $data[$i]['user']['total_subscriber'] = $this->common->total_subscriber($data[$i]['user']['id']);

                        $content_data[] = $data[$i]['user'];
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $content_data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_block_channel(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'block_user_id' => 'required|numeric',
                'block_channel_id' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $block_user_id = $request['block_user_id'];
            $block_channel_id = $request['block_channel_id'];

            $block_channel = Block_Channel::where('user_id', $user_id)->where('block_user_id', $block_user_id)->where('block_channel_id', $block_channel_id)->where('status', 1)->first();
            if ($block_channel) {

                Block_Channel::where('id', $block_channel['id'])->delete();
                return $this->common->API_Response(200, __('api_msg.unblock_channel'));
            } else {

                $insert['user_id'] = $user_id;
                $insert['block_user_id'] = $block_user_id;
                $insert['block_channel_id'] = $block_channel_id;
                $insert['status'] = 1;
                Block_Channel::insertGetId($insert);

                return $this->common->API_Response(200, __('api_msg.block_channel'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function logout(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $data = User::where('id', $user_id)->first();

            $array = array();
            if (!empty($data) && isset($data) && $data != null) {

                $array['device_type'] = 0;
                $array['device_token'] = "";
                User::where('id', $user_id)->update($array);
                Interests::where('user_id', $user_id)->delete();

                return $this->common->API_Response(200, __('api_msg.logout_successfully'));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function list_of_live_users(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Live_User::whereNotIn('user_id', [$user_id])->where('status', 1)->with('user')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            foreach ($data as $key => $value) {

                $value['channel_id'] = $value['user']['channel_id'] ?? "";
                $value['channel_name'] = $value['user']['channel_name'] ?? "";
                $value['full_name'] = $value['user']['full_name'] ?? "";
                $value['email'] = $value['user']['email'] ?? "";
                $value['country_code'] = $value['user']['country_code'] ?? "";
                $value['mobile_number'] = $value['user']['mobile_number'] ?? "";
                $value['country_name'] = $value['user']['country_name'] ?? "";
                $value['image'] = isset($value['user']) ? $this->common->getImage($this->folder_user, $value['user']['image'], $value['user']['image_storage_type']) : asset('/assets/imgs/default.png');
                $value['is_fake'] = 0;
                $value['is_buy'] = $this->common->is_any_package_buy($user_id);

                unset($value['user']);
            }
            return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
