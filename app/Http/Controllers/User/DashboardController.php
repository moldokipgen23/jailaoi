<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Content;
use Exception;
use App\Models\Common;
use App\Models\Feed;
use App\Models\Refer_Earn;
use App\Models\User_Badges_Bonus;

class DashboardController extends Controller
{
    private $folder_user = "user";
    private $folder_content = "content";
    private $folder_badges_bonus = "badges_bonus";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            // Expiry
            $this->common->package_expiry();
            $this->common->rent_expiry();

            $user = User_Data();
            $channel_id = $user['channel_id'];

            $params['data'] = $user;
            $params['data']['image'] = $this->common->getImage($this->folder_user, $params['data']['image'], $params['data']['image_storage_type']);
            $params['data']['cover_img'] = $this->common->getImage($this->folder_user, $params['data']['cover_img'], $params['data']['cover_img_storage_type']);
            $params['data']['total_subscriber'] = $this->common->total_subscriber($user['id']);

            $params['badges'] = User_Badges_Bonus::where('user_id', $user['id'])->with('badges_bonus')->latest()->get();
            for ($i = 0; $i < count($params['badges']); $i++) {

                if ($params['badges'][$i]['badges_bonus'] != null) {
                    $params['badges'][$i]['badges_bonus']['image'] = $this->common->getImage($this->folder_badges_bonus, $params['badges'][$i]['badges_bonus']['image'], $params['badges'][$i]['badges_bonus']['storage_type']);
                }
            }

            $params['parent_user'] = Refer_Earn::where('child_user_id', $user['id'])->with('parent_user')->latest()->get();
            for ($i = 0; $i < count($params['parent_user']); $i++) {

                if ($params['parent_user'][$i]['parent_user'] != null) {
                    $params['parent_user'][$i]['parent_user']['image'] = $this->common->getImage($this->folder_user, $params['parent_user'][$i]['parent_user']['image'], $params['parent_user'][$i]['parent_user']['image_storage_type']);
                }
            }
            $params['child_user'] = Refer_Earn::where('parent_user_id', $user['id'])->with('child_user')->latest()->get();
            for ($i = 0; $i < count($params['child_user']); $i++) {

                if ($params['child_user'][$i]['child_user'] != null) {
                    $params['child_user'][$i]['child_user']['image'] = $this->common->getImage($this->folder_user, $params['child_user'][$i]['child_user']['image'], $params['parent_user'][$i]['parent_user']['image_storage_type']);
                }
            }

            // Counter Card
            $params['VideoCount'] = Content::where('channel_id', $channel_id)->where('content_type', 1)->count();
            $params['MusicCount'] = Content::where('channel_id', $channel_id)->where('content_type', 2)->count();
            $params['ReelsCount'] = Content::where('channel_id', $channel_id)->where('content_type', 3)->count();
            $params['PodcastsCount'] = Content::where('channel_id', $channel_id)->where('content_type', 4)->count();
            $params['PlaylistCount'] = Content::where('channel_id', $channel_id)->where('content_type', 5)->count();
            $params['RadioCount'] = Content::where('channel_id', $channel_id)->where('content_type', 6)->count();
            $params['FeedCount'] = Feed::where('channel_id', $channel_id)->count();
            $params['AdsCount'] = Ads::where('user_id', $user['id'])->count();

            // Most Like Content
            $params['top_video_like'] = Content::where('channel_id', $channel_id)->where('content_type', 1)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_music_like'] = Content::where('channel_id', $channel_id)->where('content_type', 2)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_reels_like'] = Content::where('channel_id', $channel_id)->where('content_type', 3)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_podcasts_like'] = Content::where('channel_id', $channel_id)->where('content_type', 4)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_radio_like'] = Content::where('channel_id', $channel_id)->where('content_type', 6)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $this->common->imageNameToUrl($params['top_video_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_music_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_reels_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_podcasts_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_radio_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');

            // Most View Content
            $params['top_video_view'] = Content::where('channel_id', $channel_id)->where('content_type', 1)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_music_view'] = Content::where('channel_id', $channel_id)->where('content_type', 2)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_reels_view'] = Content::where('channel_id', $channel_id)->where('content_type', 3)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_podcasts_view'] = Content::where('channel_id', $channel_id)->where('content_type', 4)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_radio_view'] = Content::where('channel_id', $channel_id)->where('content_type', 6)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $this->common->imageNameToUrl($params['top_video_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_music_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_reels_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_podcasts_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_radio_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');

            return view('user.dashboard.dashboard', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
