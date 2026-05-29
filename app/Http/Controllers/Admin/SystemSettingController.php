<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Ads_View_Click_Count;
use App\Models\Badges_Bonus;
use App\Models\Block_Channel;
use App\Models\Category;
use App\Models\Coin_Package;
use App\Models\Coin_Transaction;
use App\Models\Comment;
use App\Models\Common;
use App\Models\Content;
use App\Models\Content_Like;
use App\Models\Content_Report;
use App\Models\Content_View;
use App\Models\Episode;
use App\Models\Feed;
use App\Models\Feed_Comment;
use App\Models\Feed_Content;
use App\Models\Feed_Like;
use App\Models\Feed_Report;
use App\Models\Gift;
use App\Models\Gift_Transaction;
use App\Models\Hashtag;
use App\Models\History;
use App\Models\Interests;
use App\Models\Language;
use App\Models\Live_History;
use App\Models\Live_User;
use App\Models\Notification;
use App\Models\Onboarding_Screen;
use App\Models\Package;
use App\Models\Package_Detail;
use App\Models\Page;
use App\Models\Playlist_Content;
use App\Models\Read_Notification;
use App\Models\Reason;
use App\Models\Refer_Earn;
use App\Models\Rent_Section;
use App\Models\Rent_Transaction;
use App\Models\Section;
use App\Models\Social_Link;
use App\Models\Subscriber;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User_Badges_Bonus;
use App\Models\Watch_later;
use App\Models\Withdrawal_Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

            $s3_ads_file = [];
            $s3_badges_file = [];
            $s3_category_file = [];
            $s3_content_file = [];
            $s3_feed_file = [];
            $s3_gift_file = [];
            $s3_language_file = [];
            $s3_package_file = [];
            $s3_setting_file = [];
            $s3_user_file = [];
            $s3_notification_file = [];
            try {
                $s3_ads_file = Storage::disk('s3')->allFiles('ads');
                $s3_badges_file = Storage::disk('s3')->allFiles('badges_bonus');
                $s3_category_file = Storage::disk('s3')->allFiles('category');
                $s3_content_file = Storage::disk('s3')->allFiles('content');
                $s3_feed_file = Storage::disk('s3')->allFiles('feed');
                $s3_gift_file = Storage::disk('s3')->allFiles('gift');
                $s3_language_file = Storage::disk('s3')->allFiles('language');
                $s3_package_file = Storage::disk('s3')->allFiles('package');
                $s3_setting_file = Storage::disk('s3')->allFiles('setting');
                $s3_user_file = Storage::disk('s3')->allFiles('user');
                $s3_notification_file = Storage::disk('s3')->allFiles('notification');
            } catch (Exception $e) {
            }

            // Ads
            $local_ads_file = Storage::allFiles('public/ads');
            $ads_files = array_merge($local_ads_file, $s3_ads_file);
            $ads_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $ads_files);
            $used_ads_files = Ads::pluck('image')->merge(Ads::pluck('video'))->filter()->toArray();
            foreach ($ads_name as $value) {
                if (!in_array($value, $used_ads_files)) {
                    $this->common->deleteImageToFolder('ads', $value, 1);
                    $this->common->deleteImageToFolder('ads', $value, 2);
                }
            }

            // Badges & Bonus
            $local_badges_file = Storage::allFiles('public/badges_bonus');
            $badges_files = array_merge($local_badges_file, $s3_badges_file);
            $badges_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $badges_files);
            $used_badges_files = Badges_Bonus::pluck('image')->filter()->toArray();
            foreach ($badges_name as $value) {
                if (!in_array($value, $used_badges_files)) {
                    $this->common->deleteImageToFolder('badges_bonus', $value, 1);
                    $this->common->deleteImageToFolder('badges_bonus', $value, 2);
                }
            }

            // Category
            $local_category_file = Storage::allFiles('public/category');
            $category_files = array_merge($local_category_file, $s3_category_file);
            $category_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $category_files);
            $used_category_files = Category::pluck('image')->filter()->toArray();
            foreach ($category_name as $value) {
                if (!in_array($value, $used_category_files)) {
                    $this->common->deleteImageToFolder('category', $value, 1);
                    $this->common->deleteImageToFolder('category', $value, 2);
                }
            }

            // Content
            $local_content_file = Storage::allFiles('public/content');
            $content_files = array_merge($local_content_file, $s3_content_file);
            $content_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $content_files);
            $content_used_files = Content::pluck('portrait_img')->merge(Content::pluck('landscape_img'))->merge(Content::pluck('content'))->filter()->toArray();
            $episode_used_files = Episode::pluck('portrait_img')->merge(Episode::pluck('landscape_img'))->merge(Episode::pluck('episode_audio'))->filter()->toArray();
            $used_content_files = array_unique(array_merge($content_used_files, $episode_used_files));
            foreach ($content_name as $value) {
                if (!in_array($value, $used_content_files)) {
                    $this->common->deleteImageToFolder('content', $value, 1);
                    $this->common->deleteImageToFolder('content', $value, 2);
                }
            }

            // Database
            $local_database_file = Storage::allFiles('public/database');
            $database_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $local_database_file);
            foreach ($database_name as $value) {
                $this->common->deleteImageToFolder('database', $value, 1);
            }

            // Feed
            $local_feed_file = Storage::allFiles('public/feed');
            $feed_files = array_merge($local_feed_file, $s3_feed_file);
            $feed_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $feed_files);
            $used_feed_files = Feed_Content::pluck('image')->merge(Feed_Content::pluck('video'))->filter()->toArray();
            foreach ($feed_name as $value) {
                if (!in_array($value, $used_feed_files)) {
                    $this->common->deleteImageToFolder('feed', $value, 1);
                    $this->common->deleteImageToFolder('feed', $value, 2);
                }
            }

            // Gift
            $local_gift_file = Storage::allFiles('public/gift');
            $gift_files = array_merge($local_gift_file, $s3_gift_file);
            $gift_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $gift_files);
            $used_gift_files = Gift::pluck('image')->filter()->toArray();
            foreach ($gift_name as $value) {
                if (!in_array($value, $used_gift_files)) {
                    $this->common->deleteImageToFolder('gift', $value, 1);
                    $this->common->deleteImageToFolder('gift', $value, 2);
                }
            }

            // Language
            $local_language_file = Storage::allFiles('public/language');
            $language_files = array_merge($local_language_file, $s3_language_file);
            $language_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $language_files);
            $used_language_files = Language::pluck('image')->filter()->toArray();
            foreach ($language_name as $value) {
                if (!in_array($value, $used_language_files)) {
                    $this->common->deleteImageToFolder('language', $value, 1);
                    $this->common->deleteImageToFolder('language', $value, 2);
                }
            }

            // Package
            $local_package_file = Storage::allFiles('public/package');
            $package_files = array_merge($local_package_file, $s3_package_file);
            $package_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $package_files);
            $package_used_files = Package::pluck('image')->filter()->toArray();
            $coin_package_used_files = Coin_Package::pluck('image')->filter()->toArray();
            $used_package_files = array_unique(array_merge($package_used_files, $coin_package_used_files));
            foreach ($package_name as $value) {
                if (!in_array($value, $used_package_files)) {
                    $this->common->deleteImageToFolder('package', $value, 1);
                    $this->common->deleteImageToFolder('package', $value, 2);
                }
            }

            // Setting
            $local_setting_file = Storage::allFiles('public/setting');
            $setting_files = array_merge($local_setting_file, $s3_setting_file);
            $setting_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $setting_files);
            $page_used_files = Page::pluck('icon')->filter()->toArray();
            $social_link_used_files = Social_Link::pluck('image')->filter()->toArray();
            $onboarding_used_files = Onboarding_Screen::pluck('image')->filter()->toArray();
            $setting_used_files = Setting_Data();
            $used_setting_files = array_unique(array_merge($page_used_files, $social_link_used_files, $onboarding_used_files));
            foreach ($setting_name as $key => $value) {

                if (!in_array($value, $used_setting_files)) {

                    $check = 'no';
                    if ($setting_used_files['app_logo'] != $value && $setting_used_files['panel_login_page_bg_image'] != $value && $setting_used_files['panel_login_page_image'] != $value) {
                        $check = 'yes';
                    }

                    if ($check == 'yes') {
                        $this->common->deleteImageToFolder('setting', $value, 1);
                        $this->common->deleteImageToFolder('setting', $value, 2);
                    }
                }
            }

            // User
            $local_user_file = Storage::allFiles('public/user');
            $user_files = array_merge($local_user_file, $s3_user_file);
            $user_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $user_files);
            $used_user_files = User::pluck('image')->merge(User::pluck('cover_img'))->merge(User::pluck('front_id_proof'))->merge(User::pluck('back_id_proof'))->filter()->toArray();
            foreach ($user_name as $value) {
                if (!in_array($value, $used_user_files)) {
                    $this->common->deleteImageToFolder('user', $value, 1);
                    $this->common->deleteImageToFolder('user', $value, 2);
                }
            }

            // Notification
            $local_notification_file = Storage::allFiles('public/notification');
            $notification_files = array_merge($local_notification_file, $s3_notification_file);
            $notification_name = array_map(fn($file) => pathinfo($file, PATHINFO_BASENAME), $notification_files);
            $used_notification_files = Notification::pluck('image')->filter()->toArray();
            foreach ($notification_name as $value) {
                if (!in_array($value, $used_notification_files)) {
                    $this->common->deleteImageToFolder('notification', $value, 1);
                    $this->common->deleteImageToFolder('notification', $value, 2);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_clear_successfully')]);
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
    public function CleanDatabase()
    {
        try {

            Ads::query()->truncate();
            Ads_View_Click_Count::query()->truncate();
            Badges_Bonus::query()->truncate();
            Block_Channel::query()->truncate();
            Category::query()->truncate();
            Coin_Package::query()->truncate();
            Coin_Transaction::query()->truncate();
            Comment::query()->truncate();
            Content::query()->truncate();
            Content_Like::query()->truncate();
            Content_Report::query()->truncate();
            Content_View::query()->truncate();
            Episode::query()->truncate();
            Feed::query()->truncate();
            Feed_Comment::query()->truncate();
            Feed_Content::query()->truncate();
            Feed_Like::query()->truncate();
            Feed_Report::query()->truncate();
            Gift::query()->truncate();
            Gift_Transaction::query()->truncate();
            Hashtag::query()->truncate();
            History::query()->truncate();
            Interests::query()->truncate();
            Language::query()->truncate();
            Live_History::query()->truncate();
            Live_User::query()->truncate();
            Notification::query()->truncate();
            Onboarding_Screen::query()->truncate();
            Package::query()->truncate();
            Package_Detail::query()->truncate();
            Playlist_Content::query()->truncate();
            Read_Notification::query()->truncate();
            Refer_Earn::query()->truncate();
            Rent_Section::query()->truncate();
            Rent_Transaction::query()->truncate();
            Reason::query()->truncate();
            Section::query()->truncate();
            Social_Link::query()->truncate();
            Subscriber::query()->truncate();
            Transaction::query()->truncate();
            User::query()->truncate();
            User_Badges_Bonus::query()->truncate();
            Watch_later::query()->truncate();
            Withdrawal_Request::query()->truncate();

            return response()->json(['status' => 200, 'success' => __('label.data_clean_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function ClearInterests()
    {
        try {

            $data = Interests::all();
            foreach ($data as $interests) {

                $categoryData = json_decode($interests['category_ids'], true) ?? [];
                if (count($categoryData) > 10) {

                    arsort($categoryData);
                    $categoryData = array_slice($categoryData, 0, 10, true);

                    $interests['category_ids'] = json_encode($categoryData);
                    $interests->save();
                }

                $hashtagData = json_decode($interests['hashtag_ids'], true) ?? [];
                if (count($hashtagData) > 20) {

                    arsort($hashtagData);
                    $hashtagData = array_slice($hashtagData, 0, 20, true);

                    $interests['hashtag_ids'] = json_encode($hashtagData);
                    $interests->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_clear_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
