<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Content;
use App\Models\Subscriber;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            $user = Auth::guard('artist')->user();

            $params['total_views'] = Content::where('channel_id', $user->channel_id)->sum('total_view');
            $params['total_likes'] = Content::where('channel_id', $user->channel_id)->sum('total_like');
            $params['total_followers'] = Subscriber::where('to_user_id', $user->id)->count();
            $params['total_content'] = Content::where('channel_id', $user->channel_id)->count();

            $params['views_by_type'] = Content::select('content_type', DB::raw('SUM(total_view) as total'))
                ->where('channel_id', $user->channel_id)
                ->groupBy('content_type')
                ->get();

            $params['recent_views'] = Content::where('channel_id', $user->channel_id)
                ->where('total_view', '>', 0)
                ->orderBy('total_view', 'desc')
                ->take(10)
                ->get();

            return view('artist.analytics.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
