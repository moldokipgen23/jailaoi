<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Artist\AuthController;
use App\Http\Controllers\Artist\DashboardController;
use App\Http\Controllers\Artist\SongController;

Route::group(['middleware' => 'installation'], function () {

    // Login-Logout
    Route::get('login', [AuthController::class, 'login'])->name('artist.login');
    Route::post('login', [AuthController::class, 'save_login'])->name('artist.save.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('artist.logout');

    Route::group(['middleware' => ['auth.artist', 'artist.role']], function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('artist.dashboard');

        // Songs
        Route::get('songs', [SongController::class, 'index'])->name('artist.songs.index');
        Route::get('songs/create', [SongController::class, 'create'])->name('artist.songs.create');
        Route::post('songs', [SongController::class, 'store'])->name('artist.songs.store');
        Route::get('songs/{id}/edit', [SongController::class, 'edit'])->name('artist.songs.edit');
        Route::put('songs/{id}', [SongController::class, 'update'])->name('artist.songs.update');
        Route::get('songs/{id}/delete', [SongController::class, 'destroy'])->name('artist.songs.destroy');

    });
});
