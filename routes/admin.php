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
use App\Http\Controllers\Admin\ArtistRequestController;
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
use App\Http\Controllers\Admin\PodcastBannerController;
use App\Http\Controllers\Admin\PodcastSectionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Models\Section;

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

    Route::group(['middleware' => 'authadmin'], function () {

        // Chunk upload (requires auth)
        Route::any('song/saveChunk', [SongController::class, 'saveChunk']);
        Route::any('podcasts/saveChunk', [PodcastController::class, 'saveChunk']);

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        // Profile
        Route::resource('profile', ProfileController::class)->only(['index']);
        // Category
        Route::resource('category', CategoryController::class)->only(['index']);
        // Language
        Route::resource('language', LanguageController::class)->only(['index']);
        // City
        Route::resource('city', CityController::class)->only(['index']);
        // Pages
        Route::resource('page', PageController::class)->only(['index', 'store', 'edit']);
        // Artist/RJ
        Route::resource('artist', ArtistController::class)->only(['index']);
        // Artist Requests
        Route::resource('artist-requests', ArtistRequestController::class)->only(['index']);
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
        Route::get('getcontent', [BannerController::class, 'getcontent'])->name('getcontent');
        // Podcast Section
        Route::resource('podcastsection', PodcastSectionController::class)->only(['index', 'store', 'show', 'update']);
        Route::post('podcastsection/content', [PodcastSectionController::class, 'GetSectionData'])->name('podcastsection.data');
        Route::post('podcastsection/edit', [PodcastSectionController::class, 'SectionDataEdit'])->name('podcastsection.edit');
        Route::post('podcastsection/status', [PodcastSectionController::class, 'changeStatus'])->name('podcastsection.status');
        Route::post('podcastsection/sortable', [PodcastSectionController::class, 'SectionSortable'])->name('podcastsection.sortable');
        Route::post('podcastsection/sortable/save', [PodcastSectionController::class, 'SectionSortableSave'])->name('podcastsection.sortable.save');
        // Song
        Route::resource('song', SongController::class)->only(['index', 'create', 'edit']);
        Route::get('song_status/{id}', [SongController::class, 'changeStatus'])->name('song.status');
        // Podcasts
        Route::resource('podcast', PodcastController::class)->only(['index', 'show']);
        Route::get('podcastepisode/{id}', [PodcastController::class, 'PodcastIndex'])->name('podcast.episode.index');
        Route::get('podcastepisode/add/{id}', [PodcastController::class, 'PodcastAdd'])->name('podcast.episode.add');
        Route::get('podcastepisode/edit/{podcasts_id}/{id}', [PodcastController::class, 'PodcastEdit'])->name('podcast.episode.edit');
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
        Route::post('setting/smtp', [SettingController::class, 'smtpSave'])->name('smtp.save');
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
            // Artist Requests
            Route::post('artist-requests/approve', [ArtistRequestController::class, 'approve'])->name('artist-requests.approve');
            Route::post('artist-requests/reject', [ArtistRequestController::class, 'reject'])->name('artist-requests.reject');
            // Pages
            Route::resource('page', PageController::class)->only(['update']);
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
        });
    });
});
