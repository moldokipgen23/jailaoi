<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AlbumController;

Route::group(['middleware' => 'apipurchasecode'], function () {

    // --------------------- UserController ---------------------
    Route::post('login', [UserController::class, 'login']);
    Route::post('get_profile', [UserController::class, 'get_profile']);
    Route::post('update_profile', [UserController::class, 'update_profile']);
    Route::post('add_remove_subscribe', [UserController::class, 'add_remove_subscribe']);
    Route::post('get_subscribe_list', [UserController::class, 'get_subscribe_list']);
    Route::post('get_subscriber_list', [UserController::class, 'get_subscriber_list']);
    Route::post('add_remove_block_channel', [UserController::class, 'add_remove_block_channel']);
    Route::post('logout', [UserController::class, 'logout']);

    // --------------------- HomeController ---------------------
    Route::post('general_setting', [HomeController::class, 'general_setting']);
    Route::post('get_payment_option', [HomeController::class, 'get_payment_option']);
    Route::post('get_pages', [HomeController::class, 'get_pages']);
    Route::post('get_social_link', [HomeController::class, 'get_social_link']);
    Route::post('get_onboarding_screen', [HomeController::class, 'get_onboarding_screen']);
    Route::post('get_report_reason', [HomeController::class, 'get_report_reason']);
    Route::post('get_package', [HomeController::class, 'get_package']);
    Route::post('get_coin_package', [HomeController::class, 'get_coin_package']);
    Route::post('add_view', [HomeController::class, 'add_view']);
    Route::post('add_content_report', [HomeController::class, 'add_content_report']);
    Route::post('add_remove_like_dislike', [HomeController::class, 'add_remove_like_dislike']);
    Route::post('add_comment', [HomeController::class, 'add_comment']);
    Route::post('edit_comment', [HomeController::class, 'edit_comment']);
    Route::post('delete_comment', [HomeController::class, 'delete_comment']);
    Route::post('get_comment', [HomeController::class, 'get_comment']);
    Route::post('get_reply_comment', [HomeController::class, 'get_reply_comment']);
    Route::post('add_remove_watch_later', [HomeController::class, 'add_remove_watch_later']);
    Route::post('get_content_detail', [HomeController::class, 'get_content_detail']);
    Route::post('get_like_content', [HomeController::class, 'get_like_content']);
    Route::post('get_content_by_channel', [HomeController::class, 'get_content_by_channel']);
    Route::post('get_watch_later_content', [HomeController::class, 'get_watch_later_content']);
    Route::post('add_content_to_history', [HomeController::class, 'add_content_to_history']);
    Route::post('remove_content_to_history', [HomeController::class, 'remove_content_to_history']);
    Route::post('get_content_to_history', [HomeController::class, 'get_content_to_history']);
    Route::post('get_episode_by_podcasts', [HomeController::class, 'get_episode_by_podcasts']);
    Route::post('get_rent_section', [HomeController::class, 'get_rent_section']);
    Route::post('get_rent_section_detail', [HomeController::class, 'get_rent_section_detail']);
    Route::post('get_user_rent_content', [HomeController::class, 'get_user_rent_content']);
    Route::post('get_rent_content_by_channel', [HomeController::class, 'get_rent_content_by_channel']);
    Route::post('delete_content', [HomeController::class, 'delete_content']);
    Route::post('search_content', [HomeController::class, 'search_content']);
    // --------------------- AlbumController ---------------------
    Route::post('get_album_list', [AlbumController::class, 'get_album_list']);
    Route::post('get_album_detail', [AlbumController::class, 'get_album_detail']);
    Route::post('get_album_songs', [AlbumController::class, 'get_album_songs']);
    Route::post('create_album', [AlbumController::class, 'create_album']);
    Route::post('edit_album', [AlbumController::class, 'edit_album']);
    Route::post('delete_album', [AlbumController::class, 'delete_album']);

    // --------------------- ArtistController ---------------------
    Route::post('get_artist_list', [ArtistController::class, 'get_artist_list']);
    Route::post('get_artist_profile', [ArtistController::class, 'get_artist_profile']);
    Route::post('get_artist_content', [ArtistController::class, 'get_artist_content']);
    Route::post('apply_artist', [ArtistController::class, 'apply_artist']);
    Route::post('get_artist_request_status', [ArtistController::class, 'get_artist_request_status']);
    Route::post('follow_artist', [ArtistController::class, 'follow_artist']);
    Route::post('unfollow_artist', [ArtistController::class, 'unfollow_artist']);
    Route::post('get_artist_dashboard', [ArtistController::class, 'get_artist_dashboard']);

    Route::post('add_rent_transaction', [HomeController::class, 'add_rent_transaction']);
    Route::post('add_transaction', [HomeController::class, 'add_transaction']);
    Route::post('get_transaction_list', [HomeController::class, 'get_transaction_list']);
    Route::post('add_coin_transaction', [HomeController::class, 'add_coin_transaction']);
    Route::post('get_coin_transaction_list', [HomeController::class, 'get_coin_transaction_list']);
    Route::post('get_ads', [HomeController::class, 'get_ads']);
    Route::post('add_ads_view_click_count', [HomeController::class, 'add_ads_view_click_count']);
    Route::post('get_ads_coin_history', [HomeController::class, 'get_ads_coin_history']);
    Route::post('get_gift', [HomeController::class, 'get_gift']);
    Route::post('get_user_gift', [HomeController::class, 'get_user_gift']);
    Route::post('buy_gift', [HomeController::class, 'buy_gift']);
    Route::post('get_withdrawal_request_list', [HomeController::class, 'get_withdrawal_request_list']);
    Route::post('get_notification', [HomeController::class, 'get_notification']);
    Route::post('read_notification', [HomeController::class, 'read_notification']);
    Route::post('send_gift', [HomeController::class, 'send_gift']);
    Route::post('send_gift_transaction', [HomeController::class, 'send_gift_transaction']);

    // --------------------- ContentController ---------------------
    Route::post('get_video_list', [ContentController::class, 'get_video_list']);
    Route::post('get_category', [ContentController::class, 'get_category']);
    Route::post('get_language', [ContentController::class, 'get_language']);
    Route::post('get_releted_video', [ContentController::class, 'get_releted_video']);
    Route::post('get_music_section', [ContentController::class, 'get_music_section']);
    Route::post('get_music_section_detail', [ContentController::class, 'get_music_section_detail']);
    Route::post('get_music_by_category', [ContentController::class, 'get_music_by_category']);
    Route::post('get_music_by_language', [ContentController::class, 'get_music_by_language']);
    Route::post('get_releted_music', [ContentController::class, 'get_releted_music']);
    Route::post('upload_video', [ContentController::class, 'upload_video']);
    Route::post('upload_music', [ContentController::class, 'upload_music']);
    Route::post('upload_radio', [ContentController::class, 'upload_radio']);
    Route::post('create_playlist', [ContentController::class, 'create_playlist']);
    Route::post('edit_playlist', [ContentController::class, 'edit_playlist']);
    Route::post('delete_playlist', [ContentController::class, 'delete_playlist']);
    Route::post('add_remove_content_to_playlist', [ContentController::class, 'add_remove_content_to_playlist']);
    Route::post('add_multipal_content_to_playlist', [ContentController::class, 'add_multipal_content_to_playlist']);
    Route::post('get_playlist_content', [ContentController::class, 'get_playlist_content']);
    Route::post('get_content_to_playlist', [ContentController::class, 'get_content_to_playlist']);
    Route::post('create_podcast', [ContentController::class, 'create_podcast']);
    Route::post('upload_episode', [ContentController::class, 'upload_episode']);
});
