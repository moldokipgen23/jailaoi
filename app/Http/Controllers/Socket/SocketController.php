<?php

namespace App\Http\Controllers\Socket;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Gift;
use App\Models\Live_History;
use App\Models\Live_User;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class SocketController extends Controller
{
    private $folder = "user";
    private $folder_gift = "gift";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    // addlivehistory
    public function addLiveHistory(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request->user_id;
            $roomId = $request->room_id;

            // Delete Data
            Live_User::where('user_id', $userId)->delete();

            // Insert Live History
            $addHistory = new Live_History();
            $addHistory['room_id'] = $roomId;
            $addHistory['user_id'] = $userId;
            $addHistory['total_gift'] = 0;
            $addHistory['total_join_user'] = 0;
            $addHistory['total_live_chat'] = 0;
            $addHistory['start_time'] = date('Y-m-d H:i:s');
            $addHistory['end_time'] = "";
            $addHistory['duration'] = 0;
            $addHistory['status'] = 1;
            $addHistory->save();

            // Insert Live User
            $addUser = new Live_User();
            $addUser['room_id'] = $roomId;
            $addUser['user_id'] = $userId;
            $addUser['total_view'] = 0;
            $addUser['status'] = 1;
            $addUser->save();

            // Send Notification
            $this->common->goLiveSendNotification($userId, $roomId);

            return $this->common->API_Response(200, __('api_msg.data_added'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // endlive
    public function endLive(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request->user_id;
            $roomId = $request->room_id;

            // Delete Live User
            Live_User::where('user_id', $userId)->delete();

            // Update Live History
            $data = Live_History::where('user_id', $userId)->where('room_id', $roomId)->latest()->first();
            if (isset($data)) {

                $data['end_time'] = date('Y-m-d H:i:s');
                $data['duration'] = strtotime($data['end_time']) - strtotime($data['start_time']);
                $data->save();
            }
            return $this->common->API_Response(200, __('api_msg.status_updated'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // addView
    public function addView(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request->user_id;
            $roomId = $request->room_id;

            // Update Live User & History
            Live_User::where('room_id', $roomId)->latest()->increment('total_view');
            Live_History::where('room_id', $roomId)->latest()->increment('total_join_user');

            // Retrieve the updated live count for response
            $updatedHistory = Live_User::where('room_id', $roomId)->latest()->first();

            return $this->common->API_Response(200, __('api_msg.data_added'), ['live_count' => $updatedHistory['total_view'] ?? 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // lessView
    public function lessView(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request->user_id;
            $roomId = $request->room_id;

            // Update Live User
            Live_User::where('room_id', $roomId)->latest()->decrement('total_view');

            // Retrieve the updated live count for response
            $updatedHistory = Live_User::where('room_id', $roomId)->latest()->first();

            return $this->common->API_Response(200, __('api_msg.data_added'), ['live_count' => $updatedHistory['total_view'] ?? 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // liveChat
    public function liveChat(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
                'comment' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request['user_id'];
            $roomId = $request['room_id'];
            $comment = $request['comment'];

            // Update Live History
            Live_History::where('room_id', $roomId)->latest()->increment('total_live_chat');

            $user = User::where('id', $userId)->latest()->first();
            if (isset($user)) {

                $this->common->imageNameToUrl(array($user), 'image', $this->folder);

                $data['user_name'] = $user['channel_name'];
                $data['full_name'] = $user['full_name'];
                $data['image'] = $user['image'];
                $data['comment'] = $comment;

                return $this->common->API_Response(200, __('api_msg.data_added'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    // sendGift
    public function sendGift(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'room_id' => 'required',
                'gift_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $userId = $request->user_id;
            $roomId = $request->room_id;
            $giftId = $request->gift_id;

            // Update Live History
            Live_History::where('room_id', $roomId)->latest()->increment('total_gift');

            // Gift
            $gift = Gift::where('id', $giftId)->first();
            $user = User::where('id', $userId)->first();
            $live_user = Live_User::where('room_id', $roomId)->first();

            if (isset($gift) && isset($user) && isset($live_user)) {

                User::where('id', $userId)->decrement('wallet_balance', $gift['price']);
                User::where('id', $live_user['user_id'])->increment('wallet_balance', $gift['price']);

                $this->common->imageNameToUrl(array($gift), 'image', $this->folder_gift, $gift['storage_type']);

                $data['name'] = $gift['name'];
                $data['image'] = $gift['image'];

                return $this->common->API_Response(200, __('api_msg.data_added'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
