<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\SupportTicket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    private $common;
    public $page_limit;

    public function __construct()
    {
        $this->common = new Common;
        $this->page_limit = env('PAGE_LIMIT', 20);
    }

    public function submit(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|exists:tbl_user,id',
                'type' => 'required|in:account,billing,playback,content,refund,other',
                'subject' => 'required|max:255',
                'message' => 'required',
            ]);

            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $ticket = SupportTicket::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            return $this->common->API_Response(200, 'Support ticket submitted successfully.', $ticket);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function tickets(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|exists:tbl_user,id',
            ]);

            if ($validation->fails()) {
                return ['status' => 400, 'message' => $validation->errors()->first()];
            }

            $page = (int) ($request->page ?? 1);
            $offset = ($page - 1) * $this->page_limit;

            $query = SupportTicket::where('user_id', $request->user_id);

            $total = $query->count();

            $data = $query->orderBy('id', 'desc')
                ->skip($offset)
                ->take($this->page_limit)
                ->get();

            $pagination = [
                'current_page' => $page,
                'total_record' => $total,
                'total_page' => (int) ceil($total / $this->page_limit),
            ];

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
