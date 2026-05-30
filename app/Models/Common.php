<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Exception;

class Common extends Model
{
    private $folder_content = "content";

    // Image Functions
    public function getImage($folder = "", $name = "", $storage_type = 1)
    {
        try {
            $name = $this->stripFolderPrefix($folder, $name);
            if ($storage_type == 1) {

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
            } else if ($storage_type == 2) {

                if ($name != "" && $folder != "") {
                    
                    try {
                        
                        $storage = Storage_Setting();
                        $appName = $storage['s3_endpoint'];
                        $bucket_name = $storage['s3_bucket_name'];
                        
                        if (Storage::disk('s3')->exists($folder . '/' . $name)) {
                            $name =  'https://' . $bucket_name . '.' . $appName . '/' . $folder . '/' . $name;
                        } else {
                            if ($folder == "user") {
                                $name = asset('assets/imgs/default.png');
                            } else {
                                $name = asset('assets/imgs/no_img.png');
                            }
                        }
                    } catch (Exception $e) {
                        if ($folder == "user") {
                            $name = asset('assets/imgs/default.png');
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
            }
            return $name;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function saveImage($org_name, $folder, $prefix = "", $storage_type = 1)
    {
        try {
            $img_ext = $org_name->getClientOriginalExtension();
            $filename = $prefix . date('Y_m_d_') . uniqid() . '.' . $img_ext;

            if ($storage_type == 1) {
                $org_name->move(base_path('storage/app/public/' . $folder), $filename);
            } else if ($storage_type == 2) {
                $org_name->storeAs($folder, $filename, 's3');
            }
            return $filename;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function getVideo($folder = "", $name = "", $storage_type = 1)
    {
        try {
            $name = $this->stripFolderPrefix($folder, $name);
            if ($storage_type == 1) {

                $appName = Config::get('app.image_url');
                if ($folder != "" && $name != "") {
                    if (Storage::disk('public')->exists($folder . '/' . $name)) {
                        $name = $appName . $folder . '/' . $name;
                    } else {
                        $name = "";
                    }
                } else {
                    $name = "";
                }
            } else if ($storage_type == 2) {

                if ($name != "" && $folder != "") {

                    try {
                        $storage = Storage_Setting();
                        $appName = $storage['s3_endpoint'];
                        $bucket_name = $storage['s3_bucket_name'];

                        if (Storage::disk('s3')->exists($folder . '/' . $name)) {
                            $name =  'https://' . $bucket_name . '.' . $appName . '/' . $folder . '/' . $name;
                        } else {
                            $name = "";
                        }
                    } catch (Exception $e) {
                        $name = "";
                    }
                } else {
                    $name = "";
                }
            }
            return $name;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function imageNameToUrl($array, $column, $folder, $storage_field = 'storage_type')
    {
        try {
            foreach ($array as $key => $value) {

                if (isset($value[$column])) {
                    $value[$column] = $this->stripFolderPrefix($folder, $value[$column]);
                }

                if ($value[$storage_field] == 1) { // Local Storage

                    $appName = Config::get('app.image_url');

                    if (isset($value[$column]) && $value[$column] != "") {

                        if (Storage::disk('public')->exists($folder . '/' . $value[$column])) {
                            $value[$column] = $appName . $folder . '/' . $value[$column];
                        } else {
                            if ($folder == "user") {
                                $value[$column] = asset('assets/imgs/default.png');
                            } else {
                                $value[$column] = asset('assets/imgs/no_img.png');
                            }
                        }
                    } else {
                        if ($folder == "user") {
                            $value[$column] = asset('assets/imgs/default.png');
                        } else {
                            $value[$column] = asset('assets/imgs/no_img.png');
                        }
                    }
                } else if ($value[$storage_field] == 2) { // AWS S3 Storage

                    if (isset($value[$column]) && $value[$column] != "") {

                        try {
                            $storage = Storage_Setting();
                            $appName = $storage['s3_endpoint'];
                            $bucket_name = $storage['s3_bucket_name'];

                            $url =   'https://' . $bucket_name . '.' . $appName . '/' . $folder . '/' . $value[$column];
                            if (Storage::disk('s3')->exists($folder . '/' . $value[$column])) {
                                $value[$column] = $url;
                            } else {
                                if ($folder == "user") {
                                    $value[$column] = asset('assets/imgs/default.png');
                                } else {
                                    $value[$column] = asset('assets/imgs/no_img.png');
                                }
                            }
                        } catch (Exception $e) {
                            if ($folder == "user") {
                                $value[$column] = asset('assets/imgs/default.png');
                            } else {
                                $value[$column] = asset('assets/imgs/no_img.png');
                            }
                        }
                    } else {
                        if ($folder == "user") {
                            $value[$column] = asset('assets/imgs/default.png');
                        } else {
                            $value[$column] = asset('assets/imgs/no_img.png');
                        }
                    }
                }
            }
            return $array;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function deleteImageToFolder($folder, $name, $storage_type)
    {
        try {

            if ($storage_type == 1) {
                Storage::disk('public')->delete($folder . '/' . $name);
            } else if ($storage_type == 2) {
                Storage::disk('s3')->delete($folder . '/' . $name);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // API's Functions
    public function API_Response($status_code, $message, $array = [], $pagination = '')
    {
        try {
            $data['status'] = $status_code;
            $data['message'] = $message;

            if ($status_code == 200 || $status_code == 206) {
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
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function more_page($current_page, $page_size)
    {
        try {
            $more_page = false;
            if ($current_page < $page_size) {
                $more_page = true;
            }
            return $more_page;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function pagination_array($total_rows, $page_size, $current_page, $more_page)
    {
        try {
            $array['total_rows'] = $total_rows;
            $array['total_page'] = $page_size;
            $array['current_page'] = (int) $current_page;
            $array['more_page'] = $more_page;

            return $array;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // Common Functions
    public function rent_expiry()
    {
        $all_data = Rent_Transaction::where('status', 1)->get();
        for ($i = 0; $i < count($all_data); $i++) {

            if ($all_data[$i]['expiry_date'] < date("Y-m-d")) {
                $all_data[$i]['status'] = 0;
                $all_data[$i]->save();
            }
        }
        return true;
    }
    public function package_expiry()
    {
        $all_data = Transaction::where('status', 1)->get();
        for ($i = 0; $i < count($all_data); $i++) {

            if ($all_data[$i]['expiry_date'] < date("Y-m-d")) {
                $all_data[$i]['status'] = 0;
                $all_data[$i]->save();
            }
        }
        return true;
    }
    public function SetSmtpConfig()
    {
        $smtp = Smtp_Setting::latest()->first();
        if (isset($smtp) && $smtp != null && $smtp['status'] == 1) {

            if ($smtp) {
                $data = [
                    'driver' => 'smtp',
                    'host' => $smtp['host'],
                    'port' => $smtp['port'],
                    'encryption' => 'tls',
                    'username' => $smtp['user'],
                    'password' => $smtp['pass'],
                    'timeout'  => 5,
                    'from' => [
                        'address' => $smtp['from_email'],
                        'name' => $smtp['from_name']
                    ]
                ];
                Config::set('mail', $data);
            }
        }
        return true;
    }
    public function createChannelName($name)
    {
        $channel = User::where('channel_name', $name)->first();
        if ($channel) {
            $name = $name . Str::random(3);
        }
        return $name;
    }
    public function user_tag_line()
    {
        return "Hey, I am user on " . App_Name() . " App.";
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
    public function is_any_package_buy($user_id)
    {
        $this->package_expiry();
        return Transaction::where('user_id', $user_id)->where('status', 1)->exists() ? 1 : 0;
    }
    public function get_block_channel($user_id)
    {
        return Block_Channel::where('user_id', $user_id)->where('status', 1)->pluck('block_channel_id')->toArray();
    }
    public function get_subscriber($user_id)
    {
        return Subscriber::where('user_id', $user_id)->where('status', 1)->with('to_user')->get()
            ->filter(fn($sub) => $sub->to_user && isset($sub->to_user->channel_id))->pluck('to_user.channel_id')->toArray();
    }
    public function get_interests_category($user_id)
    {
        $data = Interests::where('user_id', $user_id)->first();
        if (!$data || empty($data['category_ids'])) {
            return [];
        }

        $categories = json_decode($data['category_ids'], true);
        arsort($categories);

        return array_keys($categories);
    }
    public function get_interests_hashtag($user_id)
    {
        $data = Interests::where('user_id', $user_id)->first();
        if (!$data || empty($data['hashtag_ids'])) {
            return [];
        }

        $hashtag = json_decode($data['hashtag_ids'], true);
        arsort($hashtag);

        return array_keys($hashtag);
    }
    public function add_interests($user_id, $content_id, $point, $status = 1) // 1- plus, 2- Minus
    {
        $content = Content::where('id', $content_id)->first();
        if ($content && in_array($content['content_type'], [1, 2, 4, 5, 6])) {

            $category_id = $content['category_id'] ?? 0;
            if ($category_id != 0) {

                $interests = Interests::where('user_id', $user_id)->first();
                if ($interests) {

                    $categoryData = json_decode($interests['category_ids'], true) ?? [];
                    $currentPoint = $categoryData[$category_id] ?? 0;
                    if ($status == 1) {
                        $categoryData[$category_id] = ($currentPoint) + $point;
                    } else if ($status == 2) {
                        $categoryData[$category_id] = ($currentPoint) - $point;
                    }
                    $interests['category_ids'] = json_encode($categoryData);
                    $interests->save();
                } else {

                    $inset = [
                        'user_id' => $user_id,
                        'category_ids' => json_encode([$category_id => $point]),
                        'hashtag_ids' => json_encode([]),
                    ];
                    Interests::insert($inset);
                }
            }
        } else if ($content && $content['content_type'] == 3) {

            $hashtag_ids = explode(',', $content['hashtag_id']);
            $hashtag_ids = array_filter($hashtag_ids);
            $hashtag_ids = array_map('intval', $hashtag_ids);
            if (!empty($hashtag_ids)) {

                $interests = Interests::where('user_id', $user_id)->first();
                if ($interests) {

                    $hashtagData = json_decode($interests['hashtag_ids'], true) ?? [];
                    foreach ($hashtag_ids as $tag_id) {

                        $currentPoint = $hashtagData[$tag_id] ?? 0;
                        if ($status == 1) {
                            $hashtagData[$tag_id] = $currentPoint + $point;
                        } elseif ($status == 2) {
                            $hashtagData[$tag_id] = $currentPoint - $point;
                        }
                    }
                    $interests['hashtag_ids'] = json_encode($hashtagData);
                    $interests->save();
                } else {

                    $insert = [
                        'user_id' => $user_id,
                        'category_ids' => json_encode([]),
                        'hashtag_ids' => json_encode(array_fill_keys($hashtag_ids, $point)),
                    ];
                    Interests::insert($insert);
                }
            }
        }
        return true;
    }
    public function music_section_query($user_id, $content_type, $category_id, $language_id, $order_by_view, $order_by_like, $order_by_upload, $no_of_content)
    {
        try {

            // Remove Not Episode & Content in Podcasts, Radio, Playlist 
            if ($content_type == 4) {

                $podcasts_id = Episode::where('status', 1)->pluck('podcasts_id')->unique()->toArray();
                $content = Content::whereIn('id', $podcasts_id)->where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            } else if ($content_type == 5) {

                $playlist_id = Playlist_Content::where('status', 1)->pluck('playlist_id')->unique()->toArray();
                $content = Content::whereIn('id', $playlist_id)->where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            } else {
                $content = Content::where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            }

            if ($category_id != 0) {
                $content->where('category_id', $category_id);
            }
            if ($language_id != 0) {
                $content->where('language_id', $language_id);
            }
            if ($order_by_upload == 2) {
                $content->orderBy('id', 'desc');
            }
            if ($order_by_view == 2) {
                $content->orderBy('total_view', 'desc');
            }
            if ($order_by_like == 2) {
                $content->orderBy('total_like', 'desc');
            }
            $query = $content->take($no_of_content)->get();

            for ($i = 0; $i < count($query); $i++) {

                $query[$i]['portrait_img'] = $this->getImage($this->folder_content, $query[$i]['portrait_img'], $query[$i]['portrait_img_storage_type']);
                $query[$i]['landscape_img'] = $this->getImage($this->folder_content, $query[$i]['landscape_img'], $query[$i]['landscape_img_storage_type']);
                if ($query[$i]['content_upload_type'] == 'server_video') {
                    $query[$i]['content'] = $this->getVideo($this->folder_content, $query[$i]['content'], $query[$i]['content_storage_type']);
                }

                $query[$i]['user_id'] = $this->getUserId($query[$i]['channel_id']);
                $query[$i]['channel_name'] = $this->getChannelName($query[$i]['channel_id']);
                $query[$i]['channel_image'] = $this->getChannelImage($query[$i]['channel_id']);
                $query[$i]['category_name'] = $this->getCategoryName($query[$i]['category_id']);
                $query[$i]['language_name'] = $this->getLanguageName($query[$i]['language_id']);
                $query[$i]['is_subscribe'] = $this->is_subscribe($user_id, $query[$i]['user_id']);
                $query[$i]['total_comment'] = $this->getTotalComment($query[$i]['id']);
                $query[$i]['is_user_like_dislike'] = $this->getUserLikeDislike($user_id, $query[$i]['content_type'], $query[$i]['id'], 0);
                $query[$i]['total_subscriber'] = $this->total_subscriber($query[$i]['user_id']);
                $query[$i]['total_episode'] = $this->getTotalEpisode($query[$i]['id']);
                $query[$i]['is_buy'] = $this->is_any_package_buy($user_id);
                $query[$i]['stop_time'] = $this->getContentStopTime($user_id, $query[$i]['content_type'], $query[$i]['id'], 0);
            }
            return $query;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function getTotalEpisode($podcasts_id)
    {
        return Episode::where('podcasts_id', $podcasts_id)->where('status', 1)->count();
    }
    public function music_section_details_query($content_type, $category_id, $language_id, $order_by_view, $order_by_like, $order_by_upload)
    {
        try {

            // Remove Not Episode & Content in Podcasts, Radio, Playlist 
            if ($content_type == 4) {

                $podcasts_id = Episode::where('status', 1)->pluck('podcasts_id')->unique()->toArray();
                $content = Content::whereIn('id', $podcasts_id)->where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            } else if ($content_type == 5) {

                $playlist_id = Playlist_Content::where('status', 1)->pluck('playlist_id')->unique()->toArray();
                $content = Content::whereIn('id', $playlist_id)->where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            } else {
                $content = Content::where('content_type', $content_type)->where('status', 1)->where('is_rent', 0);
            }

            if ($category_id != 0) {
                $content->where('category_id', $category_id);
            }
            if ($language_id != 0) {
                $content->where('language_id', $language_id);
            }
            if ($order_by_upload == 2) {
                $content->orderBy('id', 'desc');
            }
            if ($order_by_view == 2) {
                $content->orderBy('total_view', 'desc');
            }
            if ($order_by_like == 2) {
                $content->orderBy('total_like', 'desc');
            }
            $query = $content;
            return $query;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function is_block($user_id, $to_user_id)
    {
        return Block_Channel::where('user_id', $user_id)->where('block_user_id', $to_user_id)->where('status', 1)->exists() ? 1 : 0;
    }
    public function getUserLikeDislike($user_id, $content_type, $content_id, $episode_id = 0)
    {
        return Content_Like::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->value('status') ?? 0;
    }
    public function getUserId($channel_id)
    {
        return User::where('channel_id', $channel_id)->value('id') ?? 0;
    }
    public function getChannelName($channel_id)
    {
        return User::where('channel_id', $channel_id)->value('channel_name') ?? '';
    }
    public function getChannelImage($channel_id)
    {
        $channel = User::where('channel_id', $channel_id)->first();
        if ($channel != null & isset($channel) && $channel['image'] != "") {
            return $this->getImage('user', $channel['image'], $channel['image_storage_type']);
        }
        return asset('assets/imgs/default.png');
    }
    public function getCategoryName($category_id)
    {
        return Category::where('id', $category_id)->value('name') ?? '';
    }
    public function getLanguageName($language_id)
    {
        return Language::where('id', $language_id)->value('name') ?? '';
    }
    public function is_subscribe($user_id, $to_user_id)
    {
        return Subscriber::where('user_id', $user_id)->where('to_user_id', $to_user_id)->where('status', 1)->exists() ? 1 : 0;
    }
    public function getTotalComment($content_id)
    {
        return Comment::where('content_id', $content_id)->where('comment_id', 0)->where('status', 1)->count();
    }
    public function is_total_content($channel_id)
    {
        $total_contents = 0;
        $total_content = Content::where('channel_id', $channel_id)->where('status', 1)->count();
        $total_feed = Feed::where('channel_id', $channel_id)->where('status', 1)->count();

        $total_contents = $total_content + $total_feed;
        return $total_contents;
    }
    public function total_subscriber($to_user_id)
    {
        return Subscriber::where('to_user_id', $to_user_id)->where('status', 1)->count();
    }
    public function checkHashTag($hashTag)
    {
        try {

            if (strpos($hashTag, '#') !== false) {

                $remove = substr($hashTag, strpos($hashTag, '#'));
                $tag = explode('#', $remove);

                $id = [];
                if (count($tag) > 0) {
                    foreach ($tag as $key => $value) {

                        if ($value && $value != "") {

                            $value = ltrim($value);
                            $tag = explode(' ', $value)[0];

                            $row = Hashtag::where('name', $tag)->first();
                            if (isset($row->id)) {

                                $id[] = $row->id;
                                $row->increment('total_used', 1);
                            } else {

                                $data['name'] = $tag;
                                $data['total_used'] = 1;
                                $hashtag_id = Hashtag::insertGetId($data);
                                $id[] = $hashtag_id;
                            }
                        }
                    }
                    return $id;
                }
            } else {
                return array();
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function time_to_milliseconds($str)
    {

        $time = explode(":", $str);

        $hour = (int) $time[0] * 60 * 60 * 1000;
        $minute = (int) $time[1] * 60 * 1000;
        $sec = (int) $time[2] * 1000;
        $result = $hour + $minute + $sec;
        return $result;
    }
    public function get_user_budget($user_id)
    {
        $budget = 0;
        $data = User::where('id', $user_id)->first();
        if ($data) {
            $budget = $data['wallet_balance'] ?? 0;
        }
        return $budget;
    }
    public function getContentStopTime($user_id, $content_type, $content_id, $episode_id)
    {
        return History::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->value('stop_time') ?? 0;
    }
    public function getRentBuy($user_id, $content_id)
    {
        return Rent_Transaction::where('user_id', $user_id)->where('content_id', $content_id)->where('status', 1)->exists() ? 1 : 0;
    }
    public function get_total_view_click_coin($ads_id)
    {
        return Ads_View_Click_Count::where('ads_id', $ads_id)->sum('total_coin') ?? 0;
    }
    public function inactive_ads($ads_id = 0)
    {
        if ($ads_id != 0) {
            $ads = Ads::where('id', $ads_id)->where('status', 1)->get();
        } else {
            $ads = Ads::where('status', 1)->get();
        }

        $settingData = Setting_Data();
        for ($i = 0; $i < count($ads); $i++) {

            if ($ads[$i]['type'] == 1) {

                $ads_cpv = $settingData['banner_ads_cpv'];
                $user_wallet_coin = $this->get_user_budget($ads[$i]['user_id']);
                if ($ads_cpv > $user_wallet_coin) {
                    $ads[$i]['status'] = 0;
                    $ads[$i]->save();
                } else {

                    $total_view_click_coin = $this->get_total_view_click_coin($ads[$i]['id']);
                    $remening_budget = $ads[$i]['budget'] - $total_view_click_coin;
                    if ($ads_cpv > $remening_budget) {
                        $ads[$i]['status'] = 0;
                        $ads[$i]->save();
                    }
                }
            } else if ($ads[$i]['type'] == 2) {

                $ads_cpv = $settingData['interstital_ads_cpv'];
                $user_wallet_coin = $this->get_user_budget($ads[$i]['user_id']);
                if ($ads_cpv > $user_wallet_coin) {
                    $ads[$i]['status'] = 0;
                    $ads[$i]->save();
                } else {

                    $total_view_click_coin = $this->get_total_view_click_coin($ads[$i]['id']);
                    $remening_budget = $ads[$i]['budget'] - $total_view_click_coin;
                    if ($ads_cpv > $remening_budget) {
                        $ads[$i]['status'] = 0;
                        $ads[$i]->save();
                    }
                }
            } else if ($ads[$i]['type'] == 3) {

                $ads_cpv = $settingData['reward_ads_cpv'];
                $user_wallet_coin = $this->get_user_budget($ads[$i]['user_id']);
                if ($ads_cpv > $user_wallet_coin) {
                    $ads[$i]['status'] = 0;
                    $ads[$i]->save();
                } else {

                    $total_view_click_coin = $this->get_total_view_click_coin($ads[$i]['id']);
                    $remening_budget = $ads[$i]['budget'] - $total_view_click_coin;
                    if ($ads_cpv > $remening_budget) {
                        $ads[$i]['status'] = 0;
                        $ads[$i]->save();
                    }
                }
            }
        }
    }
    public function is_user_download_content($user_id)
    {
        $package_data = Transaction::where('user_id', $user_id)->where('status', 1)->with('package')->latest()->first();
        if ($package_data && $package_data['package'] != null) {
            return $package_data['package']['download_content'];
        }
        return 0;
    }
    public function deleted_feed_all_data($id)
    {
        Feed_Comment::where('feed_id', $id)->delete();
        Feed_Like::where('feed_id', $id)->delete();
        Feed_Report::where('feed_id', $id)->delete();
    }
    public function getHashTag($hashTag)
    {
        $tag_id = array_filter(explode(',', $hashTag));
        $hashtage = [];

        if (!empty($tag_id)) {
            $hashtage = Hashtag::whereIn('id', $tag_id)->get();
        }
        return $hashtage;
    }
    public function get_all_count_for_feed($array, $user_id)
    {
        $array['total_comment'] = Feed_Comment::where('feed_id', $array['id'])->where('status', 1)->count();
        $array['total_like'] = Feed_Like::where('feed_id', $array['id'])->where('status', 1)->count();
        $array['is_like'] = Feed_Like::where('feed_id', $array['id'])->where('user_id', $user_id)->where('status', 1)->exists() ? 1 : 0;
        $array['is_subscriber'] = ($user_id > 0 && !empty($array['channel']['id']) && Subscriber::where('user_id', $user_id)->where('to_user_id', $array['channel']['id'])->exists()) ? 1 : 0;
        return $array;
    }
    public function gift_buy($user_id, $gift_id)
    {
        return (int) Gift_Transaction::where('user_id', $user_id)->where('gift_id', $gift_id)->exists() ? 1 : 0;
    }
    function generateReferenceCode($length = 8)
    {
        $characters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
    public function goLiveSendNotification($user_id, $room_id)
    {
        try {

            $subscribe_user = Subscriber::where('to_user_id', $user_id)->with('user:id,user_name,full_name,image,device_token,device_type')->get();
            $user = User::find($user_id);
            if ($subscribe_user->isEmpty() || !$user) {
                return true;
            }

            $setting = Setting_Data();
            $ONESIGNAL_APP_ID = $setting['onesignal_app_id'];
            $ONESIGNAL_REST_KEY = $setting['onesignal_rest_key'];

            $message = "{$user->channel_name} is now live! Join the stream and interact.";

            // Collect all tokens
            $deviceTokens = [];
            $noti_array = [
                'room_id' => $room_id,
                'user_name' => $user->channel_name,
                'full_name' => $user->full_name,
                'user_image' => $this->getImage($this->folder_user, $user->image, $user->image_storage_type),
                'is_host' => false,
                'is_live_streaming_fake' => $setting['is_live_streaming_fake'],
            ];

            foreach ($subscribe_user as $sub) {
                if ($sub->user && !empty($sub->user->device_token)) {
                    $deviceTokens[] = $sub->user->device_token;
                }
            }

            if (count($deviceTokens) > 0) {
                $fields = [
                    'app_id' => $ONESIGNAL_APP_ID,
                    'include_player_ids' => $deviceTokens,
                    'headings' => ['en' => App_Name()],
                    'contents' => ['en' => $message],
                    'data' => $noti_array,
                ];
                $this->sendNotification($fields, $ONESIGNAL_REST_KEY);
            }
            return true;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    function sendNotification($fields, $restKey)
    {
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $restKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return true;
    }
    public function Send_Mail($type, $email, $content_title = "", $message = "") // Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active
    {
        try {

            $this->SetSmtpConfig();

            $smtp = Smtp_Setting::latest()->first();
            if (isset($smtp) && $smtp != false && $smtp['status'] == 1) {

                if ($type == 1) {

                    $title = "Welcome to " . App_Name() . "! Your Login is Successful";
                    $body = "Welcome to " . App_Name() . " App & Enjoy this app.";
                    $view = 'mail.register';
                } else if ($type == 2) {

                    $title = App_Name() . " - Transaction";
                    $body = "Welcome to " . App_Name() . " App & Enjoy this app. You have Successfully Transaction.";
                    $view = 'mail.transaction';
                } else if ($type == 3) {

                    $title = App_Name() . " - Content Report";
                    $body = "Alert, Your " . $content_title . " Content on Report. Report Reason is " . $message . ".";
                    $view = 'mail.report';
                } else if ($type == 4) {

                    $title = "Welcome to " . App_Name() . " Your User Panel is Now Active";
                    $body = "Congratulations, Your User Penal is Actived.";
                    $view = 'mail.user_panel_active';
                } else {
                    return true;
                }
                $details = [
                    'title' => $title,
                    'body' => $body
                ];

                // Send Mail
                try {
                    Mail::to($email)->send(new \App\Mail\mail($details, $view));
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else {
                return true;
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function save_notification($type = 0, $title = "", $user_id = 0, $from_user_id = 0, $content_id = 0, $is_save = 1) // 1- Admin, 2- Like, 3- Comment, 4- Subscribe
    {
        try {
            if ($is_save == 1) {
                $data = [
                    'type' => $type,
                    'title' => $title,
                    'message' => "",
                    'storage_type' => 0,
                    'image' => "",
                    'user_id' => $user_id,
                    'from_user_id' => $from_user_id,
                    'content_id' => $content_id,
                    'status' => 1,
                ];
                Notification::insertGetId($data);
            }

            $user = User::find($from_user_id);
            if ($user && isset($user['device_token'])) {
                $toUser = [$user['device_token']];

                $setting = Setting_Data();
                $ONESIGNAL_APP_ID = $setting['onesignal_app_id'];
                $ONESIGNAL_REST_KEY = $setting['onesignal_rest_key'];

                $fields = [
                    'app_id' => $ONESIGNAL_APP_ID,
                    'headings' => ['en' => App_Name()],
                    'contents' => ['en' => $title],
                    'channel_for_external_user_ids' => 'push'
                ];

                if ($user['device_type'] == 1) {
                    $fields['include_android_reg_ids'] = $toUser;
                    $fields['isAndroid'] = true;
                } elseif ($user['device_type'] == 2) {
                    $fields['include_player_ids'] = $toUser;
                    $fields['isIos'] = true;
                } elseif ($user['device_type'] == 3) {
                    $fields['include_player_ids'] = $toUser;
                    $fields['isAnyWeb'] = true;
                }

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic ' . $ONESIGNAL_REST_KEY
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);

                curl_close($ch);
                return true;
            }
            return true;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    private function stripFolderPrefix($folder, $name)
    {
        if ($folder && $name && str_starts_with($name, $folder . '/')) {
            return substr($name, strlen($folder) + 1);
        }
        return $name;
    }

    public function generateWaveform($filename, $folder)
    {
        try {
            $audioPath = storage_path($folder . $filename);
            if (!file_exists($audioPath)) {
                return '';
            }
            $waveformName = pathinfo($filename, PATHINFO_FILENAME) . '_waveform.png';
            $outputPath = dirname($audioPath) . '/' . $waveformName;
            $cmd = "ffmpeg -i " . escapeshellarg($audioPath)
                . " -filter_complex \"aformat=channel_layouts=mono,compand,showwavespic=s=800x200:colors=#6a11cb|#4e45b8\""
                . " -frames:v 1 " . escapeshellarg($outputPath) . " 2>/dev/null";
            shell_exec($cmd);
            if (file_exists($outputPath)) {
                return $waveformName;
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    public function ExtractDuration($video, $folder)
    {
        try {
            $videoPath = storage_path($folder . $video);

            // If file not exists → return 0
            if (!file_exists($videoPath)) {
                return 0;
            }

            // Use ffprobe to get duration in seconds (float)
            $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($videoPath);
            $output = shell_exec($cmd);

            // If ffprobe failed or empty output → return 0
            if (empty($output)) {
                return 0;
            }

            // Convert to milliseconds
            $durationMs = (int) round(floatval($output) * 1000);

            return $durationMs > 0 ? $durationMs : 0;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
