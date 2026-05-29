<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Block_Channel;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Content;
use App\Models\Content_Like;
use App\Models\Content_Report;
use App\Models\Content_View;
use App\Models\Episode;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Interests;
use App\Models\Notification;
use App\Models\Playlist_Content;
use App\Models\Read_Notification;
use App\Models\Refer_Earn;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\User_Badges_Bonus;
use App\Models\Watch_later;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Login Type : 1= OTP, 2= Goggle, 3= Apple, 4= Normal
class UserController extends Controller
{
    private $folder = "user";
    private $folder_content = "content";
    private $folder_badges_bonus = "badges_bonus";
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

                $query = User::latest();
                if ($input_search) {
                    $query->where(function ($q) use ($input_search) {
                        $q->where('full_name', 'LIKE', "%{$input_search}%")
                            ->orWhere('channel_name', 'LIKE', "%{$input_search}%")
                            ->orWhere('email', 'LIKE', "%{$input_search}%")
                            ->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                    });
                }
                if ($input_login_type !== 'all') {
                    $query->where('type', $input_login_type);
                }
                if ($input_type == 'today') {
                    $query->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                } elseif ($input_type == 'month') {
                    $query->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                } elseif ($input_type == 'year') {
                    $query->whereYear('created_at', date('Y'));
                }
                $data = $query->get();

                $this->common->imageNameToUrl($data, 'image', $this->folder, 'image_storage_type');

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $user_delete = __('label.delete_user');

