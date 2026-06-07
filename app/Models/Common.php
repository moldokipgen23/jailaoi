<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Mail;

class Common extends Model
{
    public $folder_song = "song";
    public $folder_podcast = "podcast";
    public $folder_music = "music";
    public $folder_artist = "artist";

    // Image Functions
    public function saveImage($org_name, $folder, $prefix = "")
    {
        try {

            $img_ext = $org_name->getClientOriginalExtension();
            $filename = $prefix . date('d_m_Y_') . rand(1111, 9999) . '.' . $img_ext;
            $org_name->move(base_path('storage/app/public/' . $folder), $filename);
            return $filename;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function imageNameToUrl($array, $column, $folder)
    {
        try {

            foreach ($array as $key => $value) {
                $appName = Config::get('app.image_url');
                if (isset($value[$column]) && $value[$column] != "") {
                    if ($folder == "user" or $folder == "artist") {
                        if (Storage::disk('public')->exists($folder . '/' . $value[$column])) {
                            $value[$column] = $appName . $folder . '/' . $value[$column];
                        } else {
                            $value[$column] = asset('assets/imgs/default.png');
                        }
                    } else {
                        if (Storage::disk('public')->exists($folder . '/' . $value[$column])) {
                            $value[$column] = $appName . $folder . '/' . $value[$column];
                        } else {
                            $value[$column] = asset('assets/imgs/no_img.png');
                        }
                    }
                } else {
                    if ($folder == "user" || $folder == "artist") {
                        $value[$column] = asset('assets/imgs/default.png');
                    } else {
                        $value[$column] = asset('assets/imgs/no_img.png');
                    }
                }
            }
            return $array;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function deleteImageToFolder($folder, $name)
    {
        try {

            Storage::disk('public')->delete($folder . '/' . $name);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function Get_Image($folder = "", $name = "")
    {

        $appName = Config::get('app.image_url');

        if ($folder != "" && $name != "") {
            if ($folder == "user") {

                if (Storage::disk('public')->exists($folder . '/' . $name)) {
                    $name = $appName . $folder . '/' . $name;
                } else {
                    $name = asset('assets/imgs/default.png');
                }
            } else {

                if (Storage::disk('public')->exists($folder . '/' . $name)) {
                    $name = $appName . $folder . '/' . $name;
                } else {
                    $name = asset('assets/imgs/no_img.png');
                }
            }
        } else {

            if ($folder == "user") {
                $name = asset('assets/imgs/default.png');
            } else {
                $name = asset('assets/imgs/no_img.png');
            }
        }
        return $name;
    }
    public function Get_Song($folder = "", $name = "")
    {
        if ($name != "" && $folder != "") {

            $appName = Config::get('app.image_url');

            if (Storage::disk('public')->exists($folder . '/' . $name)) {
                $data = $appName . $folder . '/' . $name;
            } else {
                $data = "";
            }
        } else {
            $data = "";
        }
        return ($data);
    }
    public function videoNameToUrl($array, $column, $folder)
    {
        try {

            foreach ($array as $key => $value) {

                $appName = Config::get('app.image_url');

                if (isset($value[$column]) && $value[$column] != "") {

                    if (Storage::disk('public')->exists($folder . '/' . $value[$column])) {
                        $value[$column] = $appName . $folder . '/' . $value[$column];
                    } else {
                        $value[$column] = "";
                    }
                } else {

                    $value[$column] = "";
                }
            }
            return $array;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function songNameToUrl($array, $column, $folder)
    {
        try {

            foreach ($array as $key => $value) {

                if ($value['upload_type'] == 1) {
                    $appName = Config::get('app.image_url');
                    if (isset($value[$column]) && $value[$column] != "") {

                        if (Storage::disk('public')->exists($folder . '/' . $value[$column])) {
                            $value[$column] = $appName . $folder . '/' . $value[$column];
                        } else {
                            $value[$column] = "";
                        }
                    } else {
                        $value[$column] = "";
                    }
                } else {
                    $value[$column] = $value['song_url'];
                }
            }
            return $array;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    // API Functions
    public function API_Response($status_code, $message, $array = [], $pagination = '')
    {
        try {

            $data['status'] = $status_code;
            $data['message'] = $message;
            if ($status_code == 200) {
                $data['result'] = $array;
            }

            if ($pagination) {
                $data['total_rows'] = $pagination['total_rows'];
                $data['total_page'] = $pagination['total_page'];
                $data['current_page'] = $pagination['current_page'];
                $data['more_page'] = $pagination['more_page'];
            }

            return $data;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function pagination_array($total_rows, $page_size, $current_page, $more_page)
    {
        $array['total_rows'] = (int) $total_rows;
        $array['total_page'] = (int) $page_size;
        $array['current_page'] = (int) $current_page;
        $array['more_page'] = $more_page;

        return $array;
    }
    public function more_page($current_page, $page_size)
    {
        $more_page = false;
        if ($current_page < $page_size) {
            $more_page = true;
        }
        return $more_page;
    }

    // Common Functions
    function user_name($string)
    {
        $rand_number = rand(0, 1000);
        $user_name = '@' . $string . $rand_number;

        $check = User::where('user_name', $user_name)->first();
        if (isset($check) && $check != null) {
            $this->user_name($string);
        }
        return $user_name;
    }
    function sendNotification($array)
    {
        try {

            $imageUrl = $array['image_url'];
            unset($array['image_url']);

            Notification::insert($array);

            $notification = Setting_Data();
            $ONESIGNAL_APP_ID = $notification['onesignal_apid'];
            $ONESIGNAL_REST_KEY = $notification['onesignal_rest_key'];

            $fields = array(
                'app_id' => $ONESIGNAL_APP_ID,
                'included_segments' => array('All'),
                'data' => $array,
                'headings' => array("en" => $array['title']),
                'contents' => array("en" => $array['description']),
                'big_picture' => $imageUrl,
            );

            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $ONESIGNAL_REST_KEY,
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            curl_close($ch);
            return true;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function basic_notification_configuration($type)
    {
        if ($type != null) {
            return Notification_Configuration::where('type', $type)->first();
        } else {
            return [];
        }
    }
    public function send_push_notification($device_type = '', $device_token = '', $title = '', $message = '')
    {
        try {
            $setting_data = Setting_Data();
            $ONESIGNAL_APP_ID = $setting_data['onesignal_apid'];
            $ONESIGNAL_REST_KEY = $setting_data['onesignal_rest_key'];

            $fields = [
                'app_id' => $ONESIGNAL_APP_ID,
                'headings' => array("en" => $title),
                'contents' => array("en" => $message),
                'include_player_ids' => [$device_token],
            ];

            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $ONESIGNAL_REST_KEY,
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            curl_close($ch);
            return true;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function package_expiry()
    {
        $all_data = Transaction::where('status', 1)->get();
        for ($i = 0; $i < count($all_data); $i++) {

            if ($all_data[$i]['expiry_date'] <= date("Y-m-d H:i")) {
                $all_data[$i]->status = 0;
                $all_data[$i]->save();
            }
        }
        return true;
    }
    public function update_liveevent_status()
    {
        $all_data = Live_Event::get();
        for ($i = 0; $i < count($all_data); $i++) {

            if ($all_data[$i]['date'] < date("Y-m-d")) {
                $all_data[$i]->status = 0;
                $all_data[$i]->save();
            } else {
                $all_data[$i]->status = 1;
                $all_data[$i]->save();
            }
        }
        return true;
    }
    public function is_any_package_buy($user_id)
    {
        $this->package_expiry();

        if (Transaction::where('user_id', $user_id)->where('status', 1)->exists()) {
            return 1;
        } else {
            return 0;
        }
    }
    public function is_package_buy($user_id, $package_id)
    {
        $this->package_expiry();

        $is_buy = Transaction::where('user_id', $user_id)->where('package_id', $package_id)->where('status', 1)->first();
        if (!empty($is_buy)) {
            return 1;
        } else {
            return 0;
        }
    }
    public function getAllIdByName($array)
    {
        foreach ($array as $key => $value) {

            // Category
            $value['category_name'] = "";
            $category = Category::select('name')->where('id', $value['category_id'])->first();
            if (isset($category)) {
                $value['category_name'] = $category['name'];
            }

            // Language
            $value['language_name'] = "";
            $language = Language::select('name')->where('id', $value['language_id'])->first();
            if (isset($language)) {
                $value['language_name'] = $language['name'];
            }

            // Artist Name And Image
            $value['artist_name'] = "";
            $value['artist_image'] = "";

            $artist_ids = explode(',', $value['artist_id']);
            $data = Artist::whereIn('id', $artist_ids)->pluck('name')->toArray();
            if (count($data) > 0) {
                $value['artist_name'] = implode(',', $data);
            }

            $data = Artist::whereIn('id', $artist_ids)->pluck('image')->toArray();
            foreach ($data as &$image) {
                $image = $this->Get_Image($this->folder_artist, $image);
            }
            $value['artist_image'] = implode(',', $data);

            // City
            $value['city_name'] = "";
            $city = City::select('name')->where('id', $value['city_id'])->first();
            if (isset($city)) {
                $value['city_name'] = $city['name'];
            }
        }
        return $array;
    }
    public function isFavorite($type, $song_id, $user_id)
    {
        $data = Favorite::where('type', $type)->where('user_id', $user_id)->where('content_id', $song_id)->first();
        if (isset($data['id'])) {
            return 1;
        } else {
            return 0;
        }
    }
    public function get_all_count_for_content($type, $array)
    {
        try {
            $array['total_comment'] = Comment::where('type', $type)->where('content_id', $array['id'])->count();
        } catch (\Exception $e) {
            $array['total_comment'] = 0;
        }
        return $array;
    }
    public function GetPodcastNameByIds($ids)
    {
        $Ids = $ids;
        $data = Podcast::select('id', 'title')->where('id', $Ids)->get();
        if (count($data) > 0) {

            foreach ($data as $value) {
                $final_data = $value['title'];
            }
            $IDs = $final_data;
            return $IDs;
        } else {
            return "";
        }
    }
    public function SetSmtpConfig()
    {
        $smtp = Smtp::latest()->first();
        if (isset($smtp) && $smtp != null && $smtp['status'] == 1) {

            if ($smtp) {
                $data = [
                    'driver' => 'smtp',
                    'host' => $smtp->host,
                    'port' => $smtp->port,
                    'encryption' => 'tls',
                    'username' => $smtp->user,
                    'password' => $smtp->pass,
                    'from' => [
                        'address' => $smtp->from_email,
                        'name' => $smtp->from_name
                    ]
                ];
                Config::set('mail', $data);
            }
        }
        return true;
    }
    public function Send_Mail($type, $email, $full_name = '', $package_name = '', $price = 0, $transaction_id = '', $expiry_date = '') // Type = 1- Register, 2- Transaction
    {
        try {

            $this->SetSmtpConfig();

            $smtp = Smtp::latest()->first();
            if (isset($smtp) && $smtp != false && $smtp['status'] == 1) {

                if ($type == 1) {
                    $details = [
                        'title' => App_Name() . " - Register",
                        'view' => 'mail.register'
                    ];
                } else if ($type == 2) {
                    $details = [
                        'title' => App_Name() . " - Transaction",
                        'user_name' => $full_name,
                        'package_name' => $package_name,
                        'price' => $price,
                        'transaction_id' => $transaction_id,
                        'expiry_date' => $expiry_date,
                        'view' => 'mail.transaction',
                    ];
                } else if ($type == 3) {
                    $details = [
                        'title' => App_Name() . " - Login",
                        'view' => 'mail.login',
                    ];
                } else if ($type == 4) {
                    $details = [
                        'title' => App_Name() . " - Test Smtp",
                        'view' => 'mail.test',
                    ];
                } else {
                    return true;
                }

                Mail::to($email)->send(new \App\Mail\mail($details));
            } else {
                return true;
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    // type = 1-Song, 2-Podcat, 3-Live Event, 4-Artist, 5-Category, 6-Language, 7-City, 8-Music
    public function section_query($user_id, $type, $artist_id, $category_id, $language_id, $city_id, $order_by_upload, $order_by_play, $is_premium, $no_of_content, $time_window_days = 0)
    {
        try {

            if ($type == 1) {
                $content = Song::with('artist')->where('status', 1);
            } else if ($type == 2) {
                $content = Podcast::where('status', 1);
            } else if ($type == 8) {
                $content = Music::where('status', 1);
            }

            if ($type == 1 || $type == 2) {

                if ($artist_id != 0) {
                    $content->where('artist_id', $artist_id);
                }

                if ($type == 2) {
                    if ($city_id != 0) {
                        $content->where('city_id', $city_id);
                    }
                }
            }

            if ($type == 8) {

                if ($artist_id != 0) {
                    $content->whereRaw("FIND_IN_SET(?,artist_id)", $artist_id);
                }
            }

            if ($category_id != 0) {
                $content->where('category_id', $category_id);
            }

            if ($language_id != 0) {
                $content->where('language_id', $language_id);
            }

            if ($is_premium == 0) {
                $content->where('is_premium', 0);
            } else if ($is_premium == 1) {
                $content->where('is_premium', 1);
            }

            // JAILAOI: Time-windowed top played — counts rows in tbl_user_action by content_id in date window
            $usedTimeWindow = false;
            if ($order_by_play == 1 && (int) $time_window_days > 0 && in_array((int) $type, [1, 2, 8])) {
                $tableMap = [1 => 'tbl_song', 2 => 'tbl_podcast', 8 => 'tbl_music'];
                $tbl = $tableMap[$type];
                $sinceDate = now()->subDays((int) $time_window_days)->toDateTimeString();

                $sub = \Illuminate\Support\Facades\DB::table('tbl_user_action')
                    ->select('content_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as window_play_count'))
                    ->where('action', 1)
                    ->where('content_type', $type)
                    ->where('created_at', '>=', $sinceDate)
                    ->groupBy('content_id');

                $content->leftJoinSub($sub, 'play_window', function ($join) use ($tbl) {
                    $join->on($tbl . '.id', '=', 'play_window.content_id');
                })
                    ->orderByDesc(\Illuminate\Support\Facades\DB::raw('COALESCE(play_window.window_play_count, 0)'))
                    ->select($tbl . '.*');
                $usedTimeWindow = true;
            }

            if (!$usedTimeWindow) {
                if ($order_by_play == 1) {
                    $content->orderBy('total_play', 'desc');
                } else if ($order_by_play == 0) {
                    $content->orderBy('total_play', 'asc');
                }
            }

            if ($order_by_upload == 1) {
                $content->orderBy('id', 'desc');
            } else if ($order_by_upload == 0) {
                $content->orderBy('id', 'asc');
            }

            $query = $content->take($no_of_content)->get();

            $this->getAllIdByName($query);
            for ($i = 0; $i < count($query); $i++) {
                if ($type == 1) {

                    $query[$i]['image'] = $this->Get_Image($this->folder_song, $query[$i]['image']);
                    if ($query[$i]['upload_type'] == 1) {
                        $query[$i]['song_url'] = $this->Get_Song($this->folder_song, $query[$i]['song_url']);
                    }

                    $query[$i]['is_favorite'] = $this->isFavorite(1, $query[$i]['id'], $user_id);
                    $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);

                    $this->get_all_count_for_content(1, $query[$i]);
                    unset($query[$i]['artist']);
                } elseif ($type == 2) {

                    $query[$i]['portrait_img'] = $this->Get_Image($this->folder_podcast, $query[$i]['portrait_img']);
                    $query[$i]['landscape_img'] = $this->Get_Image($this->folder_podcast, $query[$i]['landscape_img']);
                    if ($query[$i]['trailer_upload_type'] == 1) {
                        $query[$i]['trailer_audio'] = $this->Get_Song($this->folder_podcast, $query[$i]['trailer_audio']);
                    }

                    $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);
                    $query[$i]['is_favorite'] = $this->isFavorite(2, $query[$i]['id'], $user_id);
                    $query[$i]['is_follow'] = $this->is_follow($user_id, $query[$i]['artist_id']);

                    $this->get_all_count_for_content(2, $query[$i]);
                } elseif ($type == 8) {

                    if ($query[$i]['upload_type'] == 1) {
                        $query[$i]['music'] = $this->Get_Song($this->folder_music, $query[$i]['music']);
                    }
                    $query[$i]['portrait_img'] = $this->Get_Image($this->folder_music, $query[$i]['portrait_img']);
                    $query[$i]['landscape_img'] = $this->Get_Image($this->folder_music, $query[$i]['landscape_img']);
                    $query[$i]['ogtag_img'] = $this->Get_Image($this->folder_music, $query[$i]['ogtag_img']);

                    $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);
                    $query[$i]['is_favorite'] = $this->isFavorite(3, $query[$i]['id'], $user_id);
                    $this->get_all_count_for_content(3, $query[$i]);
                }
            }
            return $query;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function section_query_detail($type, $artist_id, $category_id, $language_id, $city_id, $order_by_upload, $order_by_play, $is_premium)
    {
        try {

            if ($type == 1) {
                $content = Song::where('status', 1);
            } else if ($type == 2) {
                $content = Podcast::where('status', 1);
            } else if ($type == 8) {
                $content = Music::where('status', 1);
            }

            if ($type == 1 || $type == 2) {

                if ($artist_id != 0) {
                    $content->where('artist_id', $artist_id);
                }

                if ($type == 2) {
                    if ($city_id != 0) {
                        $content->where('city_id', $city_id);
                    }
                }
            }
            if ($type == 8) {

                if ($artist_id != 0) {
                    $content->whereRaw("FIND_IN_SET(?,artist_id)", $artist_id);
                }
            }

            if ($category_id != 0) {
                $content->where('category_id', $category_id);
            }

            if ($language_id != 0) {
                $content->where('language_id', $language_id);
            }

            if ($order_by_play == 1) {
                $content->orderBy('total_play', 'desc');
            } else if ($order_by_play == 0) {
                $content->orderBy('total_play', 'asc');
            }

            if ($order_by_upload == 1) {
                $content->orderBy('id', 'desc');
            } else if ($order_by_upload == 0) {
                $content->orderBy('id', 'asc');
            }

            if ($is_premium == 0) {
                $content->where('is_premium', 0);
            } else if ($is_premium == 1) {
                $content->where('is_premium', 1);
            }

            $query = $content;
            return $query;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $oldValue = env($envKey);

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }
    public function is_follow($user_id, $artist_id)
    {
        return Follow::where('user_id', $user_id)->where('artist_id', $artist_id)->exists() ? 1 : 0;
    }
}
