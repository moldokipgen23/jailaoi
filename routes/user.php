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
use App\Http\Controllers\User\EarningsController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MusicController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PasswordController;
use App\Http\Controllers\User\PlaylistController;
use App\Http\Controllers\User\RadioController;

Route::group(['middleware' => 'installation'], function () {

    // Login-Logout
    Route::get('login', [LoginController::class, 'login'])->name('user.login');
    Route::post('login', [LoginController::class, 'save_login'])->name('user.save.login');
    Route::get('logout', [LoginController::class, 'logout'])->name('user.logout');

    // JAILAOI: Public artist registration
    Route::get('register', [RegisterController::class, 'index'])->name('user.register');
    Route::post('register', [RegisterController::class, 'store'])->name('user.register.store');

    Route::group(['middleware' => 'authuser', 'as' => 'user.'], function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Profile
        Route::resource('profile', ProfileController::class)->only(['index', 'update']);
        // Password 
        Route::resource('password', PasswordController::class)->only(['index', 'update']);
        // Music
        Route::resource('music', MusicController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Playlist
        Route::resource('playlist', PlaylistController::class)->only(['index', 'store', 'update']);
        Route::get('playlist/content/{id}', [PlaylistController::class, 'pl_index'])->name('playlist.content.index');
        Route::post('playlist/contentdata', [PlaylistController::class, 'pl_content_data'])->name('playlist.get.content');
        Route::post('playlist/save', [PlaylistController::class, 'pl_save'])->name('playlist.content.save');
        Route::post('playlist/delete', [PlaylistController::class, 'pl_delete'])->name('playlist.content.delete');
        Route::post('playlist/sortorder', [PlaylistController::class, 'pl_sort_order'])->name('playlist.content.sort_order');
        // Radio
        Route::resource('radio', RadioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        // Custom Ads
        Route::resource('ads', AdsController::class)->only(['index', 'create', 'store', 'edit', 'show']);

        // JAILAOI: Earnings + Withdrawal
        Route::get('earnings', [EarningsController::class, 'index'])->name('earnings.index');
        Route::post('earnings/withdraw', [EarningsController::class, 'requestWithdrawal'])->name('earnings.withdraw');

        Route::group(['middleware' => 'checkadmin'], function () {

            // Music
            Route::resource('music', MusicController::class)->only(['show']);
            // Playlist
            Route::resource('playlist', PlaylistController::class)->only(['destroy']);
            // Radio
            Route::resource('radio', RadioController::class)->only(['show']);
        });
    });
});
