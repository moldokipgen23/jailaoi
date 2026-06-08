<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistEarning;
use App\Models\MonetizationApplication;
use App\Models\Music;
use App\Models\Subscriber;
use App\Models\Common;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MonetizationController extends Controller
{
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
                $status_filter = $request['status_filter'];

                $data = MonetizationApplication::with(['artist', 'user']);

                if ($status_filter != null && $status_filter != '' && $status_filter != 'all') {
                    $data->where('status', $status_filter);
                }

                $data = $data->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('artist_name', function ($row) {
                        return $row->artist->name ?? 'N/A';
                    })
                    ->addColumn('user_email', function ($row) {
                        return $row->user->email ?? 'N/A';
                    })
                    ->addColumn('snapshot_plays', function ($row) {
                        return number_format($row->snapshot_plays);
                    })
                    ->addColumn('current_plays', function ($row) {
                        return number_format(ArtistEarning::where('artist_id', $row->artist_id)->count());
                    })
                    ->addColumn('snapshot_followers', function ($row) {
                        return number_format($row->snapshot_followers);
                    })
                    ->addColumn('current_followers', function ($row) {
                        $uid = $row->artist->user_id ?? null;
                        if (!$uid) return 'N/A';
                        return number_format(Subscriber::where('to_user_id', $uid)->where('status', 1)->count());
                    })
                    ->addColumn('snapshot_monthly_plays', function ($row) {
                        return number_format($row->snapshot_monthly_plays);
                    })
                    ->addColumn('current_monthly_plays', function ($row) {
                        return number_format(ArtistEarning::where('artist_id', $row->artist_id)
                            ->where('created_at', '>=', now()->startOfMonth())->count());
                    })
                    ->addColumn('snapshot_tracks', function ($row) {
                        return $row->snapshot_tracks;
                    })
                    ->addColumn('current_tracks', function ($row) {
                        return number_format(Music::where('artist_id', $row->artist_id)->where('status', 1)->count());
                    })
                    ->addColumn('applied_at', function ($row) {
                        return $row->applied_at ? $row->applied_at->format('Y-m-d') : '-';
                    })
                    ->addColumn('status_badge', function ($row) {
                        $map = [
                            'pending'  => ['label' => 'Pending', 'class' => 'badge-warning'],
                            'approved' => ['label' => 'Approved', 'class' => 'badge-success'],
                            'rejected' => ['label' => 'Rejected', 'class' => 'badge-danger'],
                        ];
                        $s = $map[$row->status] ?? ['label' => ucfirst($row->status), 'class' => 'badge-secondary'];
                        return '<span class="badge ' . $s['class'] . '">' . $s['label'] . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        if ($row->status === 'pending') {
                            $btn = '<div class="d-flex justify-content-around">';
                            $btn .= '<a class="edit-delete-btn approve-mono" title="Approve" data-id="' . $row->id . '" style="color: green; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-check fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '<a class="edit-delete-btn reject-mono" title="Reject" data-id="' . $row->id . '" style="color: red; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-xmark fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '</div>';
                            return $btn;
                        }
                        return '<span class="text-muted">—</span>';
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            }
            return view('admin.monetization.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function approve(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'application_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $app = MonetizationApplication::with('user')->find($request->application_id);
            if (!$app) {
                return response()->json(array('status' => 400, 'errors' => 'Application not found'));
            }

            $app->status = 'approved';
            $app->reviewed_at = now();
            $app->save();

            try {
                if ($app->user) {
                    $this->common->SetSmtpConfig();
                    Mail::to($app->user->email)->send(
                        new \App\Mail\MonetizationApprovedMail($app->user->full_name ?? 'there')
                    );
                }
            } catch (Exception $e) {
                Log::error('Monetization approval email failed: ' . $e->getMessage());
            }

            return response()->json(array('status' => 200, 'success' => 'Monetization application approved'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function reject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'application_id' => 'required|numeric',
                'admin_note' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $app = MonetizationApplication::with('user')->find($request->application_id);
            if (!$app) {
                return response()->json(array('status' => 400, 'errors' => 'Application not found'));
            }

            $app->status = 'rejected';
            $app->admin_note = $request->admin_note;
            $app->reviewed_at = now();
            $app->save();

            try {
                if ($app->user) {
                    $this->common->SetSmtpConfig();
                    Mail::to($app->user->email)->send(
                        new \App\Mail\MonetizationRejectedMail(
                            $app->user->full_name ?? 'there',
                            $request->admin_note
                        )
                    );
                }
            } catch (Exception $e) {
                Log::error('Monetization rejection email failed: ' . $e->getMessage());
            }

            return response()->json(array('status' => 200, 'success' => 'Monetization application rejected'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
