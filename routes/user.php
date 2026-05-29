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
use App\Http\Controllers\User\AdsController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\FeedController;
use App\Http\Controllers\User\MusicController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PasswordController;
use App\Http\Controllers\User\VideoController;
use App\Http\Controllers\User\ReelsController;
use App\Http\Controllers\User\PodcastsController;
use App\Http\Controllers\User\PlaylistController;
use App\Http\Controllers\User\RadioController;

Route::group(['middleware' => 'installation'], function () {

    // Login-Logout
    Route::get('login', [LoginController::class, 'login'])->name('user.login');
    Route::post('login', [LoginController::class, 'save_login'])->name('user.save.login');
    Route::get('logout', [LoginController::class, 'logout'])->name('user.logout');

    Route::group(['middleware' => 'authuser', 'as' => 'user.'], function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Profile
        Route::resource('profile', ProfileController::class)->only(['index', 'update']);
        // Password 
        Route::resource('password', PasswordController::class)->only(['index', 'update']);
        // Video
        Route::resource('video', VideoController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Music
        Route::resource('music', MusicController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Reels
        Route::resource('reels', ReelsController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Podcasts
        Route::resource('podcasts', PodcastsController::class)->only(['index', 'store', 'update']);
        Route::get('podcasts/episode/{id}', [PodcastsController::class, 'ep_index'])->name('podcast.episode.index');
        Route::get('podcasts/episode/add/{id}', [PodcastsController::class, 'ep_add'])->name('podcast.episode.add');
        Route::post('podcasts/episode/save', [PodcastsController::class, 'ep_save'])->name('podcast.episode.save');
        Route::get('podcasts/episode/edit/{podcasts_id}/{id}', [PodcastsController::class, 'ep_edit'])->name('podcast.episode.edit');
        Route::post('podcasts/episode/update/{podcasts_id}/{id}', [PodcastsController::class, 'ep_update'])->name('podcast.episode.update');
        Route::post('podcasts/episode/sortorder', [PodcastsController::class, 'ep_sort_order'])->name('podcast.episode.sort_order');
        // Playlist
        Route::resource('playlist', PlaylistController::class)->only(['index', 'store', 'update']);
        Route::get('playlist/content/{id}', [PlaylistController::class, 'pl_index'])->name('playlist.content.index');
        Route::post('playlist/contentdata', [PlaylistController::class, 'pl_content_data'])->name('playlist.get.content');
        Route::post('playlist/save', [PlaylistController::class, 'pl_save'])->name('playlist.content.save');
        Route::post('playlist/delete', [PlaylistController::class, 'pl_delete'])->name('playlist.content.delete');
        Route::post('playlist/sortorder', [PlaylistController::class, 'pl_sort_order'])->name('playlist.content.sort_order');
        // Radio
        Route::resource('radio', RadioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Feed
        Route::resource('feed', FeedController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Custom Ads
        Route::resource('ads', AdsController::class)->only(['index', 'create', 'store', 'edit', 'show']);

        Route::group(['middleware' => 'checkadmin'], function () {

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
        });
    });
});
