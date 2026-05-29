<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use App\Models\Feed;
use App\Models\Feed_Content;
use App\Models\Feed_Report;
use Illuminate\Http\Request;
use Exception;

class FeedReportController extends Controller
{
    private $folder = "feed";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            $params['user'] = User::orderby('id', 'desc')->latest()->get();

            $input_user = $request['input_user'];
            $input_report_user = $request['input_report_user'];

            $query = Feed_Report::with('feed', 'user', 'report_user')->orderBy('id', 'DESC');
            if ($input_user != 0) {
                $query->where('user_id', $input_user);
            }
            if ($input_report_user != 0) {
                $query->where('report_user_id', $input_report_user);
            }
            $params['data'] = $query->paginate(18);

            for ($i = 0; $i < count($params['data']); $i++) {

                $feed_content = Feed_Content::where('feed_id', $params['data'][$i]['feed_id'])->first();
                if ($params['data'][$i]['feed'] != null && $feed_content) {

                    $params['data'][$i]['feed']['content_type'] = $feed_content['content_type'];
                    $params['data'][$i]['feed']['image'] = $this->common->getImage($this->folder, $feed_content['image'], $feed_content['image_storage_type']);
                    if ($feed_content['content_type'] == 2) {
                        $params['data'][$i]['feed']['video'] = $this->common->getVideo($this->folder, $feed_content['video'], $feed_content['video_storage_type']);
                    }
                } else {
                    $params['data'][$i]['feed'] = [
                        'content_type' => 0,
                        'image' => asset('assets/imgs/no_img.png'),
                        'video' => "",
                    ];
                }
            }
            return view('admin.feed_report.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            Feed_Report::where('id', $id)->delete();
            return redirect()->route('admin.feedreport.index')->with('success', __('label.feed_report_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus(Request $request)
    {
        try {

            $data = Feed::where('id', $request['id'])->first();
            if ($data) {
                $data['status'] = $data['status'] === 1 ? 0 : 1;
                $data->save();
            }
            return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data['status']]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
