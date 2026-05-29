<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Common;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ArtistRequestController extends Controller
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
                $input_search = $request['input_search'];
                $status_filter = $request['status_filter'];

                $data = ArtistRequest::with('user');

                if ($input_search != null && isset($input_search)) {
                    $data->where('artist_name', 'LIKE', "%{$input_search}%");
                }
                if ($status_filter != null && isset($status_filter)) {
                    $data->where('status', $status_filter);
                }

                $data = $data->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('user_info', function ($row) {
                        if ($row->user) {
                            $name = $row->user->full_name ?? '';
                            $email = $row->user->email ?? '';
                            return $name . ' (' . $email . ')';
                        }
                        return 'N/A';
                    })
                    ->addColumn('status_badge', function ($row) {
                        if ($row->status == 'pending') {
                            return '<span class="badge badge-warning">Pending</span>';
                        } elseif ($row->status == 'approved') {
                            return '<span class="badge badge-success">Approved</span>';
                        } else {
                            return '<span class="badge badge-danger">Rejected</span>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        if ($row->status == 'pending') {
                            $btn = '<div class="d-flex justify-content-around">';
                            $btn .= '<a class="edit-delete-btn approve_request" title="Approve" data-id="' . $row->id . '" data-user-id="' . $row->user_id . '" data-artist-name="' . $row->artist_name . '" style="color: green; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-check fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '<a class="edit-delete-btn reject_request" title="Reject" data-id="' . $row->id . '" data-user-id="' . $row->user_id . '" style="color: red; cursor: pointer;">';
                            $btn .= '<i class="fa-solid fa-xmark fa-xl"></i>';
                            $btn .= '</a>';
                            $btn .= '</div>';
                            return $btn;
                        }
                        return '-';
                    })
                    ->rawColumns(['status_badge', 'action', 'user_info'])
                    ->make(true);
            }
            return view('admin.artist_request.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function approve(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $artistReq = ArtistRequest::find($request->request_id);
            if (!$artistReq) {
                return response()->json(array('status' => 400, 'errors' => 'Request not found'));
            }

            $user = User::find($artistReq->user_id);
            if (!$user) {
                return response()->json(array('status' => 400, 'errors' => 'User not found'));
            }

            $existingArtist = Artist::where('user_id', $user->id)->first();
            if ($existingArtist) {
                $artistReq->status = 'approved';
                $artistReq->save();
                $user->role = 'artist';
                $user->save();
                return response()->json(array('status' => 200, 'success' => 'Artist request approved'));
            }

            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $artistReq->artist_name,
                'image' => $user->image ?? '',
                'bio' => $artistReq->bio ?? '',
                'status' => 1,
            ]);

            $user->role = 'artist';
            $user->bio = $artistReq->bio ?? $user->bio;
            $user->save();

            $artistReq->status = 'approved';
            $artistReq->save();

            return response()->json(array('status' => 200, 'success' => 'Artist request approved successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function reject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'request_id' => 'required|numeric',
                'admin_note' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $artistReq = ArtistRequest::find($request->request_id);
            if (!$artistReq) {
                return response()->json(array('status' => 400, 'errors' => 'Request not found'));
            }

            $artistReq->status = 'rejected';
            $artistReq->admin_note = $request->admin_note ?? '';
            $artistReq->save();

            return response()->json(array('status' => 200, 'success' => 'Artist request rejected'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
