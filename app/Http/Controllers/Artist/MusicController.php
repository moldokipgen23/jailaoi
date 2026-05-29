<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\Auth;

class MusicController extends Controller
{
    private $folder_content = "content";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            $user = Auth::guard('artist')->user();
            $params['music'] = Content::where('channel_id', $user->channel_id)
                ->where('content_type', 2)
                ->latest()
                ->get();
            $this->common->imageNameToUrl($params['music'], 'portrait_img', $this->folder_content);
            $this->common->imageNameToUrl($params['music'], 'landscape_img', $this->folder_content);
            return view('artist.music.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('artist')->user();
            $content = Content::where('id', $id)->where('channel_id', $user->channel_id)->first();
            if ($content) {
                $content->delete();
                return response()->json(['status' => 200, 'success' => __('label.delete_successfully')]);
            }
            return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
