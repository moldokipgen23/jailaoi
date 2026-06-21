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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\AdmobSettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\SongController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\FaceBookAdsSettingController;
use App\Http\Controllers\Admin\LiveEventController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MusicController;
use App\Http\Controllers\Admin\NotificationConfigurationController;
use App\Http\Controllers\Admin\PanelSettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\ArtistRequestController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\MonetizationController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\PlayErrorController;

// Artisan
Route::get('artisan', function () {

    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "<h1>All Config Cache Clear Successfully.</h1>";
});

// Version
Route::get('version', function () {
    return "<h1>
        <li>PHP : " . phpversion() . "</li>
        <li>Laravel : " . app()->version() . "</li>
    </h1>";
});

Route::group(['middleware' => 'installation'], function () {

    // Login-Logout
    Route::get('login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('login', [LoginController::class, 'save_login'])->name('admin.save.login');
    Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    // Chunk
    Route::any('song/saveChunk', [SongController::class, 'saveChunk']);
    Route::any('podcasts/saveChunk', [PodcastController::class, 'saveChunk']);
    Route::any('podcasts/episode/saveChunk', [PodcastController::class, 'saveChunkEpisode']);
    Route::any('music/saveChunk', [MusicController::class, 'saveChunk']);

    Route::group(['middleware' => 'authadmin'], function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Profile
        Route::resource('profile', ProfileController::class)->only(['index']);
        // Category
        Route::resource('category', CategoryController::class)->only(['index', 'show']);
        Route::post('category/sortable/save', [CategoryController::class, 'CategorySortableSave'])->name('category.sortable.save');
        // Language
        Route::resource('language', LanguageController::class)->only(['index', 'show']);
        Route::post('language/sortable/save', [LanguageController::class, 'LanguageSortableSave'])->name('language.sortable.save');
        // City
        Route::resource('city', CityController::class)->only(['index', 'show']);
        Route::post('city/sortable/save', [CityController::class, 'CitySortableSave'])->name('city.sortable.save');
        // Pages
        Route::resource('page', PageController::class)->only(['index', 'create', 'store', 'edit', 'show']);
        Route::post('page/save_setting', [PageController::class, 'save_setting'])->name('page.save_setting');
        // Artist/RJ
        Route::resource('artist', ArtistController::class)->only(['index', 'show']);
        Route::get('artist/detail/{id}', [ArtistController::class, 'detail'])->name('artist.detail');
        Route::post('artist/sortable/save', [ArtistController::class, 'ArtistSortableSave'])->name('artist.sortable.save');
        // User
        Route::resource('user', UserController::class)->only(['index', 'create', 'edit']);
        // Section
        Route::resource('section', SectionController::class)->only(['index', 'store', 'show', 'update']);
        Route::post('section/content', [SectionController::class, 'GetSectionData'])->name('section.data');
        Route::post('section/edit', [SectionController::class, 'SectionDataEdit'])->name('section.edit');
        Route::post('section/status', [SectionController::class, 'changeStatus'])->name('section.status');
        Route::post('section/sortable', [SectionController::class, 'SectionSortable'])->name('section.sortable');
        Route::post('section/sortable/save', [SectionController::class, 'SectionSortableSave'])->name('section.sortable.save');
        // Banner
        Route::resource('banner', BannerController::class)->only(['index', 'store', 'show']);
        Route::get('getcontent', [BannerController::class, 'getcontent'])->name('banner.getcontent');
        // Song
        Route::resource('song', SongController::class)->only(['index', 'create', 'edit']);
        Route::get('song_status/{id}', [SongController::class, 'changeStatus'])->name('song.status');
        // Podcasts
        Route::resource('podcast', PodcastController::class)->only(['index', 'show']);
        Route::get('podcastepisode/{id}', [PodcastController::class, 'PodcastIndex'])->name('podcast.episode.index');
        Route::get('podcastepisode/add/{id}', [PodcastController::class, 'PodcastAdd'])->name('podcast.episode.add');
        Route::get('podcastepisode/edit/{podcasts_id}/{id}', [PodcastController::class, 'PodcastEdit'])->name('podcast.episode.edit');
        // Music
        Route::resource('music', MusicController::class)->only(['index', 'create', 'edit']);
        Route::get('music_status/{id}', [MusicController::class, 'changeStatus'])->name('music.status');
        // Notification
        Route::resource('notification', NotificationController::class)->only(['index', 'create', 'store']);
        Route::get('notifications/setting', [NotificationController::class, 'setting'])->name('notification.setting');
        // Live Event
        Route::resource('liveevent', LiveEventController::class)->only(['index']);
        Route::get('liveevent/{id}', [LiveEventController::class, 'LiveEventIndex'])->name('liveevent.user.index');
        // Comment
        Route::resource('comment', CommentController::class)->only(['index', 'show']);
        // Package
        Route::resource('package', PackageController::class)->only(['index', 'create', 'edit']);
        // Transaction
        Route::resource('transaction', TransactionController::class)->only(['index', 'create']);
        Route::any('searchuser', [TransactionController::class, 'searchUser'])->name('searchUser');
        // Payment
        Route::resource('payment', PaymentController::class)->only(['index']);
        // App Setting
        Route::get('setting', [SettingController::class, 'index'])->name('setting');
        Route::post('setting/app', [SettingController::class, 'app'])->name('setting.app');
        Route::post('setting/currency', [SettingController::class, 'currency'])->name('setting.currency');
        Route::post('setting/dev', [SettingController::class, 'dev'])->name('setting.dev');
        Route::post('setting/screenshot', [SettingController::class, 'screenshot'])->name('setting.screenshot');
        Route::post('setting/save_key', [SettingController::class, 'save_key'])->name('setting.save_key');
        Route::post('setting/smtp', [SettingController::class, 'smtpSave'])->name('smtp.save');
        Route::post('setting/test_smtp', [SettingController::class, 'testSmtp'])->name('setting.test_smtp');
        Route::post('setting/sociallinks', [SettingController::class, 'sociallinksupdate'])->name('setting.sociallinksupdate');
        Route::post('setting/onboardingscreen', [SettingController::class, 'saveOnBoardingScreen'])->name('setting.obboardingscreen');
        // Admob
        Route::resource('admob', AdmobSettingController::class)->only(['index']);
        // FaceBook Ads
        Route::resource('fbads', FaceBookAdsSettingController::class)->only(['index']);
        // System Setting
        Route::get('systemsetting', [SystemSettingController::class, 'index'])->name('system.setting.index');
        Route::post('systemsetting/cleardata', [SystemSettingController::class, 'ClearData'])->name('system.setting.cleardata');
        Route::post('systemsetting/dummydata', [SystemSettingController::class, 'DummyData'])->name('system.setting.dummydata');
        Route::post('systemsetting/cleandatabase', [SystemSettingController::class, 'CleanDatabase'])->name('system.setting.cleandatabase');
        // Panel Setting
        Route::get('panel_setting', [PanelSettingController::class, 'index'])->name('panel_setting.index');
        Route::post('panel_setting/save', [PanelSettingController::class, 'save'])->name('panel_setting.save');
        // Notification Configuration
        Route::resource('notification_configuration', NotificationConfigurationController::class)->only(['index', 'store']);
        // Artist Requests
        Route::get('artist-requests', [ArtistRequestController::class, 'index'])->name('admin.artist-requests.index');
        // JAILAOI: Monetization Applications
        Route::get('monetization', [MonetizationController::class, 'index'])->name('admin.monetization.index');

        // JAILAOI: KYC
        Route::get('kyc', [KycController::class, 'index'])->name('admin.kyc.index');
        Route::get('kyc/view/{id}', [KycController::class, 'view'])->name('admin.kyc.view');
        // JAILAOI: Withdrawals
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('admin.withdrawals.index');
        Route::get('withdrawals/show/{id}', [WithdrawalController::class, 'show'])->name('admin.withdrawals.show');
        // JAILAOI: Monetization / Earnings Overview
        Route::get('earnings', [WithdrawalController::class, 'earningsOverview'])->name('admin.earnings.index');

        Route::group(['middleware' => 'checkadmin'], function () {

            // Profile
            Route::resource('profile', ProfileController::class)->only(['store']);
            Route::post('profile/changepassword', [ProfileController::class, 'ChangePassword'])->name('profile.changepassword');
            // Category
            Route::resource('category', CategoryController::class)->only(['store', 'update', 'destroy']);
            // Language
            Route::resource('language', LanguageController::class)->only(['store', 'update', 'destroy']);
            // City
            Route::resource('city', CityController::class)->only(['store', 'update', 'destroy']);
            // Artist/RJ
            Route::resource('artist', ArtistController::class)->only(['store', 'update', 'destroy']);
            Route::post('artist/{id}/suspend', [ArtistController::class, 'suspend'])->name('artist.suspend');
            Route::post('artist/{id}/unsuspend', [ArtistController::class, 'unsuspend'])->name('artist.unsuspend');
            // Pages
            Route::resource('page', PageController::class)->only(['update', 'destroy']);
            // User
            Route::resource('user', UserController::class)->only(['store', 'update', 'destroy']);
            // Song
            Route::resource('song', SongController::class)->only(['store', 'update', 'show']);
            // Podcasts
            Route::resource('podcast', PodcastController::class)->only(['store', 'update', 'destroy']);
            Route::post('podcastepisode/save', [PodcastController::class, 'PodcastSave'])->name('podcast.episode.save');
            Route::post('podcastepisode/update/{podcasts_id}/{id}', [PodcastController::class, 'PodcastUpdate'])->name('podcast.episode.update');
            Route::get('podcastepisode/delete/{podcasts_id}/{id}', [PodcastController::class, 'PodcastDelete'])->name('podcast.episode.delete');
            Route::post('podcastepisode/sortable', [PodcastController::class, 'PodcastSortable'])->name('podcast.episode.sortable');
            // Music
            Route::resource('music', MusicController::class)->only(['store', 'update', 'show']);
            // Notification
            Route::resource('notification', NotificationController::class)->only(['destroy']);
            Route::post('notifications/setting', [NotificationController::class, 'settingsave'])->name('notification.settingsave');
            // Live Event
            Route::resource('liveevent', LiveEventController::class)->only(['store', 'update', 'destroy']);
            Route::get('notifications/setting', [NotificationController::class, 'setting'])->name('notification.setting');
            Route::post('liveevent/delete/{liveevent_id}/{id}', [LiveEventController::class, 'LiveEventDelete'])->name('liveevent.user.delete');
            // Package
            Route::resource('package', PackageController::class)->only(['store', 'update', 'destroy']);
            // Transaction
            Route::resource('transaction', TransactionController::class)->only(['store', 'destroy']);
            // Payment
            Route::resource('payment', PaymentController::class)->only(['edit', 'update']);
            // Admob
            Route::post('admob/android', [AdmobSettingController::class, 'admobAndroid'])->name('admob.android');
            Route::post('admob/ios', [AdmobSettingController::class, 'admobIos'])->name('admob.ios');
            // FaceBook Ads
            Route::post('fbads/android', [FaceBookAdsSettingController::class, 'facebookadAndroid'])->name('fbads.android');
            Route::post('fbads/ios', [FaceBookAdsSettingController::class, 'facebookadIos'])->name('fbads.ios');
            // System Setting
            Route::get('systemsetting/downloadsqlfile', [SystemSettingController::class, 'DownloadSqlFile'])->name('system.setting.downloadsqlfile');
            // Artist Requests
            Route::post('artist-requests/approve', [ArtistRequestController::class, 'approve'])->name('admin.artist-requests.approve');
            Route::post('artist-requests/reject', [ArtistRequestController::class, 'reject'])->name('admin.artist-requests.reject');
            // JAILAOI: Monetization Applications
            Route::post('monetization/approve', [MonetizationController::class, 'approve'])->name('admin.monetization.approve');
            Route::post('monetization/reject', [MonetizationController::class, 'reject'])->name('admin.monetization.reject');

            // JAILAOI: KYC
            Route::post('kyc/approve', [KycController::class, 'approve'])->name('admin.kyc.approve');
            Route::post('kyc/reject', [KycController::class, 'reject'])->name('admin.kyc.reject');
            // JAILAOI: Withdrawals
            Route::post('withdrawals/approve', [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
            Route::post('withdrawals/reject', [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
            Route::post('withdrawals/mark-paid', [WithdrawalController::class, 'markPaid'])->name('admin.withdrawals.mark-paid');
            // JAILAOI: Play Errors
            Route::get('play-errors', [PlayErrorController::class, 'index'])->name('admin.play-errors');
        });
    });
});
