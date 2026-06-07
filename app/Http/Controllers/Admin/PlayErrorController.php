<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlayError;
use Exception;
use Illuminate\Http\Request;

class PlayErrorController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = PlayError::latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('user_info', function ($row) {
                        if ($row->user_id) {
                            return 'User #' . $row->user_id;
                        }
                        return 'Guest';
                    })
                    ->addColumn('http_status_badge', function ($row) {
                        $code = (int) $row->http_status;
                        if ($code >= 500) {
                            return '<span class="badge badge-danger">' . $code . '</span>';
                        } elseif ($code >= 400) {
                            return '<span class="badge badge-warning">' . $code . '</span>';
                        }
                        return '<span class="badge badge-secondary">' . ($code ?: '-') . '</span>';
                    })
                    ->addColumn('created_at_fmt', function ($row) {
                        return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                    })
                    ->rawColumns(['http_status_badge'])
                    ->make(true);
            }
            return view('admin.play_errors.index');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
