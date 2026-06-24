<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SupportReplyMail;
use App\Models\Common;
use App\Models\Smtp;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupportTicketController extends Controller
{
    public $common;

    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SupportTicket::with('user')->select('tbl_support_ticket.*');

            if ($request->filled('status')) {
                $data->where('status', $request->status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? ($row->user->full_name ?: $row->user->user_name) : 'Deleted User';
                })
                ->addColumn('type_label', function ($row) {
                    $labels = [
                        'account'  => 'Account & Login',
                        'billing'  => 'Subscription & Billing',
                        'playback' => 'Playback / Technical',
                        'content'  => 'Artist / Content Report',
                        'refund'   => 'Refund Request',
                        'other'    => 'Other',
                    ];
                    return $labels[$row->type] ?? ucfirst($row->type);
                })
                ->addColumn('status_badge', function ($row) {
                    $map    = ['open' => 'warning', 'in_progress' => 'primary', 'resolved' => 'success', 'closed' => 'secondary'];
                    $labels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
                    return '<span class="badge badge-' . ($map[$row->status] ?? 'secondary') . '">' . ($labels[$row->status] ?? $row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.support-tickets.show', $row->id) . '" class="btn btn-sm btn-default"><i class="fa-solid fa-eye"></i> View</a>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.support_ticket.index');
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'replies'])->findOrFail($id);
        return view('admin.support_ticket.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|max:5000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ticket = SupportTicket::with('user')->findOrFail($id);

        if ($ticket->status === 'closed') {
            return redirect()->back()->with('error', 'Cannot reply to a closed ticket.');
        }

        $admin = Auth::guard('admin')->user();

        SupportReply::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'admin',
            'sender_id'   => $admin->id,
            'message'     => $request->message,
        ]);

        $ticket->status     = $request->status ?? 'in_progress';
        $ticket->replied_at = now();
        $ticket->save();

        try {
            if ($ticket->user && $ticket->user->email) {
                $this->common->SetSmtpConfig();
                Mail::to($ticket->user->email)->send(new SupportReplyMail(
                    $ticket->user->full_name ?: $ticket->user->user_name,
                    $ticket->subject,
                    $request->message,
                    route('admin.support-tickets.show', $ticket->id),
                ));
            }
        } catch (Exception $e) {
            Log::error('Admin support reply email failed for ticket #' . $ticket->id . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.support-tickets.show', $ticket->id)
            ->with('success', 'Reply sent successfully.');
    }

    public function changeStatus($id, $status)
    {
        $ticket = SupportTicket::findOrFail($id);

        if (!in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $ticket->status = $status;
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket status updated to ' . ucfirst(str_replace('_', ' ', $status)) . '.');
    }
}
