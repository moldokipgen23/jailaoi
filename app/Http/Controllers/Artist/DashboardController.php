<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Common;
use App\Models\Content;
use App\Models\Subscriber;
use Exception;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $folder_content = "content";
    private $folder_artist = "artist";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            $user = Auth::guard('artist')->user();
            $artist = Artist::where('user_id', $user->id)->first();

            $params['total_content'] = Content::where('channel_id', $user->channel_id)->count();
            $params['total_video'] = Content::where('channel_id', $user->channel_id)->where('content_type', 1)->count();
            $params['total_music'] = Content::where('channel_id', $user->channel_id)->where('content_type', 2)->count();
            $params['total_followers'] = Subscriber::where('to_user_id', $user->id)->count();
            $params['total_views'] = Content::where('channel_id', $user->channel_id)->sum('total_view');
            $params['total_likes'] = Content::where('channel_id', $user->channel_id)->sum('total_like');
            $params['user'] = $user;
            $params['artist'] = $artist;
            $params['recent_content'] = Content::where('channel_id', $user->channel_id)->latest()->take(5)->get();

            $this->common->imageNameToUrl($params['recent_content'], 'portrait_img', $this->folder_content);
            $this->common->imageNameToUrl($params['recent_content'], 'landscape_img', $this->folder_content);

            return view('artist.dashboard.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
