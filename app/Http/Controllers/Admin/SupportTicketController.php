<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SupportReplyMail;
use App\Models\Common;
use App\Models\SupportTicket;
use Exception;
use Illuminate\Http\Request;
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
                        'account' => 'Account & Login',
                        'billing' => 'Subscription & Billing',
                        'playback' => 'Playback / Technical',
                        'content' => 'Artist / Content Report',
                        'refund' => 'Refund Request',
                        'other' => 'Other',
                    ];
                    return $labels[$row->type] ?? ucfirst($row->type);
                })
                ->addColumn('status_badge', function ($row) {
                    $map = ['open' => 'warning', 'in_progress' => 'primary', 'resolved' => 'success'];
                    $labels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'];
                    $badge = $map[$row->status] ?? 'secondary';
                    return '<span class="badge badge-' . $badge . '">' . ($labels[$row->status] ?? $row->status) . '</span>';
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
        $ticket = SupportTicket::with('user')->findOrFail($id);
        return view('admin.support_ticket.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_reply' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ticket = SupportTicket::with('user')->findOrFail($id);

        $status = $request->status ?? 'in_progress';

        $ticket->admin_reply = $request->admin_reply;
        $ticket->status = $status;
        $ticket->replied_at = now();
        $ticket->save();

        try {
            if ($ticket->user && $ticket->user->email) {
                $this->common->SetSmtpConfig();
                Mail::to($ticket->user->email)->send(new SupportReplyMail(
                    $ticket->user->full_name ?: $ticket->user->user_name,
                    $ticket->subject,
                    $request->admin_reply,
                    route('admin.support-tickets.show', $ticket->id),
                ));
            }
        } catch (Exception $e) {
            // silent
        }

        return redirect()->route('admin.support-tickets.show', $ticket->id)
            ->with('success', 'Reply sent successfully.');
    }

    public function changeStatus($id, $status)
    {
        $ticket = SupportTicket::findOrFail($id);

        if (!in_array($status, ['open', 'in_progress', 'resolved'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $ticket->status = $status;
        $ticket->save();

        return redirect()->back()->with('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $status)) . '.');
    }
}
