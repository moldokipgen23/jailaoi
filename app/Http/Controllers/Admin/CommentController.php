<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            $params['user'] = User::latest()->get();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_user = $request['input_user'];
                $input_type = $request['input_type'];

                $query = Comment::with(['user']);

                if (!empty($input_search)) {
                    $query->where('comment', 'LIKE', "%{$input_search}%");
                }

                if (!empty($input_type)) {
                    $query->where('type', $input_type);
                }

                if (!empty($input_user)) {
                    $query->where('user_id', $input_user);
                }

                $data = $query->latest()->get();
                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $status = $row->status == 1 ? "checked" : "";
                        return '<div class="switch">
                                    <input class="status-checkbox" id="checkbox' . $row->id . '" data-id="' . $row->id . '" type="checkbox" ' . $status . '>
                                    <label for="checkbox' . $row->id . '"></label>
                                      <span class="toggle-text"
                                        data-on="' . __('label.show') . '"
                                        data-off="' . __('label.hide') . '"></span>
                                    </div>';
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("d M Y", strtotime($row->created_at));
                        return $date;
                    })
                    ->addColumn('user', function ($row) {
                        return $row->user?->full_name ?? '-';
                    })
                    ->make(true);
            }
            return view('admin.comment.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Comment::where('id', $id)->first();
            if ($data) {
                $data->status = $data->status == 1 ? 0 : 1;
                $data->save();

                return response()->json(['status' => 200, 'success' => __('label.status_changed')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
