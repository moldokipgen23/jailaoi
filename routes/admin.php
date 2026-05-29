<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Admin\AdmobSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\HashtagController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\RentTransactionController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\MusicController;
use App\Http\Controllers\Admin\ReelsController;
use App\Http\Controllers\Admin\PodcastsController;
use App\Http\Controllers\Admin\PlaylistController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\RadioController;
use App\Http\Controllers\Admin\ContentReportController;
use App\Http\Controllers\Admin\RentSectionController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\AdsSettingController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\BadgesBonusController;
use App\Http\Controllers\Admin\CoinPackageController;
use App\Http\Controllers\Admin\CoinTransactionController;
use App\Http\Controllers\Admin\FaceBookAdsSettingController;
use App\Http\Controllers\Admin\FeedCommentController;
use App\Http\Controllers\Admin\FeedController;
use App\Http\Controllers\Admin\FeedReportController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\GiftTransactionController;
use App\Http\Controllers\Admin\PanelSettingController;
use App\Http\Controllers\Admin\ReasonController;
use App\Http\Controllers\Admin\StorageSettingController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\ArtistRequestController;

Route::group(['middleware' => 'installation'], function () {

    // Login-Logout
    Route::get('login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('login', [LoginController::class, 'save_login'])->name('admin.save.login');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    // Chunk
    Route::any('video/saveChunk', [VideoController::class, 'saveChunk']);
    Route::any('music/saveChunk', [MusicController::class, 'saveChunk']);
    Route::any('radio/saveChunk', [RadioController::class, 'saveChunk']);
    Route::any('reels/saveChunk', [ReelsController::class, 'saveChunk']);
    Route::any('ads/saveChunk', [AdsController::class, 'saveChunk']);

    Route::group(['middleware' => 'authadmin', 'as' => 'admin.'], function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/earning', [DashboardController::class, 'earningDashboard'])->name('earning.dashboard');
        // Profile
        Route::resource('profile', ProfileController::class)->only(['index', 'store']);
        Route::post('profile/changepassword', [ProfileController::class, 'ChangePassword'])->name('profile.changepassword');
        // Category
        Route::resource('category', CategoryController::class)->only(['index', 'store', 'update', 'show']);
        Route::post('category/sortorder/save', [CategoryController::class, 'sort_order_save'])->name('category.sortorder.save');
        // Language
        Route::resource('language', LanguageController::class)->only(['index', 'store', 'update', 'show']);
        Route::post('language/sortorder/save', [LanguageController::class, 'sort_order_save'])->name('language.sortorder.save');
        // Hashtag
        Route::resource('hashtag', HashtagController::class)->only(['index', 'store', 'update', 'show']);
        // Pages
        Route::resource('page', PageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        Route::post('page/layout', [PageController::class, 'page_layout'])->name('page.layout.save');
        // Artist
        Route::get('artist', [ArtistController::class, 'index'])->name('artist.index');
        Route::post('artist/store', [ArtistController::class, 'store'])->name('artist.store');
        Route::post('artist/update', [ArtistController::class, 'update'])->name('artist.update');
        Route::delete('artist/{id}', [ArtistController::class, 'destroy'])->name('artist.destroy');
        // Artist Requests
        Route::get('artist-requests', [ArtistRequestController::class, 'index'])->name('artist-requests.index');
        Route::post('artist-requests/approve', [ArtistRequestController::class, 'approve'])->name('artist-requests.approve');
        Route::post('artist-requests/reject', [ArtistRequestController::class, 'reject'])->name('artist-requests.reject');
        // Users
        Route::resource('user', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        Route::post('user/penal/{id}', [UserController::class, 'user_penal_status'])->name('user.penal.status');
        Route::get('user/dashboard/{id}', [UserController::class, 'dashboard'])->name('user.dashboard');
        // Gift
        Route::resource('gift', GiftController::class)->only(['index', 'store', 'update', 'show']);
        // Section
        Route::resource('section', SectionController::class)->only(['index', 'store', 'update', 'show']);
        Route::post('section/data', [SectionController::class, 'GetSectionData'])->name('section.content.data');
        Route::post('section/edit', [SectionController::class, 'SectionDataEdit'])->name('section.content.edit');
        Route::get('section/status/{id}', [SectionController::class, 'SectionStatus'])->name('section.status');
        Route::post('section/sortorder', [SectionController::class, 'sort_order'])->name('section.content.sortorder');
        Route::post('section/sortorder/save', [SectionController::class, 'sort_order_save'])->name('section.content.sortorder.save');
        // Video
        Route::resource('video', VideoController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('videostatus', [VideoController::class, 'changeStatus'])->name('video.status');
        // Music
        Route::resource('music', MusicController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('musicstatus', [MusicController::class, 'changeStatus'])->name('music.status');
        // Reels
        Route::resource('reels', ReelsController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('reelsstatus', [ReelsController::class, 'changeStatus'])->name('reels.status');
        // Podcasts
        Route::resource('podcasts', PodcastsController::class)->only(['index', 'store', 'update', 'show']);
        Route::get('podcasts/episode/{id}', [PodcastsController::class, 'ep_index'])->name('podcast.episode.index');
        Route::get('podcasts/episode/add/{id}', [PodcastsController::class, 'ep_add'])->name('podcast.episode.add');
        Route::post('podcasts/episode/save', [PodcastsController::class, 'ep_save'])->name('podcast.episode.save');
        Route::get('podcasts/episode/edit/{podcasts_id}/{id}', [PodcastsController::class, 'ep_edit'])->name('podcast.episode.edit');
        Route::post('podcasts/episode/update/{podcasts_id}/{id}', [PodcastsController::class, 'ep_update'])->name('podcast.episode.update');
        Route::get('podcasts/episode/status/{id}', [PodcastsController::class, 'ep_status'])->name('podcast.episode.status');
        Route::post('podcasts/episode/sortorder', [PodcastsController::class, 'ep_sort_order'])->name('podcast.episode.sort_order');
        // Playlist
        Route::resource('playlist', PlaylistController::class)->only(['index', 'store', 'update', 'show']);
        Route::get('playlist/content/{id}', [PlaylistController::class, 'pl_index'])->name('playlist.content.index');
        Route::post('playlist/contentdata', [PlaylistController::class, 'pl_content_data'])->name('playlist.get.content');
        Route::post('playlist/save', [PlaylistController::class, 'pl_save'])->name('playlist.content.save');
        Route::post('playlist/delete', [PlaylistController::class, 'pl_delete'])->name('playlist.content.delete');
        Route::post('playlist/sortorder', [PlaylistController::class, 'pl_sort_order'])->name('playlist.content.sort_order');
        // Radio
        Route::resource('radio', RadioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('radiostatus', [RadioController::class, 'changeStatus'])->name('radio.status');
        // Feed
        Route::resource('feed', FeedController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('feedstatus', [FeedController::class, 'changeStatus'])->name('feed.status');
        // Rent Section
        Route::resource('rentsection', RentSectionController::class)->only(['index', 'store', 'update', 'show']);
        Route::post('rentsection/edit', [RentSectionController::class, 'section_edit'])->name('rentsection.content.edit');
        Route::post('rentsection/sortorder/save', [RentSectionController::class, 'section_sort_order'])->name('rentsection.content.sortorder.save');
        // Reason
        Route::resource('reason', ReasonController::class)->only(['index', 'store', 'update', 'show']);
        // Rent Transaction
        Route::resource('renttransaction', RentTransactionController::class)->only(['index', 'create', 'store']);
        Route::any('rentsearchuser', [RentTransactionController::class, 'search_user'])->name('rent.search_user');
        // Package
        Route::resource('package', PackageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        // Transaction
        Route::resource('transaction', TransactionController::class)->only(['index', 'create', 'store']);
        Route::any('searchuser', [TransactionController::class, 'search_user'])->name('search_user');
        // Payment
        Route::resource('payment', PaymentController::class)->only(['index']);
        // Gift Transaction
        Route::resource('gifttransaction', GiftTransactionController::class)->only(['index']);
        // Coin Package
        Route::resource('coinpackage', CoinPackageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        // Coin Transaction
        Route::resource('cointransaction', CoinTransactionController::class)->only(['index', 'create', 'store']);
        Route::any('coinsearch_user', [CoinTransactionController::class, 'search_user'])->name('coin.search_user');
        // App Setting
        Route::get('appsetting', [AppSettingController::class, 'index'])->name('appsetting.index');
        Route::post('appsetting/app', [AppSettingController::class, 'app'])->name('appsetting.app');
        Route::post('appsetting/currency', [AppSettingController::class, 'currency'])->name('appsetting.currency');
        Route::post('appsetting/vapidkey', [AppSettingController::class, 'vapidkey'])->name('appsetting.vapidkey');
        Route::post('appsetting/smtp', [AppSettingController::class, 'smtp'])->name('appsetting.smtp');
        Route::post('appsetting/sociallink', [AppSettingController::class, 'sociallink'])->name('appsetting.sociallink');
        Route::post('appsetting/onboardingscreen', [AppSettingController::class, 'onboardingscreen'])->name('appsetting.onboardingscreen');
        Route::post('appsetting/livestreaming', [AppSettingController::class, 'livestreaming'])->name('appsetting.livestreaming');
        Route::post('appsetting/deepar', [AppSettingController::class, 'deepar'])->name('appsetting.deepar');
        Route::post('appsetting/adscommission', [AppSettingController::class, 'adscommission'])->name('appsetting.adscommission');
        Route::post('appsetting/rentcommission', [AppSettingController::class, 'rentcommission'])->name('appsetting.rentcommission');
        Route::post('appsetting/referearn', [AppSettingController::class, 'referearn'])->name('appsetting.referearn');
        Route::post('appsetting/emailtest', [AppSettingController::class, 'emailtest'])->name('appsetting.emailtest');
        Route::post('appsetting/appdownload', [AppSettingController::class, 'appdownload'])->name('appsetting.appdownload');
        // Panel Setting
        Route::get('panelsetting', [PanelSettingController::class, 'index'])->name('panelsetting.index');
        Route::post('panelsetting/save', [PanelSettingController::class, 'save'])->name('panelsetting.save');
        // Ads Setting
        Route::resource('adssetting', AdsSettingController::class)->only(['index']);
        Route::post('adssetting/bannerads', [AdsSettingController::class, 'bannerads'])->name('adssetting.bannerads');
        Route::post('adssetting/interstitalads', [AdsSettingController::class, 'interstitalads'])->name('adssetting.interstitalads');
        Route::post('adssetting/rewardads', [AdsSettingController::class, 'rewardads'])->name('adssetting.rewardads');
        // Admob
        Route::resource('admob', AdmobSettingController::class)->only(['index']);
        Route::post('admob/status', [AdmobSettingController::class, 'admobStatus'])->name('admob.status');
        Route::post('admob/android', [AdmobSettingController::class, 'admobAndroid'])->name('admob.android');
        Route::post('admob/ios', [AdmobSettingController::class, 'admobIos'])->name('admob.ios');
        // FaceBook Ads
        Route::resource('fbads', FaceBookAdsSettingController::class)->only(['index']);
        Route::post('fbads/status', [FaceBookAdsSettingController::class, 'facebookadStatus'])->name('fbads.status');
        Route::post('fbads/android', [FaceBookAdsSettingController::class, 'facebookadAndroid'])->name('fbads.android');
        Route::post('fbads/ios', [FaceBookAdsSettingController::class, 'facebookadIos'])->name('fbads.ios');
        // Custom Ads
        Route::resource('ads', AdsController::class)->only(['index', 'create', 'store', 'edit', 'show']);
        Route::get('adsstatus', [AdsController::class, 'changeStatus'])->name('ads.status');
        // Comment
        Route::resource('comment', CommentController::class)->only(['index', 'show']);
        // Feed Comment
        Route::resource('feedcomment', FeedCommentController::class)->only(['index', 'show']);
        // Withdrawal
        Route::resource('withdrawal', WithdrawalController::class)->only(['index', 'show']);
        Route::post('withdrawal/minamount', [WithdrawalController::class, 'save_amount'])->name('withdrawal.save.amount');
        // Content Report
        Route::resource('contentreport', ContentReportController::class)->only(['index', 'show']);
        Route::post('contentreport/status', [ContentReportController::class, 'changeStatus'])->name('contentreport.status');
        // Feed Report
        Route::resource('feedreport', FeedReportController::class)->only(['index', 'show']);
        Route::post('feedreport/status', [feedReportController::class, 'changeStatus'])->name('feedreport.status');
        // Badges & Bonus
        Route::resource('badgesbonus', BadgesBonusController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        // System Setting
        Route::get('systemsetting', [SystemSettingController::class, 'index'])->name('systemsetting.index');
        Route::post('systemsetting/cleardata', [SystemSettingController::class, 'ClearData'])->name('systemsetting.cleardata');
        Route::post('systemsetting/cleandatabase', [SystemSettingController::class, 'CleanDatabase'])->name('systemsetting.cleandatabase');
        Route::post('systemsetting/clearinterests', [SystemSettingController::class, 'ClearInterests'])->name('systemsetting.clearinterests');
        // Notification
        Route::resource('notification', NotificationController::class)->only(['index', 'store']);
        Route::get('notifications/setting', [NotificationController::class, 'setting'])->name('notification.setting');
        // Storage Setting
        Route::get('storagesetting', [StorageSettingController::class, 'index'])->name('storagesetting.index');
        Route::post('storagesetting/save', [StorageSettingController::class, 'save'])->name('storagesetting.save');

        Route::group(['middleware' => 'checkadmin'], function () {

            // Category
            Route::resource('category', CategoryController::class)->only(['destroy']);
            // Language
            Route::resource('language', LanguageController::class)->only(['destroy']);
            // Hashtag
            Route::resource('hashtag', HashtagController::class)->only(['destroy']);
            // Page
            Route::resource('page', PageController::class)->only(['destroy']);
            // User
            Route::resource('user', UserController::class)->only(['destroy']);
            // Gift
            Route::resource('gift', GiftController::class)->only(['destroy']);
            // Video
            Route::resource('video', VideoController::class)->only(['show']);
            // Music
            Route::resource('music', MusicController::class)->only(['show']);
            // Reels
            Route::resource('reels', ReelsController::class)->only(['show']);
            // Podcasts
            Route::resource('podcasts', PodcastsController::class)->only(['destroy']);
            Route::get('podcasts/episode/delete/{podcasts_id}/{id}', [PodcastsController::class, 'ep_delete'])->name('podcast.episode.delete');
            // Playlist
            Route::resource('playlist', PlaylistController::class)->only(['destroy']);
            // Radio
            Route::resource('radio', RadioController::class)->only(['show']);
            // Feed
            Route::resource('feed', FeedController::class)->only(['show']);
            // Reason
            Route::resource('reason', ReasonController::class)->only(['destroy']);
            // Rent Transaction
            Route::resource('renttransaction', RentTransactionController::class)->only(['destroy']);
            // Package
            Route::resource('package', PackageController::class)->only(['destroy']);
            // Transaction
            Route::resource('transaction', TransactionController::class)->only(['destroy']);
            // Payment
            Route::resource('payment', PaymentController::class)->only(['edit', 'update']);
            // Gift Transaction
            Route::resource('gifttransaction', GiftTransactionController::class)->only(['destroy']);
            // Coin Package
            Route::resource('coinpackage', CoinPackageController::class)->only(['destroy']);
            // Coin Transaction
            Route::resource('cointransaction', CoinTransactionController::class)->only(['destroy']);
            // Badges & Bonus
            Route::resource('badgesbonus', BadgesBonusController::class)->only(['destroy']);
            // System Settings
            Route::get('systemsetting/downloadsqlfile', [SystemSettingController::class, 'DownloadSqlFile'])->name('systemsetting.downloadsqlfile');
            // Notification
            Route::resource('notification', NotificationController::class)->only(['destroy']);
            Route::post('notifications/setting', [NotificationController::class, 'settingsave'])->name('notification.settingsave');
        });
    });
});
