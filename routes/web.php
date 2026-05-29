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

use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Socket\SocketController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Artisan
Route::get('clearcache', function () {

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

    // Pages
    Route::get('page/{page_name}', [PageController::class, 'page_view'])->name('page.view');
    // Change Language
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'hi', 'fr'])) {
            session(['locale' => $locale]);
            App::setLocale($locale);
        }
        return redirect()->back();
    })->name('change.language');
});

// Socket Route
Route::post('addlivehistory', [SocketController::class, 'addLiveHistory']);
Route::post('endlive', [SocketController::class, 'endLive']);
Route::post('addview', [SocketController::class, 'addView']);
Route::post('lessview', [SocketController::class, 'lessView']);
Route::post('livechat', [SocketController::class, 'liveChat']);
Route::post('sendgift', [SocketController::class, 'sendGift']);
