<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $status_filter = $request['status_filter'];
                $data = WithdrawalRequest::with('artist', 'user');
                if ($status_filter) {
                    $data->where('status', $status_filter);
                }
                $data = $data->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('artist_name', fn($row) => $row->artist->name ?? '-')
                    ->addColumn('user_info', function ($row) {
                        if ($row->user) {
                            return ($row->user->full_name ?? '') . ' (' . ($row->user->email ?? '') . ')';
                        }
                        return '-';
                    })
                    ->addColumn('status_badge', function ($row) {
                        $map = [
                            'pending' => 'badge-warning',
                            'approved' => 'badge-info',
                            'rejected' => 'badge-danger',
                            'paid' => 'badge-success',
                        ];
                        $cls = $map[$row->status] ?? 'badge-secondary';
                        return '<span class="badge ' . $cls . '">' . ucfirst($row->status) . '</span>';
                    })
                    ->addColumn('amount_fmt', fn($row) => number_format($row->amount, 2))
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-around">';
                        if ($row->status === 'pending') {
                            $btn .= '<a class="edit-delete-btn approve_withdrawal" title="Approve" data-id="' . $row->id . '" style="color:green;cursor:pointer;"><i class="fa-solid fa-check fa-xl"></i></a>';
                            $btn .= '<a class="edit-delete-btn reject_withdrawal" title="Reject" data-id="' . $row->id . '" style="color:red;cursor:pointer;"><i class="fa-solid fa-xmark fa-xl"></i></a>';
                        } elseif ($row->status === 'approved') {
                            $btn .= '<a class="edit-delete-btn mark_paid" title="Mark Paid" data-id="' . $row->id . '" style="color:green;cursor:pointer;"><i class="fa-solid fa-dollar-sign fa-xl"></i></a>';
                        } else {
                            $btn .= '-';
                        }
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            }
            return view('admin.artist_withdrawals.index');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function approve(Request $request)
    {
        return $this->setStatus($request, 'approved');
    }

    public function reject(Request $request)
    {
        return $this->setStatus($request, 'rejected');
    }

    public function markPaid(Request $request)
    {
        return $this->setStatus($request, 'paid');
    }

    private function setStatus(Request $request, $status)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|numeric',
                'admin_note' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }
            $wr = WithdrawalRequest::find($request->request_id);
            if (!$wr) return response()->json(['status' => 400, 'errors' => 'Request not found']);

            $wr->status = $status;
            if ($request->filled('admin_note')) $wr->admin_note = $request->admin_note;
            $wr->processed_at = now();
            $wr->save();

            // Send email when marked as paid
            if ($status === 'paid') {
                try {
                    $user = $wr->user;
                    if ($user) {
                        $common = new Common;
                        $common->SetSmtpConfig();
                        Mail::to($user->email)->send(
                            new \App\Mail\WithdrawalPaidMail(
                                $user->full_name ?? 'there',
                                $wr->amount,
                                $wr->payment_method ?? 'bank',
                                $wr->id
                            )
                        );
                    }
                } catch (Exception $e) {
                    Log::error('Withdrawal paid email failed: ' . $e->getMessage());
                }
            }

            return response()->json(['status' => 200, 'success' => 'Withdrawal ' . $status]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
