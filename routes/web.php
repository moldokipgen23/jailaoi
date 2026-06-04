<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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

// Page
Route::group(['middleware' => 'installation'], function () {
    Route::get('pages/{page_name}', [DashboardController::class, 'Page'])->name('admin.pages');

    // Change Language
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'hi', 'fr'])) {
            session(['locale' => $locale]);
            App::setLocale($locale);
        }
        return redirect()->back();
    })->name('change.language');
});
