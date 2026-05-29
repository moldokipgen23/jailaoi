<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
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

            $user = User_Data();

            $params['data'] = User::where('id', $user['id'])->first();
            $params['data']['image'] = $this->common->getImage($this->folder, $params['data']['image'], $params['data']['image_storage_type']);
            $params['data']['cover_img'] = $this->common->getImage($this->folder, $params['data']['cover_img'], $params['data']['cover_img_storage_type']);
            $params['data']['front_id_proof'] = $this->common->getImage($this->folder, $params['data']['front_id_proof'], $params['data']['front_id_proof_storage_type']);
            $params['data']['back_id_proof'] = $this->common->getImage($this->folder, $params['data']['back_id_proof'], $params['data']['back_id_proof_storage_type']);

            return view('user.profile.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $rules = [
                'channel_name' => 'required|min:2|unique:tbl_user,channel_name,' . $id,
                'description' => 'required',
                'full_name' => 'required|min:2',
                'email' => 'required|email|unique:tbl_user,email,' . $id,
                'country_code' => 'required',
                'country_name' => 'required',
                'mobile_number' => [
                    'required',
                    'numeric',
                    Rule::unique('tbl_user')->where(function ($query) use ($request, $id) {
                        return $query->where('country_code', $request->country_code)
                            ->where('mobile_number', $request->mobile_number)
                            ->where('id', '!=', $id);
                    }),
                ],
                'push_notification_status' => 'required',
                'send_mail_status' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg|max:5120',
                'cover_img' => 'image|mimes:jpeg,png,jpg|max:5120',
                'address' => 'required|min:2',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'pincode' => 'required|numeric',
                'is_account_verify' => 'required',
            ];
            if ($request['is_account_verify'] == 1) {
                $rules['bank_name'] = 'required';
                $rules['bank_code'] = 'required';
                $rules['bank_address'] = 'required';
                $rules['ifsc_no'] = 'required';
                $rules['account_no'] = 'required';
                $rules['front_id_proof'] = 'image|mimes:jpeg,png,jpg|max:5120';
                $rules['back_id_proof'] = 'image|mimes:jpeg,png,jpg|max:5120';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            if (isset($request['image'])) {
                $file = $request['image'];
                $requestData['image_storage_type'] = Storage_Type();
                $requestData['image'] = $this->common->saveImage($file, $this->folder, 'img_', $requestData['image_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']), $request['old_image_storage_type']);
            }
            if (isset($request['cover_img'])) {
                $file1 = $request['cover_img'];
                $requestData['cover_img_storage_type'] = Storage_Type();
                $requestData['cover_img'] = $this->common->saveImage($file1, $this->folder, 'cover_img_', $requestData['cover_img_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_cover_img']), $request['old_cover_img_storage_type']);
            }
            $requestData['website'] = $request['website'] ?? '';
            $requestData['facebook_url'] = $request['facebook_url'] ?? '';
            $requestData['instagram_url'] = $request['instagram_url'] ?? '';
            $requestData['twitter_url'] = $request['twitter_url'] ?? '';
            $requestData['bank_name'] = $request['bank_name'] ?? '';
            $requestData['bank_code'] = $request['bank_code'] ?? '';
            $requestData['bank_address'] = $request['bank_address'] ?? '';
            $requestData['ifsc_no'] = $request['ifsc_no'] ?? '';
            $requestData['account_no'] = $request['account_no'] ?? '';
            if (isset($request['front_id_proof'])) {
                $file2 = $request['front_id_proof'];
                $requestData['front_id_proof_storage_type'] = Storage_Type();
                $requestData['front_id_proof'] = $this->common->saveImage($file2, $this->folder, 'front_proof_', $requestData['front_id_proof_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_front_id_proof']), $request['old_front_id_proof_storage_type']);
            }
            if (isset($request['back_id_proof'])) {
                $file3 = $request['back_id_proof'];
                $requestData['back_id_proof_storage_type'] = Storage_Type();
                $requestData['back_id_proof'] = $this->common->saveImage($file3, $this->folder, 'back_proof_', $requestData['back_id_proof_storage_type']);

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_back_id_proof']), $request['old_back_id_proof_storage_type']);
            }
            unset($requestData['old_image'], $requestData['old_cover_img'], $requestData['old_front_id_proof'], $requestData['old_back_id_proof'], $requestData['old_image_storage_type'], $requestData['old_cover_img_storage_type'], $requestData['old_front_id_proof_storage_type'], $requestData['old_back_id_proof_storage_type']);

            $data = User::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data['id'])) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_user')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_user')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
