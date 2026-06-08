<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistEarning;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    // JAILAOI: Admin monetization / earnings overview
    public function earningsOverview()
    {
        try {
            $currency       = General_Setting::where('key', 'payout_currency')->value('value') ?? 'USD';
            $rate           = (float) (General_Setting::where('key', 'payout_rate_per_stream')->value('value') ?? 0);
            $minWithdrawal  = (float) (General_Setting::where('key', 'min_withdrawal_amount')->value('value') ?? 10);

            // Platform-wide totals
            $totalPlays     = ArtistEarning::count();
            $totalEarned    = round((float) ArtistEarning::sum('amount'), 2);
            $totalPaid      = round((float) WithdrawalRequest::where('status', 'paid')->sum('amount'), 2);
            $totalPending   = round((float) WithdrawalRequest::whereIn('status', ['pending', 'approved'])->sum('amount'), 2);
            $totalOwed      = round(max(0, $totalEarned - $totalPaid), 2);
            $pendingCount   = WithdrawalRequest::where('status', 'pending')->count();
            $activeArtists  = ArtistEarning::distinct('artist_id')->count('artist_id');

            // Monthly trend (last 6 months)
            $monthlyRows = DB::table('tbl_artist_earnings')
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                    DB::raw('SUM(amount) as earned'),
                    DB::raw('COUNT(*) as plays')
                )
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $monthlyTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $key   = now()->subMonths($i)->format('Y-m');
                $label = now()->subMonths($i)->format('M Y');
                $monthlyTrend[] = [
                    'label'  => $label,
                    'earned' => round((float) ($monthlyRows[$key]->earned ?? 0), 2),
                    'plays'  => (int) ($monthlyRows[$key]->plays ?? 0),
                ];
            }

            // Per-artist breakdown
            $artistBreakdown = DB::table('tbl_artist_earnings as ae')
                ->join('tbl_artist as a', 'a.id', '=', 'ae.artist_id')
                ->select(
                    'a.id as artist_id',
                    'a.name as artist_name',
                    DB::raw('COUNT(*) as play_count'),
                    DB::raw('SUM(ae.amount) as total_earned')
                )
                ->groupBy('a.id', 'a.name')
                ->orderByDesc('total_earned')
                ->limit(30)
                ->get();

            // Add paid/pending/available per artist
            $wdMap = WithdrawalRequest::select('artist_id', 'status', DB::raw('SUM(amount) as total'))
                ->groupBy('artist_id', 'status')
                ->get()
                ->groupBy('artist_id');

            $artistStats = $artistBreakdown->map(function ($row) use ($wdMap) {
                $earned    = round((float) $row->total_earned, 2);
                $wdRows    = $wdMap[$row->artist_id] ?? collect();
                $paid      = round((float) $wdRows->where('status', 'paid')->sum('total'), 2);
                $pending   = round((float) $wdRows->whereIn('status', ['pending', 'approved'])->sum('total'), 2);
                $available = round(max(0, $earned - $paid - $pending), 2);
                return [
                    'artist_name' => $row->artist_name,
                    'play_count'  => (int) $row->play_count,
                    'total_earned'=> $earned,
                    'paid_out'    => $paid,
                    'pending'     => $pending,
                    'available'   => $available,
                ];
            });

            return view('admin.withdrawal.earnings', compact(
                'currency', 'rate', 'minWithdrawal',
                'totalPlays', 'totalEarned', 'totalPaid', 'totalPending',
                'totalOwed', 'pendingCount', 'activeArtists',
                'monthlyTrend', 'artistStats'
            ));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
