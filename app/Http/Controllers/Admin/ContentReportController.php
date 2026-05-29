<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use App\Models\Content;
use App\Models\Content_Report;
use App\Models\Episode;
use Illuminate\Http\Request;
use Exception;

class ContentReportController extends Controller
{
    private $folder = "content";
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
            $input_type = $request['input_type'];

            $query = Content_Report::with('content', 'user', 'report_user', 'episode')->orderBy('id', 'DESC');
            if ($input_user != 0) {
                $query->where('user_id', $input_user);
            }
            if ($input_report_user != 0) {
                $query->where('report_user_id', $input_report_user);
            }
            if ($input_type != 0) {
                $query->where('content_type', $input_type);
            }
            $params['data'] = $query->paginate(18);

            for ($i = 0; $i < count($params['data']); $i++) {

                if ($params['data'][$i]['content'] != null) {

                    $params['data'][$i]['portrait_img'] = $this->common->getImage($this->folder, $params['data'][$i]['content']['portrait_img'], $params['data'][$i]['content']['portrait_img_storage_type']);
                    if ($params['data'][$i]['content']['content_type'] == 1 || $params['data'][$i]['content']['content_type'] == 2 || $params['data'][$i]['content']['content_type'] == 3 && $params['data'][$i]['content']['content_upload_type'] == 'server_video') {
                        $params['data'][$i]['video'] = $this->common->getVideo($this->folder, $params['data'][$i]['content']['content'], $params['data'][$i]['content']['content_storage_type']);
                    }
                } else {
                    $params['data'][$i]['portrait_img'] = asset('assets/imgs/no_img.png');
                }
                if ($params['data'][$i]['episode'] != null) {

                    $params['data'][$i]['episode_img'] = $this->common->getImage($this->folder, $params['data'][$i]['episode']['portrait_img'], $params['data'][$i]['episode']['portrait_img_storage_type']);
                    if ($params['data'][$i]['episode']['episode_upload_type'] == 'server_audio') {
                        $params['data'][$i]['episode_video'] = $this->common->getVideo($this->folder, $params['data'][$i]['episode']['episode_audio'], $params['data'][$i]['episode']['episode_storage_type']);
                    }
                } else {
                    $params['data'][$i]['episode_img'] = asset('assets/imgs/no_img.png');
                }
            }
            return view('admin.content_report.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            Content_Report::where('id', $id)->delete();
            return redirect()->route('admin.contentreport.index')->with('success', __('label.content_report_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus(Request $request)
    {
        try {

            $status_code = 0;
            $data = Content::where('id', $request['content_id'])->first();
            if ($data) {
                if ($data['content_type'] == 4) {

                    $episode = Episode::where('id', $request['episode_id'])->first();
                    if ($episode) {
                        $episode['status'] = $episode['status'] === 1 ? 0 : 1;
                        $episode->save();

                        $status_code = $episode['status'];
                    }
                } else {

                    $data['status'] = $data['status'] === 1 ? 0 : 1;
                    $data->save();

                    $status_code = $data['status'];
                }
            }

            return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $status_code]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
