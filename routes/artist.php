<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Artist\LoginController;
use App\Http\Controllers\Artist\DashboardController;
use App\Http\Controllers\Artist\ProfileController;
use App\Http\Controllers\Artist\MusicController;
use App\Http\Controllers\Artist\VideoController;
use App\Http\Controllers\Artist\AnalyticsController;

Route::group(['middleware' => 'installation'], function () {

    Route::get('login', [LoginController::class, 'login'])->name('artist.login');
    Route::post('login', [LoginController::class, 'save_login'])->name('artist.save.login');
    Route::get('logout', [LoginController::class, 'logout'])->name('artist.logout');

    Route::group(['middleware' => 'authartist', 'as' => 'artist.'], function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('music', [MusicController::class, 'index'])->name('music.index');
        Route::post('music/delete/{id}', [MusicController::class, 'destroy'])->name('music.destroy');

        Route::get('video', [VideoController::class, 'index'])->name('video.index');
        Route::post('video/delete/{id}', [VideoController::class, 'destroy'])->name('video.destroy');

        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });
});
