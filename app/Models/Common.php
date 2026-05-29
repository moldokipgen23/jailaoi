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

                if ($value['song_upload_type'] == "server_video") {
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

        $notification = Setting_Data();
        $ONESIGNAL_APP_ID = $notification['onesignal_apid'];
        $ONESIGNAL_REST_KEY = $notification['onesignal_rest_key'];

        $fields = array(
            'app_id' => $ONESIGNAL_APP_ID,
            'included_segments' => array('All'),
            'data' => $array,
            'headings' => array("en" => $array['name']),
            'contents' => array("en" => $array['name']),
            'big_picture' => $array['image'],
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
        // dd($response);
        curl_close($ch);
    }
    public function package_expiry()
    {
        $all_data = Transaction::where('status', 1)->get();
        for ($i = 0; $i < count($all_data); $i++) {

            if ($all_data[$i]['expiry_date'] <= date("Y-m-d")) {
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

        $is_buy = Transaction::where('user_id', $user_id)->where('status', 1)->first();
        if (!empty($is_buy)) {
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

            // Artist
            $value['artist_name'] = "";
            $artist = Artist::select('name')->where('id', $value['artist_id'])->first();
            if (isset($artist)) {
                $value['artist_name'] = $artist['name'];
            }

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
        $array['total_comment'] = Comment::where('type', $type)->where('content_id', $array['id'])->count();
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
    public function Send_Mail($type, $email) // Type = 1- Register, 2- Transaction
    {
        try {

            $this->SetSmtpConfig();

            $smtp = Smtp::latest()->first();
            if (isset($smtp) && $smtp != false && $smtp['status'] == 1) {

                if ($type == 1) {
                    $title = App_Name() . " - Register";
                    $body = "Welcome to " . App_Name() . " App & Enjoy this app.";
                } else if ($type == 2) {
                    $title = App_Name() . " - Transaction";
                    $body = "Welcome to " . App_Name() . " App & Enjoy this app. You have Successfully Transaction.";
                } else if ($type == 3) {

                    $title = app_name() . " - Login";
                    $body = "Welcome to " . app_name() . " App & Enjoy this app.";
                } else {
                    return true;
                }
                $details = [
                    'title' => $title,
                    'body' => $body
                ];

                Mail::to($email)->send(new \App\Mail\mail($details));
            } else {
                return true;
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    // type = 1-Song, 2-Podcat, 3-Live Event, 4-Artist, 5-Category, 6-Language, 7-City
    public function section_query($user_id, $type, $artist_id, $category_id, $language_id, $city_id, $order_by_upload, $order_by_play, $is_preimum, $no_of_content)
    {
        try {
            if ($type == 1) {
                $content = Song::where('status', 1);
            } else if ($type == 2) {
                $content = Podcast::where('status', 1);
            }

            if ($type == 1) {

                if ($artist_id != 0) {
                    $content->where('artist_id', $artist_id);
                }

                if ($city_id != 0) {
                    $content->where('city_id', $city_id);
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

            if ($is_preimum == 0) {
                $content->where('is_premium', 0);
            } else if ($is_preimum == 1) {
                $content->where('is_premium', 1);
            }

            $query = $content->take($no_of_content)->get();

            $this->getAllIdByName($query);
            for ($i = 0; $i < count($query); $i++) {
                if ($type == 1) {

                    $query[$i]['image'] = $this->Get_Image($this->folder_song, $query[$i]['image']);
                    if ($query[$i]['song_upload_type'] == "server_video") {
                        $query[$i]['song_url'] = $this->Get_Song($this->folder_song, $query[$i]['song_url']);
                    }

                    $query[$i]['is_favorite'] = $this->isFavorite(1, $query[$i]['id'], $user_id);
                    $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);
                    $this->get_all_count_for_content(1, $query[$i]);
                } elseif ($type == 2) {

                    $query[$i]['portrait_img'] = $this->Get_Image($this->folder_podcast, $query[$i]['portrait_img']);
                    $query[$i]['landscape_img'] = $this->Get_Image($this->folder_podcast, $query[$i]['landscape_img']);

                    $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);
                    $query[$i]['is_favorite'] = $this->isFavorite(2, $query[$i]['id'], $user_id);
                    $this->get_all_count_for_content(2, $query[$i]);
                }
            }
            return $query;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function section_query_detail($type, $artist_id, $category_id, $language_id, $city_id, $order_by_upload, $order_by_play, $is_preimum)
    {
        try {
            if ($type == 1) {
                $content = Song::where('status', 1);
            } else if ($type == 2) {
                $content = Podcast::where('status', 1);
            }

            if ($type == 1) {

                if ($artist_id != 0) {
                    $content->where('artist_id', $artist_id);
                }

                if ($city_id != 0) {
                    $content->where('city_id', $city_id);
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

            if ($is_preimum == 0) {
                $content->where('is_premium', 0);
            } else if ($is_preimum == 1) {
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
}