                        $delete = '<form onsubmit="return confirm(\'' . $user_delete . '\');" method="POST" action="' . route('admin.user.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a href="' . route('admin.user.dashboard', [$row->id]) . '" class="edit-delete-btn mr-2">';
                        $btn .= '<i class="fa-solid fa-gauge fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= '<a href="' . route('admin.user.edit', [$row->id]) . '" class="edit-delete-btn mr-2">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        return date("Y-m-d", strtotime($row->created_at));
                    })
                    ->addColumn('penal_status', function ($row) {
                        if ($row->user_penal_status == 1) {
                            $showLabel = __('label.on');
                            return "<button type='button' id='panel_add_{$row->id}' onclick='change_panel_status($row->id)' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.off');
                            return "<button type='button' id='panel_add_{$row->id}' onclick='change_panel_status($row->id)' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            $showLabel = __('label.active');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.inactive');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->rawColumns(['action', 'penal_status', 'status'])
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
            $rules = [
                'channel_name' => 'required|min:2|unique:tbl_user,channel_name',
                'description' => 'required',
                'full_name' => 'required|min:2',
                'email' => 'required|unique:tbl_user|email',
                'password' => 'required|min:4',
                'country_code' => 'required',
                'country_name' => 'required',
                'mobile_number' => [
                    'required',
                    'numeric',
                    Rule::unique('tbl_user')->where(function ($query) use ($request) {
                        return $query->where('country_code', $request->country_code)
                            ->where('mobile_number', $request->mobile_number);
                    }),
                ],
                'push_notification_status' => 'required',
                'send_mail_status' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
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
                $rules['front_id_proof'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
                $rules['back_id_proof'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();

            $storage_type = Storage_Type();
            $requestData['image_storage_type'] = $storage_type;
            $requestData['cover_img_storage_type'] = $storage_type;
            $requestData['front_id_proof_storage_type'] = $storage_type;
            $requestData['back_id_proof_storage_type'] = $storage_type;

            $requestData['channel_id'] = Str::random(8);
            $requestData['password'] = Hash::make($requestData['password']);
            $file = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'img_', $requestData['image_storage_type']);
            $requestData['cover_img'] = '';
            if (isset($request['cover_img'])) {
                $file1 = $request['cover_img'];
                $requestData['cover_img'] = $this->common->saveImage($file1, $this->folder, 'cover_img_', $requestData['cover_img_storage_type']);
            }
            $requestData['type'] = 4;
            $requestData['device_type'] = 0;
            $requestData['device_token'] = "";
            $requestData['website'] = $request['website'] ?? '';
            $requestData['facebook_url'] = $request['facebook_url'] ?? '';
            $requestData['instagram_url'] = $request['instagram_url'] ?? '';
            $requestData['twitter_url'] = $request['twitter_url'] ?? '';
            $requestData['wallet_balance'] = 0;
            $requestData['wallet_earning'] = 0;
            $requestData['bank_name'] = $request['bank_name'] ?? '';
            $requestData['bank_code'] = $request['bank_code'] ?? '';
            $requestData['bank_address'] = $request['bank_address'] ?? '';
            $requestData['ifsc_no'] = $request['ifsc_no'] ?? '';
            $requestData['account_no'] = $request['account_no'] ?? '';
            if (isset($request['front_id_proof'])) {
                $file2 = $request['front_id_proof'];
                $requestData['front_id_proof'] = $this->common->saveImage($file2, $this->folder, 'front_proof_', $requestData['front_id_proof_storage_type']);
            } else {
                $requestData['front_id_proof'] = "";
            }
            if (isset($request['back_id_proof'])) {
                $file3 = $request['back_id_proof'];
                $requestData['back_id_proof'] = $this->common->saveImage($file3, $this->folder, 'back_proof_', $requestData['back_id_proof_storage_type']);
            } else {
                $requestData['back_id_proof'] = "";
            }
            $requestData['user_penal_status'] = 0;
            $requestData['reference_code'] = $this->common->generateReferenceCode(8);
            $requestData['status'] = 1;

            $user_data = User::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($user_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_user')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_user')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = User::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['data']['image'] = $this->common->getImage($this->folder, $params['data']['image'], $params['data']['image_storage_type']);
                $params['data']['cover_img'] = $this->common->getImage($this->folder, $params['data']['cover_img'], $params['data']['cover_img_storage_type']);
                $params['data']['front_id_proof'] = $this->common->getImage($this->folder, $params['data']['front_id_proof'], $params['data']['front_id_proof_storage_type']);
                $params['data']['back_id_proof'] = $this->common->getImage($this->folder, $params['data']['back_id_proof'], $params['data']['back_id_proof_storage_type']);

                return view('admin.user.edit', $params);
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
                'cover_img' => 'mimes:jpeg,png,jpg|max:5120',
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

            $requestData['password'] = isset($request['password']) ? Hash::make($request['password']) : '';
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
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_user')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_edit_user')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = User::where('id', $id)->first();
            if (isset($data)) {

                $data['status'] = $data['status'] === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data->status]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function user_penal_status($id)
    {
        try {

            $data = User::where('id', $id)->first();
            if (isset($data)) {

                $data['user_penal_status'] = $data['user_penal_status'] === 1 ? 0 : 1;
                $data->save();

                // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                $this->common->Send_Mail(4, $data['email']);
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data->user_penal_status]);
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

            $data = User::where('id', $id)->first();
            if (isset($data)) {

                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['image_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['cover_img'], $data['cover_img_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['front_id_proof'], $data['front_id_proof_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['back_id_proof'], $data['back_id_proof_storage_type']);
                $data->delete();

                // Releted Data Delete
                Ads::where('user_id', $id)->delete();
                User_Badges_Bonus::where('user_id', $id)->delete();
                Block_Channel::where('user_id', $id)->delete();
                Block_Channel::where('block_user_id', $id)->delete();
                Subscriber::where('user_id', $id)->delete();
                Subscriber::where('to_user_id', $id)->delete();
                History::where('user_id', $id)->delete();
                Interests::where('user_id', $id)->delete();
                Notification::where('from_user_id', $id)->delete();
                Read_Notification::where('user_id', $id)->delete();
                Watch_later::where('user_id', $id)->delete();
                $this->deleteChannelContent($data['channel_id']);
            }
            return redirect()->route('admin.user.index')->with('success', __('label.user_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function dashboard($id, Request $request)
    {
        try {

            $params['data'] = User::where('id', $id)->first();
            if ($params['data'] != null) {

                $params['id'] = $id;
                $params['data']['image'] = $this->common->getImage($this->folder, $params['data']['image'], $params['data']['image_storage_type']);
                $params['data']['cover_img'] = $this->common->getImage($this->folder, $params['data']['cover_img'], $params['data']['cover_img_storage_type']);
                $params['data']['total_subscriber'] = $this->common->total_subscriber($id);

                $params['badges'] = User_Badges_Bonus::where('user_id', $id)->with('badges_bonus')->latest()->get();
                for ($i = 0; $i < count($params['badges']); $i++) {

                    if ($params['badges'][$i]['badges_bonus'] != null) {
                        $params['badges'][$i]['badges_bonus']['image'] = $this->common->getImage($this->folder_badges_bonus, $params['badges'][$i]['badges_bonus']['image'], $params['badges'][$i]['badges_bonus']['storage_type']);
                    }
                }

                $params['parent_user'] = Refer_Earn::where('child_user_id', $id)->with('parent_user')->latest()->get();
                for ($i = 0; $i < count($params['parent_user']); $i++) {

                    if ($params['parent_user'][$i]['parent_user'] != null) {
                        $params['parent_user'][$i]['parent_user']['image'] = $this->common->getImage($this->folder, $params['parent_user'][$i]['parent_user']['image'], $params['parent_user'][$i]['parent_user']['image_storage_type']);
                    }
                }
                $params['child_user'] = Refer_Earn::where('parent_user_id', $id)->with('child_user')->latest()->get();
                for ($i = 0; $i < count($params['child_user']); $i++) {

                    if ($params['child_user'][$i]['child_user'] != null) {
                        $params['child_user'][$i]['child_user']['image'] = $this->common->getImage($this->folder, $params['child_user'][$i]['child_user']['image'], $params['parent_user'][$i]['parent_user']['image_storage_type']);
                    }
                }

                return view('admin.user.dashboard', $params);
            } else {
                return redirect()->back()->with('error', __('label.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // Channel Content Delete
    public function deleteChannelContent($channelId)
    {

        $contents = Content::where('channel_id', $channelId)->get();
        foreach ($contents as $content) {
            $contentId = $content['id'];

            if (in_array($content->content_type, [1, 2])) {
                $oldHashtags = explode(',', $content->hashtag_id);
                Hashtag::whereIn('id', $oldHashtags)->decrement('total_used', 1);
            }
            $this->common->deleteImageToFolder($this->folder_content, $content['portrait_img'], $content['portrait_img_storage_type']);
            $this->common->deleteImageToFolder($this->folder_content, $content['landscape_img'], $content['landscape_img_storage_type']);
            $this->common->deleteImageToFolder($this->folder_content, $content['content'], $content['content_storage_type']);

            if ($content->content_type == 4) {

                $episodes = Episode::where('podcasts_id', $contentId)->get();
                foreach ($episodes as $episode) {
                    $this->common->deleteImageToFolder($this->folder_content, $episode['portrait_img'], $episode['portrait_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder_content, $episode['landscape_img'], $episode['landscape_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder_content, $episode['episode_audio'], $episode['episode_storage_type']);
                    $episode->delete();
                }
            }
            if ($content->content_type == 5) {
                Playlist_Content::where('channel_id', $channelId)->delete();
            }

            // Delete Related Data
            Comment::where('content_id', $contentId)->delete();
            Content_Report::where('content_id', $contentId)->delete();
            History::where('content_id', $contentId)->delete();
            Content_Like::where('content_id', $contentId)->delete();
            Notification::where('content_id', $contentId)->delete();
            Content_View::where('content_id', $contentId)->delete();
            Watch_later::where('content_id', $contentId)->delete();

            $content->delete();
        }
    }
}
