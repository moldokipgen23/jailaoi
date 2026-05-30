<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\User;
use App\Models\General_Setting;
use App\Models\Category;
use App\Models\Coin_Package;
use App\Models\Coin_Transaction;
use App\Models\Content;
use App\Models\Feed;
use App\Models\Gift;
use App\Models\Hashtag;
use App\Models\Language;
use App\Models\Package;
use App\Models\Rent_Transaction;
use App\Models\Subscriber;
use App\Models\Transaction;
use App\Models\Withdrawal_Request;
use Exception;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $folder_content = "content";
    private $folder_user = "user";
    private $folder_category = "category";
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

            // Content toggle settings
            $setting = [];
            $settingRows = General_Setting::whereIn('key', ['video_status', 'reels_status', 'feed_status'])->pluck('value', 'key');
            $videoEnabled = ($settingRows['video_status'] ?? '1') === '1';
            $reelsEnabled = ($settingRows['reels_status'] ?? '1') === '1';
            $feedEnabled = ($settingRows['feed_status'] ?? '1') === '1';

            // Counter Card
            $params['UserCount'] = User::count();
            $params['CategoryCount'] = Category::count();
            $params['LanguageCount'] = Language::count();
            $params['HashtagCount'] = Hashtag::count();
            $params['VideoCount'] = $videoEnabled ? Content::where('content_type', 1)->count() : 0;
            $params['MusicCount'] = Content::where('content_type', 2)->count();
            $params['ReelsCount'] = $reelsEnabled ? Content::where('content_type', 3)->count() : 0;
            $params['PodcastsCount'] = Content::where('content_type', 4)->count();
            $params['FeedCount'] = $feedEnabled ? Feed::count() : 0;
            $params['PlaylistCount'] = Content::where('content_type', 5)->count();
            $params['RadioCount'] = Content::where('content_type', 6)->count();
            $params['GiftCount'] = Gift::count();
            $params['video_enabled'] = $videoEnabled;
            $params['reels_enabled'] = $reelsEnabled;
            $params['feed_enabled'] = $feedEnabled;

            // User Statistice
            $user_data = [];
            $user_month = [];
            $d = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            for ($i = 1; $i < 13; $i++) {
                $Sum = User::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->count();
                $user_data['sum'][] = (int) $Sum;
            }
            for ($i = 1; $i <= $d; $i++) {
                $Sum = User::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->whereDay('created_at', $i)->count();
                $user_month['sum'][] = (int) $Sum;
            }
            $params['user_year'] = json_encode($user_data);
            $params['user_month'] = json_encode($user_month);

            // Most Subscriber
            $params['top_subscriber'] = Subscriber::select('to_user_id', 'to_user_id as user_id', DB::raw('count(*) as total_subscriber'))->groupBy('to_user_id')->orderBy('total_subscriber', 'desc')->with('to_user')->take(5)->get();
            for ($i = 0; $i < count($params['top_subscriber']); $i++) {
                if ($params['top_subscriber'][$i]['to_user'] != null && isset($params['top_subscriber'][$i]['to_user'])) {
                    $this->common->imageNameToUrl(array($params['top_subscriber'][$i]['to_user']), 'image', $this->folder_user, 'image_storage_type');
                }
            }

            // Most Like Content
            $params['top_video_like'] = $videoEnabled ? Content::where('content_type', 1)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get() : [];
            $params['top_music_like'] = Content::where('content_type', 2)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_reels_like'] = $reelsEnabled ? Content::where('content_type', 3)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get() : [];
            $params['top_podcasts_like'] = Content::where('content_type', 4)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $params['top_radio_like'] = Content::where('content_type', 6)->orderBy('total_like', 'desc')->where('status', 1)->take(5)->get();
            $this->common->imageNameToUrl($params['top_video_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_music_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_reels_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_podcasts_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_radio_like'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');

            // Most View Content
            $params['top_video_view'] = $videoEnabled ? Content::where('content_type', 1)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get() : [];
            $params['top_music_view'] = Content::where('content_type', 2)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_reels_view'] = $reelsEnabled ? Content::where('content_type', 3)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get() : [];
            $params['top_podcasts_view'] = Content::where('content_type', 4)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $params['top_radio_view'] = Content::where('content_type', 6)->orderBy('total_view', 'desc')->where('status', 1)->take(5)->get();
            $this->common->imageNameToUrl($params['top_video_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_music_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_reels_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_podcasts_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
            $this->common->imageNameToUrl($params['top_radio_view'], 'portrait_img', $this->folder_content, 'portrait_img_storage_type');

            // Best Category
            $params['best_category'] = Category::orderBy('id', 'desc')->take(8)->get();
            $this->common->imageNameToUrl($params['best_category'], 'image', $this->folder_category);

            // Most Used Hashtag
            $params['most_used_hashtag'] = Hashtag::orderBy('total_used', 'desc')->take(8)->get();

            return view('admin.dashboard.dashboard', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function earningDashboard()
    {
        try {

            // Counter Card
            $params['PackageCount'] = Package::count();
            $params['CoinPackageCount'] = Coin_Package::count();
            $params['TotalMonthRentRevenueCount'] = Rent_Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('price');
            $params['TotalRentRevenueCount'] = Rent_Transaction::whereYear('created_at', date('Y'))->sum('price');
            $params['CurrentMonthCount'] = Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('price');
            $params['CurrentMonthCoinCount'] = Coin_Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('price');
            $params['TotalMonthRentEarningCount'] = Rent_Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('admin_commission');
            $params['TotalRentEarningCount'] = Rent_Transaction::sum('admin_commission');
            $params['TransactionCount'] = Transaction::whereYear('created_at', date('Y'))->sum('price');
            $params['CoinTransactionCount'] = Coin_Transaction::whereYear('created_at', date('Y'))->sum('price');
            $params['PendingWithdrawalCount'] = Withdrawal_Request::where('status', 0)->sum('amount');
            $params['CompletedWithdrawalCount'] = Withdrawal_Request::where('status', 1)->sum('amount');

            // Package Statistice
            $subscription = Package::get();
            $pack_data = [];
            foreach ($subscription as $row) {

                $sum = array();
                for ($i = 1; $i < 13; $i++) {
                    $Sum = Transaction::where('package_id', $row['id'])->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('price');
                    $sum[] = (int) $Sum;
                }
                $pack_data['label'][] = $row['name'];
                $pack_data['sum'][] = $sum;
            }
            $params['package'] = json_encode($pack_data);

            // Rent Earning Statistice
            $rent_data = [];
            for ($i = 1; $i < 13; $i++) {
                $Sum = Rent_Transaction::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('price');
                $rent_data['sum'][] = (int) $Sum;
            }
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[] = date('F', mktime(0, 0, 0, $i, 10));  // Full month names (January, February, ...)
            }
            $params['rent_earning'] = json_encode($rent_data);
            $params['months'] = json_encode($months);

            // Coin Package Statistice
            $coin_subscription = Coin_Package::get();
            $coin_pack_data = [];
            foreach ($coin_subscription as $row) {

                $sum = array();
                for ($i = 1; $i < 13; $i++) {
                    $Sum = Coin_Transaction::where('package_id', $row->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('price');
                    $sum[] = (int) $Sum;
                }
                $coin_pack_data['label'][] = $row->name;
                $coin_pack_data['sum'][] = $sum;
            }
            $params['coin_package'] = json_encode($coin_pack_data);

            // Withdrawal Statistice
            $withdrawal_data['sum'][0] = (int) Withdrawal_Request::whereYear('created_at', date('Y'))->where('status', 0)->sum('amount');
            $withdrawal_data['sum'][1] = (int) Withdrawal_Request::whereYear('created_at', date('Y'))->where('status', 1)->sum('amount');
            $params['withdrawal_earning'] = json_encode($withdrawal_data);

            return view('admin.dashboard.earning_dashboard', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
