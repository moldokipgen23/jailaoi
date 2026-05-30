<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Models\Feed_Comment;
use App\Models\General_Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class FeedCommentController extends Controller
{
    public function index(Request $request)
    {
        if ((General_Setting::where('key', 'feed_status')->value('value') ?? '1') === '0') {
            return redirect()->route('admin.dashboard');
        }
        try {
            $params['data'] = [];
            $params['user'] = User::latest()->get();
            $params['feed'] = Feed::latest()->get();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_user = $request['input_user'];
                $input_feed = $request['input_feed'];

                $query = Feed_Comment::with('user', 'feed');
                if ($input_search) {
                    $query->where('comment', 'LIKE', "%{$input_search}%");
                }
                if ($input_user != 0) {
                    $query->where('user_id', $input_user);
                }
                if ($input_feed != 0) {
                    $query->where('feed_id', $input_feed);
                }
                $data = $query->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        if ($row->status == 1) {
                            $showLabel = __('label.show');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.hide');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->make(true);
            }
            return view('admin.feed_comment.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Feed_Comment::where('id', $id)->first();
            if (isset($data)) {

                $data['status'] = $data['status'] === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data['status']]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
