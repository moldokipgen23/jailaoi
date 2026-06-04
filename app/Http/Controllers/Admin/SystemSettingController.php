<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Banner;
use App\Models\Batch;
use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Episode;
use App\Models\Event_Join_User;
use App\Models\Favorite;
use App\Models\Follow;
use App\Models\Language;
use App\Models\Live_Event;
use App\Models\Music;
use App\Models\Notification;
use App\Models\Onboarding_Screen;
use App\Models\Package;
use App\Models\Page;
use App\Models\Play;
use App\Models\Podcast;
use App\Models\Social_Link;
use App\Models\Song;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User_Action;
use App\Models\User_Notification_Tracking;
use App\Models\User_Summary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Exception;

class SystemSettingController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $params['data'] = [];
            return view('admin.system_setting.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ClearData()
    {
        try {
            // Folder Name
            $app = 'public/app';
            $artist = 'public/artist';
            $category = 'public/category';
            $city = 'public/city';
            $database = 'public/database';
            $language = 'public/language';
            $live_event = 'public/live_event';
            $notification = 'public/notification';
            $package = 'public/package';
            $podcast = 'public/podcast';
            $song = 'public/song';
            $user = 'public/user';
            $music = 'public/music';

            // Name Array
            $app_name = [];
            $artist_name = [];
            $category_name = [];
            $city_name = [];
            $database_name = [];
            $language_name = [];
            $live_event_name = [];
            $notification_name = [];
            $package_name = [];
            $podcast_name = [];
            $song_name = [];
            $user_name = [];
            $music_name = [];

            // Get Files
            $app_file = Storage::allFiles($app);
            $artist_file = Storage::allFiles($artist);
            $category_file = Storage::allFiles($category);
            $city_file = Storage::allFiles($city);
            $database_file = Storage::allFiles($database);
            $language_file = Storage::allFiles($language);
            $live_event_file = Storage::allFiles($live_event);
            $notification_file = Storage::allFiles($notification);
            $package_file = Storage::allFiles($package);
            $podcast_file = Storage::allFiles($podcast);
            $song_file = Storage::allFiles($song);
            $user_file = Storage::allFiles($user);
            $music_file = Storage::allFiles($music);

            // Add Name In Array
            foreach ($app_file as $app_file) {
                array_push($app_name, pathinfo($app_file)['basename']);
            }
            foreach ($artist_file as $artist_file) {
                array_push($artist_name, pathinfo($artist_file)['basename']);
            }
            foreach ($category_file as $file_name) {
                array_push($category_name, pathinfo($file_name)['basename']);
            }
            foreach ($city_file as $city_file) {
                array_push($city_name, pathinfo($city_file)['basename']);
            }
            foreach ($database_file as $database_file) {
                array_push($database_name, pathinfo($database_file)['basename']);
            }
            foreach ($language_file as $language_file) {
                array_push($language_name, pathinfo($language_file)['basename']);
            }
            foreach ($live_event_file as $live_event_file) {
                array_push($live_event_name, pathinfo($live_event_file)['basename']);
            }
            foreach ($notification_file as $notification_file) {
                array_push($notification_name, pathinfo($notification_file)['basename']);
            }
            foreach ($package_file as $package_file) {
                array_push($package_name, pathinfo($package_file)['basename']);
            }
            foreach ($podcast_file as $podcast_file) {
                array_push($podcast_name, pathinfo($podcast_file)['basename']);
            }
            foreach ($song_file as $song_file) {
                array_push($song_name, pathinfo($song_file)['basename']);
            }
            foreach ($user_file as $user_file) {
                array_push($user_name, pathinfo($user_file)['basename']);
            }
            foreach ($music_file as $music_file) {
                array_push($music_name, pathinfo($music_file)['basename']);
            }

            // Delete File In Folder
            foreach ($app_name as $key => $value) {

                $app_file_check = Page::select('id')->where('icon', $value)->first();
                $app_file_check_1 = Onboarding_Screen::select('id')->where('image', $value)->first();

                $settingData = Setting_Data();
                $app_file_check_2 = 'yes';
                if ($settingData['app_logo'] != $value) {
                    $app_file_check_2 = 'no';
                }

                if ($app_file_check == null && $app_file_check_1 == null && $app_file_check_2 == 'no') {
                    $this->common->deleteImageToFolder('app', $value);
                }
            }
            foreach ($artist_name as $key => $value) {

                $artist_file_check = Artist::select('id')->where('image', $value)->first();
                if ($artist_file_check == null) {
                    $this->common->deleteImageToFolder('artist', $value);
                }
            }
            foreach ($category_name as $key => $value) {

                $category_file_check = Category::select('id')->where('image', $value)->first();
                if ($category_file_check == null) {
                    $this->common->deleteImageToFolder('category', $value);
                }
            }
            foreach ($city_name as $key => $value) {

                $city_file_check = City::select('id')->where('image', $value)->first();
                if ($city_file_check == null) {
                    $this->common->deleteImageToFolder('city', $value);
                }
            }
            foreach ($database_name as $key => $value) {
                $this->common->deleteImageToFolder('database', $value);
            }
            foreach ($language_name as $key => $value) {

                $language_file_check = Language::select('id')->where('image', $value)->first();
                if ($language_file_check == null) {
                    $this->common->deleteImageToFolder('language', $value);
                }
            }
            foreach ($live_event_name as $key => $value) {

                $live_event_file_check = Live_Event::select('id')->where('portrait_img', $value)->orwhere('landscape_img', $value)->first();
                if ($live_event_file_check == null) {
                    $this->common->deleteImageToFolder('live_event', $value);
                }
            }
            foreach ($notification_name as $key => $value) {

                $notification_file_check = Notification::select('id')->where('image', $value)->first();
                if ($notification_file_check == null) {
                    $this->common->deleteImageToFolder('notification', $value);
                }
            }
            foreach ($package_name as $key => $value) {

                $package_file_check = Package::select('id')->where('image', $value)->first();
                if ($package_file_check == null) {
                    $this->common->deleteImageToFolder('package', $value);
                }
            }
            foreach ($podcast_name as $key => $value) {

                $podcast_file_check = Podcast::select('id')->where('portrait_img', $value)->orwhere('landscape_img', $value)->first();
                $podcast_file_check_1 = Episode::select('id')->where('portrait_img', $value)->orwhere('landscape_img', $value)->orwhere('episode_audio', $value)->first();

                if ($podcast_file_check == null && $podcast_file_check_1 == null) {
                    $this->common->deleteImageToFolder('podcast', $value);
                }
            }
            foreach ($song_name as $key => $value) {

                $song_file_check = Song::select('id')->where('image', $value)->orwhere('song_url', $value)->first();
                if ($song_file_check == null) {
                    $this->common->deleteImageToFolder('song', $value);
                }
            }
            foreach ($user_name as $key => $value) {

                $user_file_check = User::select('id')->where('image', $value)->first();
                if ($user_file_check == null) {
                    $this->common->deleteImageToFolder('user', $value);
                }
            }
            foreach ($music_name as $key => $value) {

                $music_file_check = Music::select('id')->where('portrait_img', $value)->orwhere('landscape_img', $value)->orwhere('ogtag_img', $value)->first();
                if ($music_file_check == null) {
                    $this->common->deleteImageToFolder('music', $value);
                }
            }

            return response()->json(['status' => 200, 'success' => __('label.data_clear')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function DownloadSqlFile()
    {
        try {

            Artisan::call('config:clear');

            $storageAt = storage_path() . "/app/public/database";
            if (!file_exists($storageAt)) {
                File::makeDirectory($storageAt, 0755, true, true);
            }

            $mysqlHostName = env('DB_HOST');
            $mysqlUserName = env('DB_USERNAME');
            $mysqlPassword = env('DB_PASSWORD');
            $DbName = env('DB_DATABASE');

            // get all table name
            $result = DB::select("SHOW TABLES");
            $prep = "Tables_in_$DbName";
            foreach ($result as $res) {
                $tables[] =  $res->$prep;
            }

            $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $statement = $connect->prepare("SHOW TABLES");
            $statement->execute();
            $result = $statement->fetchAll();

            $output = '';
            foreach ($tables as $table) {

                $show_table_query = "SHOW CREATE TABLE " . $table . "";
                $statement = $connect->prepare($show_table_query);
                $statement->execute();
                $show_table_result = $statement->fetchAll();

                foreach ($show_table_result as $show_table_row) {
                    $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                }
                $select_query = "SELECT * FROM " . $table . "";
                $statement = $connect->prepare($select_query);
                $statement->execute();
                $total_row = $statement->rowCount();

                for ($count = 0; $count < $total_row; $count++) {
                    $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                    $table_column_array = array_keys($single_result);
                    $table_value_array = array_values($single_result);
                    $output .= "\nINSERT INTO $table (";
                    $output .= "`" . implode("`, `", $table_column_array) . "`) VALUES (";
                    $output .= "'" . implode("', '", $table_value_array) . "');\n";
                }
            }

            $file_name = App_Name() . '_db_' . date('d_m_Y') . '.sql';
            $file_handle = fopen(storage_path() . '/app/public/database/' . $file_name, 'w+');
            fwrite($file_handle, $output);
            fclose($file_handle);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(storage_path() . '/app/public/database/' . $file_name));
            ob_clean();
            flush();
            readfile(storage_path() . '/app/public/database/' . $file_name);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function DummyData()
    {
        try {

            // $artist = [
            //     ['name' => 'Arijit Singh', 'image' => 'artist1.jpg', 'bio' => $this->common->artist_tag_line(), 'status' => 1],
            //     ['name' => 'Sonu Nigam', 'image' => 'artist2.jpg', 'bio' => $this->common->artist_tag_line(), 'status' => 1],
            //     ['name' => 'Shreya Ghoshal', 'image' => 'artist3.jpg', 'bio' => $this->common->artist_tag_line(), 'status' => 1],
            // ];
            // Artist::insert($artist);

            // $category = [
            //     ['name' => 'Sport', 'image' => 'category1.jpg', 'type' => 1, 'status' => 1],
            //     ['name' => 'Bollywood', 'image' => 'category2.jpg', 'type' => 1, 'status' => 1],
            //     ['name' => 'Workout', 'image' => 'category3.jpg', 'type' => 2, 'status' => 1],
            //     ['name' => 'Romance', 'image' => 'category4.jpg', 'type' => 2, 'status' => 1],
            // ];
            // Category::insert($category);

            // $language = [
            //     ['name' => 'Hindi', 'image' => 'language1.jpg', 'status' => 1],
            //     ['name' => 'English', 'image' => 'language2.jpg', 'status' => 1],
            // ];
            // Language::insert($language);

            // $hashtag = [
            //     ['name' => 'workout', 'total_used' => 78, 'status' => 1],
            //     ['name' => 'travel', 'total_used' => 99, 'status' => 1],
            //     ['name' => 'food', 'total_used' => 34, 'status' => 1],
            //     ['name' => 'lifestyle', 'total_used' => 67, 'status' => 1],
            //     ['name' => 'business', 'total_used' => 61, 'status' => 1],
            // ];
            // Hashtag::insert($hashtag);

            // $user = [
            //     [
            //         'channel_id' => 'xnawp7wg',
            //         'channel_name' => 'Thoughts of Devloper',
            //         'full_name' => 'Henry',
            //         'email' => 'henry@dt.com',
            //         'password' => Hash::make('henry'),
            //         'mobile_number' => '6352147890',
            //         'type' => 4,
            //         'image' => 'user1.jpg',
            //         'cover_img' => 'coveruser1.jpg',
            //         'description' => $this->common->user_tag_line(),
            //         'device_type' => 0,
            //         'device_token' => "",
            //         'website' => "",
            //         'facebook_url' => "",
            //         'instagram_url' => "",
            //         'twitter_url' => "",
            //         'wallet_balance' => 0,
            //         'wallet_earning' => 0,
            //         'bank_name' => "",
            //         'bank_code' => "",
            //         'bank_address' => "",
            //         'ifsc_no' => "",
            //         'account_no' => "",
            //         'id_proof' => "",
            //         'address' => "",
            //         'city' => "",
            //         'state' => "",
            //         'country' => "",
            //         'pincode' => 0,
            //         'country' => "",
            //         'status' => 1
            //     ],
            //     [
            //         'channel_id' => '3dsvzlwy',
            //         'channel_name' => 'Devloper Planet',
            //         'full_name' => 'Jack',
            //         'email' => 'jack@dt.com',
            //         'password' => Hash::make('jack'),
            //         'mobile_number' => '7845120369',
            //         'type' => 4,
            //         'image' => 'user2.jpg',
            //         'cover_img' => 'coveruser2.jpg',
            //         'description' => $this->common->user_tag_line(),
            //         'device_type' => 0,
            //         'device_token' => "",
            //         'website' => "",
            //         'facebook_url' => "",
            //         'instagram_url' => "",
            //         'twitter_url' => "",
            //         'wallet_balance' => 0,
            //         'wallet_earning' => 0,
            //         'bank_name' => "",
            //         'bank_code' => "",
            //         'bank_address' => "",
            //         'ifsc_no' => "",
            //         'account_no' => "",
            //         'id_proof' => "",
            //         'address' => "",
            //         'city' => "",
            //         'state' => "",
            //         'country' => "",
            //         'pincode' => 0,
            //         'country' => "",
            //         'status' => 1
            //     ],
            //     [
            // Artist::query()->truncate();
            // Banner::query()->truncate();
            // Category::query()->truncate();
            // City::query()->truncate();
            // Comment::query()->truncate();
            // Episode::query()->truncate();
            // Favorite::query()->truncate();
            // Language::query()->truncate();
            // Live_Event::query()->truncate();
            // Notification::query()->truncate();
            // Package::query()->truncate();
            // Podcast::query()->truncate();
            // Onboarding_Screen::query()->truncate();
            // Song::query()->truncate();
            // Transaction::query()->truncate();
            // User::query()->truncate();
            // User_Notification_Tracking::query()->truncate();
            // Event_Join_User::query()->truncate();

            // return response()->json(array('status' => 200, 'success' => __('label.data_clean)));
            //         'channel_id' => '32gko0af',
            //         'channel_name' => 'Devloper Studio',
            //         'full_name' => 'Axel',
            //         'email' => 'axel@dt.com',
            //         'password' => Hash::make('axel'),
            //         'mobile_number' => '820147963',
            //         'type' => 4,
            //         'image' => 'user3.jpg',
            //         'cover_img' => 'coveruser3.jpg',
            //         'description' => $this->common->user_tag_line(),
            //         'device_type' => 0,
            //         'device_token' => "",
            //         'website' => "",
            //         'facebook_url' => "",
            //         'instagram_url' => "",
            //         'twitter_url' => "",
            //         'wallet_balance' => 0,
            //         'wallet_earning' => 0,
            //         'bank_name' => "",
            //         'bank_code' => "",
            //         'bank_address' => "",
            //         'ifsc_no' => "",
            //         'account_no' => "",
            //         'id_proof' => "",
            //         'address' => "",
            //         'city' => "",
            //         'state' => "",
            //         'country' => "",
            //         'pincode' => 0,
            //         'country' => "",
            //         'status' => 1
            //     ],
            // ];
            // User::insert($user);

            // $content = [
            //     [
            //         'content_type' => 1,
            //         'channel_id' => 'xnawp7wg',
            //         'category_id' => 1,
            //         'language_id' => 1,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Thoughts of Devloper',
            //         'description' => 'Thoughts of Devloper',
            //         'portrait_img' => 'videocontent1.jpg',
            //         'landscape_img' => 'videocontent1.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'video.mp4',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 4578,
            //         'total_like' => 2700,
            //         'total_dislike' => 125,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 1,
            //         'channel_id' => '3dsvzlwy',
            //         'category_id' => 1,
            //         'language_id' => 1,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Devloper Planet',
            //         'description' => 'Devloper Planet',
            //         'portrait_img' => 'videocontent2.jpg',
            //         'landscape_img' => 'videocontent2.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'video.mp4',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 2789,
            //         'total_like' => 156,
            //         'total_dislike' => 25,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 2,
            //         'channel_id' => '0',
            //         'category_id' => 1,
            //         'language_id' => 1,
            //         'artist_id' => 1,
            //         'hashtag_id' => 0,
            //         'title' => 'Music - The Language of Feelings.',
            //         'description' => 'Music - The Language of Feelings.',
            //         'portrait_img' => 'musiccontent1.jpg',
            //         'landscape_img' => 'musiccontent1.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'music.mp3',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 4578,
            //         'total_like' => 2700,
            //         'total_dislike' => 125,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 2,
            //         'channel_id' => '0',
            //         'category_id' => 2,
            //         'language_id' => 2,
            //         'artist_id' => 2,
            //         'hashtag_id' => 0,
            //         'title' => 'Music - Part of Life',
            //         'description' => 'Music - Part of Life',
            //         'portrait_img' => 'musiccontent2.jpg',
            //         'landscape_img' => 'musiccontent2.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'music.mp3',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 2789,
            //         'total_like' => 156,
            //         'total_dislike' => 25,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 3,
            //         'channel_id' => 'xnawp7wg',
            //         'category_id' => 0,
            //         'language_id' => 0,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Types of Devloper',
            //         'description' => 'Types of Devloper',
            //         'portrait_img' => 'reelscontent1.jpg',
            //         'landscape_img' => 'reelscontent1.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'reels.mp4',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 4578,
            //         'total_like' => 2700,
            //         'total_dislike' => 125,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 3,
            //         'channel_id' => '3dsvzlwy',
            //         'category_id' => 0,
            //         'language_id' => 0,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Planet Life',
            //         'description' => 'Planet Life',
            //         'portrait_img' => 'reelscontent2.jpg',
            //         'landscape_img' => 'reelscontent2.jpg',
            //         'content_upload_type' => 'server_video',
            //         'content' => 'reels.mp4',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 2789,
            //         'total_like' => 156,
            //         'total_dislike' => 25,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 4,
            //         'channel_id' => 'xnawp7wg',
            //         'category_id' => 1,
            //         'language_id' => 1,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'My Daily !!!',
            //         'description' => 'My Daily !!!',
            //         'portrait_img' => 'podcastscontent1.jpg',
            //         'landscape_img' => 'podcastscontent1.jpg',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 4578,
            //         'total_like' => 2700,
            //         'total_dislike' => 125,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 4,
            //         'channel_id' => '3dsvzlwy',
            //         'category_id' => 2,
            //         'language_id' => 2,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Life partner',
            //         'description' => 'Life partner',
            //         'portrait_img' => 'podcastscontent2.jpg',
            //         'landscape_img' => 'podcastscontent2.jpg',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 1,
            //         'is_download' => 1,
            //         'is_like' => 1,
            //         'total_play' => 2789,
            //         'total_like' => 156,
            //         'total_dislike' => 25,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 5,
            //         'channel_id' => 'xnawp7wg',
            //         'category_id' => 0,
            //         'language_id' => 0,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'My Fav Playlist',
            //         'description' => 'My Fav Playlist',
            //         'portrait_img' => '',
            //         'landscape_img' => '',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 0,
            //         'is_download' => 0,
            //         'is_like' => 0,
            //         'total_play' => 0,
            //         'total_like' => 0,
            //         'total_dislike' => 0,
            //         'playlist_type' => 1,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 5,
            //         'channel_id' => '3dsvzlwy',
            //         'category_id' => 0,
            //         'language_id' => 0,
            //         'artist_id' => 0,
            //         'hashtag_id' => 0,
            //         'title' => 'Driving Song Playlist',
            //         'description' => 'Driving Song Playlist',
            //         'portrait_img' => '',
            //         'landscape_img' => '',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 0,
            //         'is_download' => 0,
            //         'is_like' => 0,
            //         'total_play' => 0,
            //         'total_like' => 0,
            //         'total_dislike' => 0,
            //         'playlist_type' => 1,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 6,
            //         'channel_id' => '0',
            //         'category_id' => 1,
            //         'language_id' => 1,
            //         'artist_id' => 1,
            //         'hashtag_id' => 0,
            //         'title' => 'Morning Radio...',
            //         'description' => 'Morning Radio...',
            //         'portrait_img' => 'radiocontent1.jpg',
            //         'landscape_img' => 'radiocontent1.jpg',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 0,
            //         'is_download' => 0,
            //         'is_like' => 0,
            //         'total_play' => 0,
            //         'total_like' => 0,
            //         'total_dislike' => 0,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            //     [
            //         'content_type' => 6,
            //         'channel_id' => '0',
            //         'category_id' => 2,
            //         'language_id' => 2,
            //         'artist_id' => 2,
            //         'hashtag_id' => 0,
            //         'title' => 'Radio With RJ...',
            //         'description' => 'Radio With RJ...',
            //         'portrait_img' => 'radiocontent2.jpg',
            //         'landscape_img' => 'radiocontent2.jpg',
            //         'content_upload_type' => '',
            //         'content' => '',
            //         'content_size' => '0',
            //         'is_rent' => 0,
            //         'rent_price' => 0,
            //         'is_comment' => 0,
            //         'is_download' => 0,
            //         'is_like' => 0,
            //         'total_play' => 0,
            //         'total_like' => 0,
            //         'total_dislike' => 0,
            //         'playlist_type' => 0,
            //         'is_admin_added' => 1,
            //         'status' => 1,
            //     ],
            // ];
            // Content::insert($content);

            return response()->json(array('status' => 200, 'success' => __('label.data_insert')));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function CleanDatabase()
    {
        try {

            Artist::query()->truncate();
            Banner::query()->truncate();
            Category::query()->truncate();
            City::query()->truncate();
            Comment::query()->truncate();
            Episode::query()->truncate();
            Event_Join_User::query()->truncate();
            Favorite::query()->truncate();
            Follow::query()->truncate();
            Language::query()->truncate();
            Live_Event::query()->truncate();
            Music::query()->truncate();
            Notification::query()->truncate();
            Onboarding_Screen::query()->truncate();
            Package::query()->truncate();
            Play::query()->truncate();
            Podcast::query()->truncate();
            Social_Link::query()->truncate();
            Song::query()->truncate();
            Transaction::query()->truncate();
            User::query()->truncate();
            User_Notification_Tracking::query()->truncate();
            User_Action::query()->truncate();
            User_Summary::query()->truncate();
            Batch::query()->truncate();

            return response()->json(['status' => 200, 'success' => __('label.data_clean')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
