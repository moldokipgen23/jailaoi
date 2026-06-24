<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SupportUserReplyMail;
use App\Models\Common;
use App\Models\Smtp;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
                'type'    => 'required|in:account,billing,playback,content,refund,other',
                'subject' => 'required|max:255',
                'message' => 'required',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $ticket = SupportTicket::create([
                'user_id' => $request->user_id,
                'type'    => $request->type,
                'subject' => $request->subject,
                'message' => $request->message,
                'status'  => 'open',
            ]);

            SupportReply::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'user',
                'sender_id'   => $request->user_id,
                'message'     => $request->message,
            ]);

            return $this->common->API_Response(200, 'Support ticket submitted successfully.', $ticket);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function reply(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id'   => 'required|exists:tbl_user,id',
                'ticket_id' => 'required|exists:tbl_support_ticket,id',
                'message'   => 'required',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $ticket = SupportTicket::with('user')->where('id', $request->ticket_id)
                ->where('user_id', $request->user_id)
                ->first();

            if (!$ticket) {
                return $this->common->API_Response(404, 'Ticket not found.', null);
            }

            if ($ticket->status === 'closed') {
                return $this->common->API_Response(400, 'This ticket is closed and cannot receive new replies.', null);
            }

            SupportReply::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'user',
                'sender_id'   => $request->user_id,
                'message'     => $request->message,
            ]);

            if ($ticket->status === 'resolved') {
                $ticket->status = 'open';
                $ticket->save();
            }

            try {
                $this->common->SetSmtpConfig();
                $smtp = Smtp::latest()->first();
                if ($smtp && $smtp->status == 1 && $smtp->from_email) {
                    $userName = $ticket->user ? ($ticket->user->full_name ?: $ticket->user->user_name) : 'User';
                    Mail::to($smtp->from_email)->send(new SupportUserReplyMail(
                        $userName,
                        $ticket->subject,
                        $request->message,
                        $ticket->id,
                    ));
                }
            } catch (Exception $e) {
                // silent
            }

            return $this->common->API_Response(200, 'Reply sent successfully.', null);
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
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $page   = (int) ($request->page ?? 1);
            $offset = ($page - 1) * $this->page_limit;
            $query  = SupportTicket::where('user_id', $request->user_id);
            $total  = $query->count();

            $data = $query->orderBy('id', 'desc')
                ->skip($offset)
                ->take($this->page_limit)
                ->get();

            $pagination = [
                'current_page' => $page,
                'total_record' => $total,
                'total_page'   => (int) ceil($total / $this->page_limit),
            ];

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function thread(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id'   => 'required|exists:tbl_user,id',
                'ticket_id' => 'required|exists:tbl_support_ticket,id',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $ticket = SupportTicket::where('id', $request->ticket_id)
                ->where('user_id', $request->user_id)
                ->with('replies')
                ->first();

            if (!$ticket) {
                return $this->common->API_Response(404, 'Ticket not found.', null);
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $ticket);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
