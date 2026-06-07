<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Song;
use App\Models\Language;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\Payment_Option;
use App\Models\Favorite;
use App\Models\Live_Event;
use App\Models\Common;
use App\Models\Episode;
use App\Models\Event_Join_User;
use App\Models\Follow;
use App\Models\Subscriber;
use App\Models\ArtistEarning;
use App\Models\General_Setting;
use App\Models\Music;
use App\Models\Notification;
use App\Models\Onboarding_Screen;
use App\Models\Page;
use App\Models\Play;
use App\Models\Podcast;
use App\Models\Section;
use App\Models\Social_Link;
use App\Models\User;
use App\Models\User_Action;
use App\Models\User_Notification_Tracking;
use App\Models\User_Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private $folder_app = "app";
    private $folder_city = "city";
    private $folder_song = "song";
    private $folder_artist = "artist";
    private $folder_package = "package";
    private $folder_podcast = "podcast";
    private $folder_category = "category";
    private $folder_language = "language";
    private $folder_live_event = "live_event";
    private $folder_notification = "notification";
    private $folder_user = "user";
    private $folder_music = "music";
    public $common;
    public $page_limit;
    public function __construct()
    {
        $this->common = new Common;
        $this->page_limit = env('PAGE_LIMIT', 20);
    }

    public function general_setting()
    {
        try {

            $list = General_Setting::get();
            foreach ($list as $key => $value) {

                if ($value['key'] == "app_logo" || $value['key'] == "dev_logo" || $value['key'] == "login_page_image" || $value['key'] == "company_logo") {
                    $value['value'] = $this->common->Get_Image($this->folder_app, $value['value']);
                }

                if ($value['key'] == "currency") {
                    if (!empty($value['value'])) {
                        $value['value'] = strtoupper($value['value']);
                    }
                }
            }
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $list);
        } catch (Exception $e) {
            return $this->common->API_Response(400, $e->getMessage());
        }
    }
    public function get_payment_option()
    {
        try {

            $return = [];
            $Option_data = Payment_Option::get();
            foreach ($Option_data as $value) {
                $return[$value['name']] = $value;
            }
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $return);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_payment_token()
    {
        return response()->json([
            'status' => 400,
            'message' => 'PayTM payment token is not configured.',
            'result' => [
                'paytmChecksum' => '',
                'verifySignature' => false,
            ],
        ]);
    }
    public function get_pages()
    {
        try {

            $data = Page::select('title', 'description', 'icon')->get();

            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['url'] = env('APP_URL') . '/public/pages/' . $data[$i]['title'];
                $data[$i]['icon'] = $this->common->Get_Image($this->folder_app, $data[$i]['icon']);
            }
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_onboarding_screen()
    {
        try {

            $data = Onboarding_Screen::get();
            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_app);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_social_link()
    {
        try {

            $data = Social_Link::latest()->get();
            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_app);

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_city(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = City::orderBy('sort_order', 'asc')->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_city);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_artist(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;
            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Artist::orderBy('sort_order', 'asc')->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                foreach ($data as $artist) {
                    $artist['image'] = $this->common->Get_Image($this->folder_artist, $artist['image']);
                    $artist['is_follow'] = $this->common->is_follow($user_id, $artist['id']);
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_category(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Category::orderBy('sort_order', 'asc')->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_category);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_language(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Language::orderBy('sort_order', 'asc')->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_language);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_package(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Package::orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_package);
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['is_buy'] = $this->common->is_package_buy($user_id, $data[$i]['id']);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_radio_banner(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Banner::where('type', 1)->with('song')->orderBy('id', 'DESC')->get();

            $result = [];
            foreach ($data as $key => $value) {
                if ($value['song'] != null) {
                    $result[] = $value['song'];
                }
            }

            $this->common->imageNameToUrl($result, 'image', $this->folder_song);
            $this->common->songNameToUrl($result, 'song_url', $this->folder_song);
            $this->common->getAllIdByName($result);

            foreach ($result as $key => $value) {

                $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                $value['is_buy'] = $this->common->is_any_package_buy($user_id);

                $this->common->get_all_count_for_content(1, $value);
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_radio_by_city(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'city_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $city_id = $request->city_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $language_id = isset($request->language_id) ? $request->language_id : "";

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($language_id) {
                $lang_id = explode(",", $language_id);
                $data = Song::where('city_id', $city_id)->whereIn('language_id', $lang_id)->where('status', 1)->orderBy('id', "DESC");
            } else {
                $data = Song::where('city_id', $city_id)->where('status', 1)->orderBy('id', "DESC");
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);

                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_radio_by_artist(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'artist_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $artist_id = $request->artist_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $language_id = isset($request->language_id) ? $request->language_id : "";

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($language_id) {
                $lang_id = explode(",", $language_id);
                $data = Song::where('artist_id', $artist_id)->whereIn('language_id', $lang_id)->where('status', 1)->orderBy('id', "DESC");
            } else {
                $data = Song::where('artist_id', $artist_id)->where('status', 1)->orderBy('id', "DESC");
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value['is_follow'] = $this->common->is_follow($user_id, $value['artist_id']);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_radio_by_language(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'language_id' => 'required',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $language_id = isset($request->language_id) ? $request->language_id : "";

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($language_id) {
                $lang_id = explode(",", $language_id);
                $data = Song::whereIn('language_id', $lang_id)->where('status', 1)->orderBy('id', "DESC");
            } else {
                $data = Song::where('status', 1)->orderBy('id', "DESC");
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_radio_by_category(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'category_id' => 'required',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $category_id = $request->category_id;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $language_id = isset($request->language_id) ? $request->language_id : "";

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($language_id) {
                $lang_id = explode(",", $language_id);
                $data = Song::where('category_id', $category_id)->whereIn('language_id', $lang_id)->where('status', 1)->orderBy('id', "DESC");
            } else {
                $data = Song::where('category_id', $category_id)->where('status', 1)->orderBy('id', 'DESC');
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_latest_song(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $language_id = isset($request->language_id) ? $request->language_id : "";

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($language_id) {
                $lang_id = explode(",", $language_id);
                $data = Song::whereIn('language_id', $lang_id)->where('status', 1)->orderBy('id', "DESC");
            } else {
                $data = Song::where('status', 1)->orderBy('id', "DESC");
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_popular_song(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Song::where('status', 1)->orderBy('total_play', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {
                    $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_transaction(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'package_id' => 'required|numeric',
                    'price' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $package_id = $request->package_id;
            $price = $request->price;
            $transaction_id = isset($request->transaction_id) ? $request->transaction_id : "";
            $description = isset($request->description) ? $request->description : "";

            $Pdata = Package::where('id', $package_id)->where('status', '1')->first();
            if (!empty($Pdata)) {
                $Edate = date("Y-m-d H:i", strtotime('+' . $Pdata->time . ' ' . strtolower($Pdata->type), time()));
            } else {
                return $this->common->API_Response(400, __('api_msg.please_enter_right_package_id'));
            }

            $insert = new Transaction();
            $insert->user_id = $user_id;
            $insert->package_id = $package_id;
            $insert->price = $price;
            $insert->description = $description;
            $insert->transaction_id = $transaction_id;
            $insert->expiry_date = $Edate;
            $insert->status = 1;
            if ($insert->save()) {

                $transactions = Transaction::where('user_id', $user_id)->where('id', "!=", $insert->id)->get();
                foreach ($transactions as $data) {
                    $data->update(['status' => 0]);
                }
                // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                $user_data = User::where('id', $user_id)->first();
                if (isset($user_data)) {
                    $check = $this->common->basic_notification_configuration('package-buy');

                    if ($check['status'] == 1 && $check['send_mail'] == 1) {
                        $this->common->Send_Mail(2, $user_data->email, $user_data->full_name, $Pdata->name, $price, $transaction_id, $Edate);
                    }

                    if ($check['status'] == 1 && $check['send_notification'] == 1) {
                        $title = __('api_msg.package_purchase');
                        $message = __('api_msg.package_buy_msg');
                        $this->common->send_push_notification($user_data['device_type'], $user_data['device_token'], $title, $message);
                    }
                }

                return $this->common->API_Response(200, __('api_msg.transaction_add_successfully'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function transaction_list(Request $request)
    {
        try {

            $this->common->package_expiry();

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $result = Transaction::where('user_id', $user_id)->with('package')->latest()->get();

            if (count($result) > 0) {

                foreach ($result as $key => $value) {
                    if ($value['package'] != null) {
                        $value['package_name'] = $value['package']['name'];
                        $value['package_price'] = $value['package']['price'];
                    } else {
                        $value['package_name'] = "";
                        $value['package_price'] = 0;
                    }

                    $value['buy_date'] = $value['created_at']->format('Y-m-d H:i');

                    unset($value['package']);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_favorite(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'type' => 'required|numeric',
                    'user_id' => 'required|numeric',
                    'content_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $contet_id = $request->content_id;
            $type = $request->type;

            $favorites = Favorite::where('type', $type)->where('content_id', $contet_id)->where('user_id', $user_id)->first();

            if (isset($favorites) && $favorites != null) {

                $favorites->delete();
                return $this->common->API_Response(200, __('api_msg.remove_to_favorite'), []);
            } else {

                $insert = [
                    'type' => $type,
                    'user_id' => $user_id,
                    'content_id' => $contet_id,
                ];
                Favorite::insertGetId($insert);
                return $this->common->API_Response(200, __('api_msg.add_to_favorite'), []);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_favorite_list(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'type' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Favorite::where('type', 1)->where('user_id', $user_id)->with('song')->orderBy('id', 'DESC');
            } else if ($type == 2) {
                $data = Favorite::where('type', 2)->where('user_id', $user_id)->with('podcast')->orderBy('id', 'DESC');
            } else if ($type == 3) {
                $data = Favorite::where('type', 3)->where('user_id', $user_id)->with('music')->orderBy('id', 'DESC');
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $result = [];
                foreach ($data as $key => $item) {

                    if ($item['type'] == 1 && !empty($item['song'])) {
                        if ($item['song']['status'] == 1) {

                            $item['song']['image'] = $this->common->Get_Image($this->folder_song, $item['song']['image']);
                            if ($item['song']['upload_type'] == 1) {
                                $item['song']['song_url'] = $this->common->Get_Song($this->folder_song, $item['song']['song_url']);
                            }
                            $item['song']['is_favorite'] = $this->common->isFavorite(1, $item['song']['id'], $user_id);
                            $item['song']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $this->common->get_all_count_for_content(1, $item['song']);
                            $this->common->getAllIdByName(array($item['song']));

                            $result[] = $item['song'];
                        }
                    } elseif ($item['type'] == 2 && !empty($item['podcast'])) {

                        if ($item['podcast']['status'] == 1) {

                            $item['podcast']['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $item['podcast']['portrait_img']);
                            $item['podcast']['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $item['podcast']['landscape_img']);

                            if ($item['podcast']['trailer_upload_type'] == 1) {
                                $item['podcast']['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $item['podcast']['trailer_audio']);
                            }
                            $item['podcast']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['podcast']['is_favorite'] = $this->common->isFavorite(2, $item['podcast']['id'], $user_id);
                            $this->common->get_all_count_for_content(2, $item['podcast']);
                            $this->common->getAllIdByName(array($item['podcast']));

                            $result[] = $item['podcast'];
                        }
                    } elseif ($item['type'] == 3 && !empty($item['music'])) {

                        if ($item['music']['status'] == 1) {

                            if ($item['music']['upload_type'] == 1) {
                                $item['music']['music'] = $this->common->Get_Song($this->folder_music, $item['music']['music']);
                            }
                            $item['music']['portrait_img'] = $this->common->Get_Image($this->folder_music, $item['music']['portrait_img']);
                            $item['music']['landscape_img'] = $this->common->Get_Image($this->folder_music, $item['music']['landscape_img']);
                            $item['music']['ogtag_img'] = $this->common->Get_Image($this->folder_music, $item['music']['ogtag_img']);

                            $item['music']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['music']['is_favorite'] = $this->common->isFavorite(3, $item['music']['id'], $user_id);
                            $this->common->get_all_count_for_content(3, $item['music']);
                            $this->common->getAllIdByName(array($item['music']));

                            $result[] = $item['music'];
                        }
                    }
                }
                if (count($result) > 0) {
                    return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function search_content(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'type' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $name = isset($request->name) ? $request->name : "";
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Song::where('status', 1)->orderBy('id', "DESC");
            } else if ($type == 2) {
                $data = Podcast::where('status', 1)->orderBy('id', "DESC");
            } else if ($type == 3) {
                $data = Live_Event::where('status', 1)->orderBy('id', "DESC");
            } else if ($type == 4) {
                $data = Music::where('status', 1)->orderBy('id', "DESC");
            }

            if ($type == 1 && !empty($name)) {
                $data->where('name', 'LIKE', "%{$name}%");
            } elseif (($type == 2 || $type == 3 || $type == 4) && !empty($name)) {
                $data->where('title', 'LIKE', "%{$name}%");
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->getAllIdByName($data);
                if ($type == 1) {

                    $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                    $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $value);
                    }
                } else if ($type == 2) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                    foreach ($data as $key => $value) {
                        if ($value['trailer_upload_type'] == 1) {
                            $value['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $value['trailer_audio']);
                        }
                        $value['is_favorite'] = $this->common->isFavorite(2, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $value['is_follow'] = $this->common->is_follow($user_id, $value['artist_id']);
                        $this->common->get_all_count_for_content(2, $value);
                    }
                } else if ($type == 3) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_live_event);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_live_event);
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]['is_join'] = 0;
                        $check = Event_Join_User::where('user_id', $user_id)->where('live_event_id', $data[$i]['id'])->where('status', 1)->first();
                        if (isset($check) && $check != null) {
                            $data[$i]['is_join'] = 1;
                        }
                    }
                } else if ($type == 4) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'ogtag_img', $this->folder_music);
                    foreach ($data as $key => $value) {
                        if ($value['upload_type'] == 1) {
                            $value['music'] = $this->common->Get_Song($this->folder_music, $value['music']);
                        }
                        $value['is_favorite'] = $this->common->isFavorite(3, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(3, $value);
                    }
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_latest_podcast(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Podcast::where('status', 1)->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {

                    if ($value['trailer_upload_type'] == 1) {
                        $value['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $value['trailer_audio']);
                    }
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value['is_favorite'] = $this->common->isFavorite(2, $value['id'], $user_id);
                    $this->common->get_all_count_for_content(2, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_popular_podcast(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Podcast::where('status', 1)->orderBy('total_play', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                $this->common->getAllIdByName($data);
                foreach ($data as $key => $value) {

                    if ($value['trailer_upload_type'] == 1) {
                        $value['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $value['trailer_audio']);
                    }
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value['is_favorite'] = $this->common->isFavorite(2, $value['id'], $user_id);
                    $this->common->get_all_count_for_content(2, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_episode_by_podcast(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'podcast_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $podcast_id = $request['podcast_id'];
            $podcast = Podcast::where('id', $podcast_id)->where('status', 1)->first();
            if ($podcast != null) {
                $data = Episode::where('podcasts_id', $podcast_id)->orderBy('id', 'DESC');
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]['episode_upload_type'] == 1) {
                        $data[$i]['episode_audio'] =  $this->common->Get_Song($this->folder_podcast, $data[$i]['episode_audio']);
                    }
                    $data[$i]['podcast_title'] = $this->common->GetPodcastNameByIds($data[$i]['podcasts_id']);
                    $data[$i]['total_comment'] = Comment::where('type', 2)->where('content_id', $podcast_id)->where('episode_id', $data[$i]['id'])->where('status', 1)->count();
                    $data[$i]['category_id'] = $podcast['category_id'];
                    $data[$i]['language_id'] = $podcast['language_id'];
                    $data[$i]['artist_id'] = $podcast['artist_id'];
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_live_event(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Live_Event::where('status', 1)->orderBy('id', 'DESC');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_live_event);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_live_event);

                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['is_join'] = 0;
                    $check = Event_Join_User::where('user_id', $user_id)->where('live_event_id', $data[$i]['id'])->where('status', 1)->first();
                    if (isset($check) && $check != null) {
                        $data[$i]['is_join'] = 1;
                    }
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_podcast_banner(Request $request)
    {
        try {

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Banner::where('type', 2)->with('podcast')->orderBy('id', 'DESC')->get();

            $result = [];
            foreach ($data as $key => $value) {
                if ($value['podcast'] != null) {
                    $result[] = $value['podcast'];
                }
            }

            $this->common->imageNameToUrl($result, 'portrait_img', $this->folder_podcast);
            $this->common->imageNameToUrl($result, 'landscape_img', $this->folder_podcast);

            foreach ($result as $key => $value) {

                if ($value['trailer_upload_type'] == 1) {
                    $value['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $value['trailer_audio']);
                }
                $value['is_buy'] = $this->common->is_any_package_buy($user_id);
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function join_live_event(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'live_event_id' => 'required|numeric',
                    'type' => 'required|numeric',
                    'price' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $live_event_id = $request->live_event_id;
            $type = $request->type;
            $price = $request->price;
            $transaction_id = isset($request->transaction_id) ? $request->transaction_id : "";
            $description = isset($request->description) ? $request->description : "";

            $insert = new Event_Join_User();
            $insert->user_id = $user_id;
            $insert->live_event_id = $live_event_id;
            $insert->type = $type;
            $insert->transaction_id = $transaction_id;
            $insert->price = $price;
            $insert->description = $description;
            $insert->status = 1;
            if ($insert->save()) {
                return $this->common->API_Response(200, __('api_msg.transaction_add_successfully'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_section_list(Request $request) // type = 1-Song, 2-Podcat, 3-Live Event, 4-Artist, 5-Category, 6-Language, 7-City, 8-Music
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'section_type' => 'required',
                ]
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $this->common->update_liveevent_status();

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $section_type = $request->section_type;

            $data = Section::whereIn('user_id', [0, $user_id])->where('section_type', $section_type)->where('status', 1)->orderByRaw('user_id=? DESC', [$user_id])->orderBy('sortable', 'asc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['data'] = [];
                    if ($data[$i]['type'] == 1 || $data[$i]['type'] == 2 || $data[$i]['type'] == 8) {

                        $query = $this->common->section_query($user_id, $data[$i]['type'], $data[$i]['artist_id'], $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['city_id'], $data[$i]['order_by_upload'], $data[$i]['order_by_play'], $data[$i]['is_premium'], $data[$i]['no_of_content']);

                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['type'] == 3) {

                        $content = Live_Event::where('status', 1);

                        if ($data[$i]['order_by_upload'] == 1) {
                            $content->orderBy('id', 'desc');
                        } else {
                            $content->orderBy('id', 'asc');
                        }

                        if ($data[$i]['is_paid'] == 0) {
                            $content->where('is_paid', 0);
                        } else if ($data[$i]['is_paid'] == 1) {
                            $content->where('is_paid', 1);
                        }
                        $query = $content->take($data[$i]['no_of_content'])->get();

                        $this->common->imageNameToUrl($query, 'portrait_img', $this->folder_live_event);
                        $this->common->imageNameToUrl($query, 'landscape_img', $this->folder_live_event);

                        for ($j = 0; $j < count($query); $j++) {
                            $query[$j]['is_join'] = 0;
                            $check = Event_Join_User::where('user_id', $user_id)->where('live_event_id', $query[$j]['id'])->where('status', 1)->first();
                            if (isset($check) && $check != null) {

                                $query[$j]['is_join'] = 1;
                            }
                        }

                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['type'] == 4) {

                        if ($section_type == 2) {
                            $content = Artist::where('type', 3)->where('status', 1);
                        } else if ($section_type == 3) {
                            $content = Artist::where('type', 1)->where('status', 1);
                        } else if ($section_type == 4) {
                            $content = Artist::where('type', 2)->where('status', 1);
                        } else {
                            $content = Artist::where('status', 1);
                        }

                        if ($data[$i]['order_by_upload'] == 1) {
                            $content->orderBy('id', 'desc');
                        } else {
                            $content->orderBy('id', 'asc');
                        }

                        $data[$i]['data'] = $content->take($data[$i]['no_of_content'])->get();
                        $this->common->imageNameToUrl($data[$i]['data'], 'image', $this->folder_artist);
                    } else if ($data[$i]['type'] == 5) {

                        $content = Category::where('status', 1);
                        if ($data[$i]['order_by_upload'] == 1) {
                            $content->orderBy('id', 'desc');
                        } else {
                            $content->orderBy('id', 'asc');
                        }

                        $data[$i]['data'] = $content->take($data[$i]['no_of_content'])->get();
                        $this->common->imageNameToUrl($data[$i]['data'], 'image', $this->folder_category);
                    } else if ($data[$i]['type'] == 6) {

                        $content = Language::where('status', 1);

                        if ($data[$i]['order_by_upload'] == 1) {
                            $content->orderBy('id', 'desc');
                        } else {
                            $content->orderBy('id', 'asc');
                        }

                        $data[$i]['data'] = $content->take($data[$i]['no_of_content'])->get();
                        $this->common->imageNameToUrl($data[$i]['data'], 'image', $this->folder_language);
                    } else if ($data[$i]['type'] == 7) {

                        $content = City::where('status', 1);

                        if ($data[$i]['order_by_upload'] == 1) {
                            $content->orderBy('id', 'desc');
                        } else {
                            $content->orderBy('id', 'asc');
                        }

                        $data[$i]['data'] = $content->take($data[$i]['no_of_content'])->get();
                        $this->common->imageNameToUrl($data[$i]['data'], 'image', $this->folder_city);
                    }
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_section_detail(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'section_id' => 'required|numeric',
                    'user_id' => 'numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $section_id = $request['section_id'];
            $user_id = isset($request['user_id']) ? $request['user_id'] : 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            $section = Section::where('id', $section_id)->first();
            if ($section != null && isset($section)) {

                if ($section['type'] == 1 || $section['type'] == 2 || $section['type'] == 8) {
                    $content = $this->common->section_query_detail($section['type'], $section['artist_id'], $section['category_id'], $section['language_id'], $section['city_id'], $section['order_by_upload'], $section['order_by_play'], $section['is_premium']);
                    $data = $content;
                } else if ($section['type'] == 3) {

                    $content = Live_Event::where('status', 1);

                    if ($section['order_by_upload'] == 1) {
                        $content->orderBy('id', 'desc');
                    } else {
                        $content->orderBy('id', 'asc');
                    }

                    if ($section['is_paid'] == 0) {
                        $content->where('is_paid', 0);
                    } else if ($section['is_paid'] == 1) {
                        $content->where('is_paid', 1);
                    }

                    $data = $content;
                } else if ($section['type'] == 4 || $section['type'] == 5 || $section['type'] == 6 || $section['type'] == 7) {

                    if ($section['type'] == 4) {

                        if ($section['section_type'] == 2) {
                            $content = Artist::where('type', 3)->where('status', 1);
                        } else if ($section['section_type'] == 3) {
                            $content = Artist::where('type', 1)->where('status', 1);
                        } else if ($section['section_type'] == 4) {
                            $content = Artist::where('type', 2)->where('status', 1);
                        } else {
                            $content = Artist::where('status', 1);
                        }
                    } else if ($section['type'] == 5) {

                        $content = Category::where('status', 1);
                    } else if ($section['type'] == 6) {

                        $content = Language::where('status', 1);
                    } else if ($section['type'] == 7) {

                        $content = City::where('status', 1);
                    }

                    if ($section['order_by_upload'] == 1) {
                        $content->orderBy('id', 'desc');
                    } else {
                        $content->orderBy('id', 'asc');
                    }

                    $data = $content;
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $offset = $page_no * $total_page - $total_page;

            $more_page = $this->common->more_page($page_no, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $page_no, $more_page);

            $data->take($total_page)->skip($offset);
            $data = $data->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {
                    if ($section['type'] == 1) {

                        $data[$i]['image'] = $this->common->Get_Image($this->folder_song, $data[$i]['image']);
                        if ($data[$i]['upload_type'] == 1) {
                            $data[$i]['song_url'] = $this->common->Get_Song($this->folder_song, $data[$i]['song_url']);
                        }
                        $data[$i]['is_favorite'] = $this->common->isFavorite(1, $data[$i]['id'], $user_id);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $data[$i]);
                        $this->common->getAllIdByName(array($data[$i]));
                    } else if ($section['type'] == 2) {

                        $data[$i]['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['portrait_img']);
                        $data[$i]['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['landscape_img']);
                        if ($data[$i]['trailer_upload_type'] == 1) {
                            $data[$i]['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $data[$i]['trailer_audio']);
                        }
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['is_favorite'] = $this->common->isFavorite(2, $data[$i]['id'], $user_id);
                        $data[$i]['is_follow'] = $this->common->is_follow($user_id, $data[$i]['artist_id']);
                        $this->common->get_all_count_for_content(2, $data[$i]);
                        $this->common->getAllIdByName(array($data[$i]));
                    } else if ($section['type'] == 3) {

                        $data[$i]['portrait_img'] = $this->common->Get_Image($this->folder_live_event, $data[$i]['portrait_img']);
                        $data[$i]['landscape_img'] = $this->common->Get_Image($this->folder_live_event, $data[$i]['landscape_img']);

                        $data[$i]['is_join'] = 0;
                        $check = Event_Join_User::where('user_id', $user_id)->where('live_event_id', $data[$i]['id'])->where('status', 1)->first();
                        if (isset($check) && $check != null) {
                            $data[$i]['is_join'] = 1;
                        }
                    } else if ($section['type'] == 4) {

                        $data[$i]['image'] = $this->common->Get_Image($this->folder_artist, $data[$i]['image']);
                    } else if ($section['type'] == 5) {

                        $data[$i]['image'] = $this->common->Get_Image($this->folder_category, $data[$i]['image']);
                    } else if ($section['type'] == 6) {

                        $data[$i]['image'] = $this->common->Get_Image($this->folder_language, $data[$i]['image']);
                    } else if ($section['type'] == 7) {

                        $data[$i]['image'] = $this->common->Get_Image($this->folder_city, $data[$i]['image']);
                    } else if ($section['type'] == 8) {

                        if ($data[$i]['upload_type'] == 1) {
                            $data[$i]['music'] = $this->common->Get_Song($this->folder_music, $data[$i]['music']);
                        }
                        $data[$i]['portrait_img'] = $this->common->Get_Image($this->folder_music, $data[$i]['portrait_img']);
                        $data[$i]['landscape_img'] = $this->common->Get_Image($this->folder_music, $data[$i]['landscape_img']);
                        $data[$i]['ogtag_img'] = $this->common->Get_Image($this->folder_music, $data[$i]['ogtag_img']);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['is_favorite'] = $this->common->isFavorite(3, $data[$i]['id'], $user_id);
                        $this->common->get_all_count_for_content(3, $data[$i]);
                        $this->common->getAllIdByName(array($data[$i]));
                    }
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_podcast_section_list(Request $request)
    {
        try {
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            // JAILAOI: Podcast tab (section_type=4). Any content type allowed.
            $data = Section::whereIn('user_id', [0, $user_id])->where('section_type', 4)->where('status', 1)->orderByRaw('user_id=? DESC', [$user_id])->orderBy('sortable', 'asc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['data'] = [];
                    $query = $this->common->section_query($user_id, $data[$i]['type'], $data[$i]['artist_id'], $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['city_id'], $data[$i]['order_by_upload'], $data[$i]['order_by_play'], $data[$i]['is_premium'], $data[$i]['no_of_content']);
                    $data[$i]['data'] = $query;
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_podcast_section_detail(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'section_id' => 'required|numeric',
                    'user_id' => 'numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $section_id = $request['section_id'];
            $user_id = isset($request['user_id']) ? $request['user_id'] : 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            $section = Section::where('id', $section_id)->first();
            if ($section != null && isset($section)) {
                if ($section['type'] == 2) {
                    $content = $this->common->section_query_detail($section['type'], $section['artist_id'], $section['category_id'], $section['language_id'], $section['city_id'], $section['order_by_upload'], $section['order_by_play'], $section['is_premium']);
                    $data = $content;
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $offset = $page_no * $total_page - $total_page;

            $more_page = $this->common->more_page($page_no, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $page_no, $more_page);

            $data->take($total_page)->skip($offset);
            $data = $data->get();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['portrait_img']);
                    $data[$i]['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['landscape_img']);
                    if ($data[$i]['trailer_upload_type'] == 1) {
                        $data[$i]['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $data[$i]['trailer_audio']);
                    }
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['is_favorite'] = $this->common->isFavorite(2, $data[$i]['id'], $user_id);
                    $this->common->get_all_count_for_content(2, $data[$i]);
                    $this->common->getAllIdByName(array($data[$i]));
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_banner(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;
            
            $type = $request->type ?? 0;
            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Banner::with('song', 'podcast', 'music')->orderBy('id', 'desc');

            if (!empty($type)) {
                $data->where('type', $type);
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {
                $result = [];
                foreach ($data as &$item) {
                    if ($item['type'] == 1 && !empty($item['song'])) {
                        if ($item['song']['status'] == 1) {
                            $item['song']['image'] = $this->common->Get_Image($this->folder_song, $item['song']['image']);
                            if ($item['song']['upload_type'] == 1) {
                                $item['song']['song_url'] = $this->common->Get_Song($this->folder_song, $item['song']['song_url']);
                            }
                            $item['song']['is_favorite'] = $this->common->isFavorite(1, $item['song']['id'], $user_id);
                            $item['song']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $this->common->get_all_count_for_content(1, $item['song']);
                            $item['song']['type'] = 1;

                            $result[] = $item['song'];
                        }
                    } elseif ($item['type'] == 2 && !empty($item['podcast'])) {
                        if ($item['podcast']['status'] == 1) {
                            $item['podcast']['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $item['podcast']['portrait_img']);
                            $item['podcast']['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $item['podcast']['landscape_img']);

                            if ($item['podcast']['trailer_upload_type'] == 1) {
                                $item['podcast']['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $item['podcast']['trailer_audio']);
                            }

                            $item['podcast']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['podcast']['is_favorite'] = $this->common->isFavorite(2, $item['podcast']['id'], $user_id);
                            $this->common->get_all_count_for_content(2, $item['podcast']);
                            $item['podcast']['type'] = 2;

                            $result[] = $item['podcast'];
                        }
                    } elseif ($item['type'] == 3 && !empty($item['music'])) {
                        if ($item['music']['status'] == 1) {
                            if ($item['music']['upload_type'] == 1) {
                                $item['music']['music'] = $this->common->Get_Song($this->folder_music, $item['music']['music']);
                            }
                            $item['music']['portrait_img'] = $this->common->Get_Image($this->folder_music, $item['music']['portrait_img']);
                            $item['music']['landscape_img'] = $this->common->Get_Image($this->folder_music, $item['music']['landscape_img']);
                            $item['music']['ogtag_img'] = $this->common->Get_Image($this->folder_music, $item['music']['ogtag_img']);

                            $item['music']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['music']['is_favorite'] = $this->common->isFavorite(3, $item['music']['id'], $user_id);
                            $this->common->get_all_count_for_content(3, $item['music']);
                            $item['music']['type'] = 3;

                            $result[] = $item['music'];
                        }
                    }
                    unset($item['song'], $item['podcast'], $item['music']);
                }

                if (count($result) > 0) {
                    $this->common->getAllIdByName($result);
                    return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_notification(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $user_notification_id = User_Notification_Tracking::where('user_id', $user_id)->get();

            $NotiIds = [];
            foreach ($user_notification_id as $key => $value) {
                $NotiIds[] = $value['notification_id'];
            }

            $result = Notification::whereNotIn('id', $NotiIds)->orderBy('id', 'desc')->get();

            $this->common->imageNameToUrl($result, 'image', $this->folder_notification);

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function read_notification(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'notification_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $notification_id = $request['notification_id'];

            $noti = new User_Notification_Tracking();
            $noti->user_id = $user_id;
            $noti->notification_id = $notification_id;
            if ($noti->save()) {
                return $this->common->API_Response(200, __('api_msg.notification_read_successfully'));
            } else {
                return $this->common->API_Response(400,  __('api_msg.notification_not_read'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_play(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'content_id' => 'required|numeric',
                    'type' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            if ($request->type == 2) {
                $validation1 = Validator::make(
                    $request->all(),
                    [
                        'episode_id' => 'required|numeric',
                    ],
                );
                if ($validation1->fails()) {
                    return $this->common->API_Response(400, $validation1->errors()->first());
                }
            }

            $user_id = $request->user_id;
            $content_id = $request->content_id;
            $type = $request->type;
            $episode_id = isset($request->episode_id) ? $request->episode_id : 0;

            $existingplay = Play::where('type', $type)->where('user_id', $user_id)->where('content_id', $content_id)->where('episode_id', $episode_id)->first();

            if (!isset($existingplay->id)) {

                $SaveData = new Play();
                $SaveData->type = $type;
                $SaveData->user_id = $user_id;
                $SaveData->content_id = $content_id;
                $SaveData->episode_id = $episode_id;
                $SaveData->status = 1;

                if ($SaveData->save()) {
                    if ($type == 1) {

                        Song::where('id', $content_id)->increment('total_play');
                        $this->creditArtistEarning($type, $content_id, $user_id);
                    } else if ($type == 2) {

                        Podcast::where('id', $content_id)->increment('total_play');
                        Episode::where('podcasts_id', $content_id)->where('id', $episode_id)->increment('total_play');
                    } else  if ($type == 3) {

                        Music::where('id', $content_id)->increment('total_play');
                        $this->creditArtistEarning($type, $content_id, $user_id);
                    }
                    return $this->common->API_Response(200, __('api_msg.play_added'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.previously_played'));
            }
            return $this->common->API_Response(200, __('api_msg.song_play_successfully'), []);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_comment(Request $request)
    {
        try {

            if ($request->type == 1 || $request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'content_id' => 'required|numeric',
                        'comment' => 'required',
                    ],
                );
            } elseif ($request->type == 2) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'content_id' => 'required|numeric',
                        'episode_id' => 'required|numeric',
                        'comment' => 'required',
                    ],
                );
            } else {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                );
            }

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $type = $request->type;
            $user_id = $request->user_id;
            $content_id = isset($request->content_id) ? $request->content_id : 0;
            $episode_id = isset($request->episode_id) ? $request->episode_id : 0;
            $comment = $request->comment;

            $insert = new Comment();
            $insert['type'] = $type;
            $insert['user_id'] = $user_id;
            $insert['content_id'] = $content_id;
            $insert['episode_id'] = $episode_id;
            $insert['comment'] = $comment;
            $insert->save();

            return $this->common->API_Response(200, __('api_msg.comment_add_successfully'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_comment(Request $request)
    {
        try {

            if ($request->type == 1 || $request->type == 3) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'content_id' => 'required|numeric',
                    ],
                );
            } elseif ($request->type == 2) {

                $validation = Validator::make(
                    $request->all(),
                    [

                        'content_id' => 'required|numeric',
                        'episode_id' => 'required|numeric',
                    ],
                );
            } else {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                );
            }

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $type = $request->type;
            $content_id = $request->content_id;
            $episode_id = $request->episode_id;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Comment::where('content_id', $content_id)->where('type', 1)->where('status', 1)->with('user')->orderBy('id', 'desc');
            } else if ($type == 2) {
                $data = Comment::where('content_id', $content_id)->where('episode_id', $episode_id)->where('type', 2)->where('status', 1)->with('user')->orderBy('id', 'desc');
            } else if ($type == 3) {
                $data = Comment::where('content_id', $content_id)->where('type', 3)->where('status', 1)->with('user')->orderBy('id', 'desc');
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['user_name'] = "";
                    $data[$i]['full_name'] = "";
                    $data[$i]['email'] = "";
                    $data[$i]['mobile_number'] = "";
                    $data[$i]['image'] = asset('assets/imgs/default.png');
                    if ($data[$i]['user'] != null) {

                        $data[$i]['user_name'] = $data[$i]['user']['user_name'];
                        $data[$i]['full_name'] = $data[$i]['user']['full_name'];
                        $data[$i]['email'] = $data[$i]['user']['email'];
                        $data[$i]['mobile_number'] = $data[$i]['user']['mobile_number'];
                        $data[$i]['image'] = $this->common->Get_Image($this->folder_user, $data[$i]['user']['image']);
                    }
                    unset($data[$i]['user']);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit_comment(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|numeric',
                    'comment_id' => 'required|numeric',
                    'comment' => 'required',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $id = $request->comment_id;
            $comment = $request->comment;

            $update = Comment::where('id', $id)->where('user_id', $user_id)->first();
            if (isset($update) && $update != null) {

                $update['comment'] = $comment;
                $update->save();
                return $this->common->API_Response(200, __('api_msg.comment_edit_successfully'));
            }
            return $this->common->API_Response(200, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_comment(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'comment_id' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'];
            Comment::where('id', $comment_id)->delete();
            return $this->common->API_Response(200, __('api_msg.comment_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_related_data(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'content_id' => 'required',
                    'type' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_id = isset($request->content_id) ? $request->content_id : "";
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {

                $category_id = Song::where('id', $content_id)->value('category_id');
                if (!$category_id) {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
                $data = Song::where('id', '!=', $content_id)->where('category_id', $category_id)->orderByDesc('total_play');
            } else if ($type == 2) {

                $category_id = Podcast::where('id', $content_id)->value('category_id');
                if (!$category_id) {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
                $data = Podcast::where('id', '!=', $content_id)->where('category_id', $category_id)->orderByDesc('total_play');
            } else if ($type == 3) {

                $category_id = Music::where('id', $content_id)->value('category_id');
                if (!$category_id) {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
                $data = Music::where('id', '!=', $content_id)->where('category_id', $category_id)->orderByDesc('total_play');
            }
            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? $request->pageno ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->getAllIdByName($data);
                if ($type == 1) {

                    $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                    $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $value);
                    }
                } else if ($type == 2) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(2, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $value['is_follow'] = $this->common->is_follow($user_id, $value['artist_id']);
                        $this->common->get_all_count_for_content(2, $value);
                    }
                } else if ($type == 3) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'ogtag_img', $this->folder_music);
                    foreach ($data as $key => $value) {
                        if ($value['upload_type'] == 1) {
                            $value['music'] = $this->common->Get_Song($this->folder_music, $value['music']);
                        }
                        $value['is_favorite'] = $this->common->isFavorite(3, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(3, $value);
                    }
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_content_by_artist(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'artist_id' => 'required',
                    'type' => 'required|numeric',
                ],
            );
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $artist_id = isset($request->artist_id) ? $request->artist_id : 0;
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Song::where('artist_id', $artist_id)->orderByDesc('total_play');
            } else if ($type == 2) {
                $data = Podcast::where('artist_id', $artist_id)->orderByDesc('total_play');
            } else if ($type == 3) {
                $data = Music::whereRaw("FIND_IN_SET(?,artist_id)", $artist_id)->orderByDesc('total_play');
            } else {
                $songCount = Song::where('artist_id', $artist_id)->count();
                $podcastCount = Podcast::where('artist_id', $artist_id)->count();
                $musicCount = Music::whereRaw("FIND_IN_SET(?,artist_id)", $artist_id)->count();
                if ($songCount > 0) {
                    $type = 1;
                    $data = Song::where('artist_id', $artist_id)->orderByDesc('total_play');
                } else if ($podcastCount > 0) {
                    $type = 2;
                    $data = Podcast::where('artist_id', $artist_id)->orderByDesc('total_play');
                } else if ($musicCount > 0) {
                    $type = 3;
                    $data = Music::whereRaw("FIND_IN_SET(?,artist_id)", $artist_id)->orderByDesc('total_play');
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            if (count($data) > 0) {

                $this->common->getAllIdByName($data);
                if ($type == 1) {
                    $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                    $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                    $this->common->getAllIdByName($data);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $value['is_follow'] = $this->common->is_follow($user_id, $artist_id);
                        $this->common->get_all_count_for_content(1, $value);
                    }
                } else if ($type == 2) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                    foreach ($data as $key => $value) {
                        if ($value['trailer_upload_type'] == 1) {
                            $value['trailer_audio'] = $this->common->Get_Song($this->folder_podcast, $value['trailer_audio']);
                        }
                        $value['is_favorite'] = $this->common->isFavorite(2, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $value['is_follow'] = $this->common->is_follow($user_id, $artist_id);
                        $this->common->get_all_count_for_content(2, $value);
                    }
                } else if ($type == 3) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_music);
                    $this->common->imageNameToUrl($data, 'ogtag_img', $this->folder_music);
                    foreach ($data as $key => $value) {
                        if ($value['upload_type'] == 1) {
                            $value['music'] = $this->common->Get_Song($this->folder_music, $value['music']);
                        }
                        $value['is_favorite'] = $this->common->isFavorite(3, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $value['is_follow'] = $this->common->is_follow($user_id, $artist_id);

                        $this->common->get_all_count_for_content(3, $value);
                    }
                }

                $response = $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
                $response['content_type'] = $type;
                return $response;
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_follow(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'artist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $artist_id = $request['artist_id'];

            $follow = Subscriber::where('user_id', $user_id)->where('to_user_id', $artist_id)->first();
            if ($follow) {

                Subscriber::where('id', $follow['id'])->delete();
                return $this->common->API_Response(200, __('api_msg.unfollow_successfully'));
            } else {

                $insert['user_id'] = $user_id;
                $insert['to_user_id'] = $artist_id;
                Subscriber::insertGetId($insert);

                return $this->common->API_Response(200, __('api_msg.follow_successfully'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_user_action(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required',
                    'content_type' => 'required',
                    'content_id' => 'required',
                    'action' => 'required',
                ]
            );

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request->user_id;
            $content_type = $request->content_type;
            $content_id = $request->content_id;
            $action = $request->action;
            $time_spend = $request->time_spend ?? 0;
            $category_id = $request->category_id ?? 0;
            $language_id = $request->language_id ?? 0;
            $city_id = $request->city_id ?? 0;
            $artist_id = $request->artist_id ?? 0;
            $content_duration = $request->content_duration ?? 0;

            $history = User_Action::create([
                'user_id' => $user_id,
                'content_type' => $content_type,
                'content_id' => $content_id,
                'category_id' => $category_id,
                'language_id' => $language_id,
                'city_id' => $city_id,
                'artist_id' => $artist_id,
                'action' => $action,
                'time_spend' => $time_spend,
                'content_duration' => $content_duration,
                'status' => 1,
            ]);

            // JAILAOI: Credit per-stream artist earnings on play action for Song/Music
            if ((int) $action === 1 && in_array((int) $content_type, [1, 8])) {
                $this->creditArtistEarning((int) $content_type, (int) $content_id, (int) $user_id);
            }

            return $this->common->API_Response(200, __('api_msg.history_add'), [$history]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // JAILAOI: Radio tab sections (section_type=3). Any content type allowed.
    public function get_radio_section_list(Request $request)
    {
        try {
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $data = Section::whereIn('user_id', [0, $user_id])
                ->where('section_type', 3)->where('status', 1)
                ->orderByRaw('user_id=? DESC', [$user_id])->orderBy('sortable', 'asc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / max($total_page, 1));
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['data'] = [];
                    $query = $this->common->section_query($user_id, $data[$i]['type'], $data[$i]['artist_id'], $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['city_id'], $data[$i]['order_by_upload'], $data[$i]['order_by_play'], $data[$i]['is_premium'], $data[$i]['no_of_content']);
                    $data[$i]['data'] = $query;
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // JAILAOI: Music tab sections (section_type=2). Any content type allowed.
    public function get_music_section_list(Request $request)
    {
        try {
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $data = Section::whereIn('user_id', [0, $user_id])
                ->where('section_type', 2)->where('status', 1)
                ->orderByRaw('user_id=? DESC', [$user_id])->orderBy('sortable', 'asc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / max($total_page, 1));
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;
            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['data'] = [];
                    $query = $this->common->section_query($user_id, $data[$i]['type'], $data[$i]['artist_id'], $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['city_id'], $data[$i]['order_by_upload'], $data[$i]['order_by_play'], $data[$i]['is_premium'], $data[$i]['no_of_content']);
                    $data[$i]['data'] = $query;
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    // JAILAOI: Credit per-stream earnings to artist(s)
    // Called from add_play (type 1=Song, 3=Music legacy) and add_user_action (type 1=Song, 8=Music).
    // Dedupes per user+content+type so a single listener can only earn an artist credit once per song.
    private function creditArtistEarning($type, $content_id, $user_id)
    {
        try {
            if ($user_id <= 0 || $content_id <= 0) return;

            $rateSetting = General_Setting::where('key', 'payout_rate_per_stream')->first();
            $rate = $rateSetting ? (float) $rateSetting->value : 0.0;
            if ($rate <= 0) return;

            // Already credited this user for this content? skip.
            $exists = ArtistEarning::where('user_id', $user_id)
                ->where('content_id', $content_id)
                ->where('content_type', $type)
                ->exists();
            if ($exists) return;

            $artistIds = [];
            if ($type == 1) {
                $song = Song::where('id', $content_id)->first();
                if ($song && $song->artist_id) $artistIds[] = (int) $song->artist_id;
            } elseif ($type == 3 || $type == 8) {
                $music = Music::where('id', $content_id)->first();
                if ($music && $music->artist_id) {
                    foreach (explode(',', (string) $music->artist_id) as $aid) {
                        $aid = (int) trim($aid);
                        if ($aid > 0) $artistIds[] = $aid;
                    }
                }
            }

            $artistIds = array_unique(array_filter($artistIds));
            if (empty($artistIds)) return;

            $share = $rate / count($artistIds);
            foreach ($artistIds as $aid) {
                ArtistEarning::create([
                    'artist_id' => $aid,
                    'user_id' => $user_id,
                    'content_id' => $content_id,
                    'content_type' => $type,
                    'amount' => $share,
                ]);
            }
        } catch (Exception $e) {
            // silent — don't break play if earnings credit fails
        }
    }
}
