<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistKyc;
use App\Models\Common;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    public $common;
    private $folder_kyc = "kyc";

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

                $data = ArtistKyc::with(['artist', 'user']);

                if ($status_filter != null && $status_filter != '' && $status_filter != 'all') {
                    $data->where('status', $status_filter);
                }

                $data = $data->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('artist_name', function ($row) {
                        return $row->artist->name ?? 'N/A';
                    })
                    ->addColumn('full_name', function ($row) {
                        return $row->legal_first_name . ' ' . $row->legal_last_name;
                    })
                    ->addColumn('submitted_date', function ($row) {
                        return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
                    })
                    ->addColumn('status_badge', function ($row) {
                        $map = [
                            'not_started'  => ['label' => 'Not Started', 'class' => 'badge-secondary'],
                            'submitted'    => ['label' => 'Submitted', 'class' => 'badge-info'],
                            'under_review' => ['label' => 'Under Review', 'class' => 'badge-warning'],
                            'approved'     => ['label' => 'Approved', 'class' => 'badge-success'],
                            'rejected'     => ['label' => 'Rejected', 'class' => 'badge-danger'],
                        ];
                        $s = $map[$row->status] ?? ['label' => ucfirst($row->status), 'class' => 'badge-secondary'];
                        return '<span class="badge ' . $s['class'] . '">' . $s['label'] . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        if (in_array($row->status, ['submitted', 'under_review'])) {
                            $btn = '<div class="d-flex justify-content-around">';
                            $btn .= '<a class="edit-delete-btn view-kyc" title="View" data-id="' . $row->id . '" style="color: #3b82f6; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-eye fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '<a class="edit-delete-btn approve-kyc" title="Approve" data-id="' . $row->id . '" style="color: green; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-check fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '<a class="edit-delete-btn reject-kyc" title="Reject" data-id="' . $row->id . '" style="color: red; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-xmark fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '</div>';
                            return $btn;
                        }
                        $btn = '<a class="edit-delete-btn view-kyc" title="View" data-id="' . $row->id . '" style="color: #3b82f6; cursor: pointer;">';
                        $btn .= '<i class="fa-solid fa-eye fa-xl"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            }
            return view('admin.kyc.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function view($id)
    {
        try {
            $kyc = ArtistKyc::with(['artist', 'user'])->findOrFail($id);

            $kyc->id_front_img_url = $this->common->getImage($this->folder_kyc, $kyc->id_front_img);
            $kyc->id_back_img_url  = $this->common->getImage($this->folder_kyc, $kyc->id_back_img);

            $details = json_decode($kyc->payment_details, true);
            $kyc->payment_details_display = $details ?: ['raw' => $kyc->payment_details];

            return response()->json(['status' => 200, 'data' => $kyc]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function approve(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kyc_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $kyc = ArtistKyc::with('user')->find($request->kyc_id);
            if (!$kyc) {
                return response()->json(array('status' => 400, 'errors' => 'KYC record not found'));
            }

            $kyc->status = 'approved';
            $kyc->reviewed_at = now();
            $kyc->save();

            // Send approval email
            try {
                if ($kyc->user) {
                    $this->common->SetSmtpConfig();
                    Mail::to($kyc->user->email)->send(
                        new \App\Mail\KycApprovedMail($kyc->user->full_name ?? 'there')
                    );
                }
            } catch (Exception $e) {
                Log::error('KYC approval email failed: ' . $e->getMessage());
            }

            return response()->json(array('status' => 200, 'success' => 'KYC approved successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function reject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kyc_id' => 'required|numeric',
                'admin_note' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $kyc = ArtistKyc::with('user')->find($request->kyc_id);
            if (!$kyc) {
                return response()->json(array('status' => 400, 'errors' => 'KYC record not found'));
            }

            $kyc->status = 'rejected';
            $kyc->admin_note = $request->admin_note;
            $kyc->reviewed_at = now();
            $kyc->save();

            // Send rejection email
            try {
                if ($kyc->user) {
                    $this->common->SetSmtpConfig();
                    Mail::to($kyc->user->email)->send(
                        new \App\Mail\KycRejectedMail(
                            $kyc->user->full_name ?? 'there',
                            $request->admin_note
                        )
                    );
                }
            } catch (Exception $e) {
                Log::error('KYC rejection email failed: ' . $e->getMessage());
            }

            return response()->json(array('status' => 200, 'success' => 'KYC rejected'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
