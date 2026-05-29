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
use App\Models\General_Setting;
use App\Models\Notification;
use App\Models\Onboarding_Screen;
use App\Models\Page;
use App\Models\Play;
use App\Models\Podcast;
use App\Models\Podcast_Section;
use App\Models\Section;
use App\Models\Social_Link;
use App\Models\User;
use App\Models\User_Notification_Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use PHPUnit\Framework\Constraint\Count;

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
    public $common;
    public $page_limit;
    public function __construct()
    {
        $this->common = new Common;
        $this->page_limit = env('PAGE_LIMIT');
    }

    public function general_setting()
    {
        try {

            $list = General_Setting::get();
            foreach ($list as $key => $value) {

                if ($value['key'] == "app_logo") {
                    if (!empty($value['value'])) {
                        $value['value'] = $this->common->Get_Image($this->folder_app, $value['value']);
                    }
                }

                if ($value['key'] == "currency") {
                    if (!empty($value['value'])) {
                        $value['value'] = strtoupper($value['value']);
                    }
                }
            }
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $list);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_payment_option()
    {
        try {

            $return['status'] = 200;
            $return['message'] = __('api_msg.get_record_successfully');
            $return['result'] = [];

            $Option_data = Payment_Option::get();
            foreach ($Option_data as $value) {
                $return['result'][$value['name']] = $value;
            }
            return $return;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_pages()
    {
        try {

            $return['status'] = 200;
            $return['message'] = __('api_msg.get_record_successfully');
            $return['result'] = [];

            $data = Page::get();
            for ($i = 0; $i < count($data); $i++) {
                $return['result'][$i]['page_name'] = $data[$i]['page_name'];
                $return['result'][$i]['title'] = $data[$i]['title'];
                $return['result'][$i]['url'] = env('APP_URL') . '/public/pages/' . $data[$i]['page_name'];
                $return['result'][$i]['icon'] = $this->common->Get_Image($this->folder_app, $data[$i]['icon']);
            }
            return $return;
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_onboarding_screen()
    {
        try {
            $data = Onboarding_Screen::get();
            if (sizeof($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_app);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_social_link()
    {
        try {
            $Data = Social_Link::latest()->get();
            if (!$Data->isEmpty()) {

                $this->common->imageNameToUrl($Data, 'image', $this->folder_app);

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $Data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_city(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = City::orderBy('id', 'DESC');

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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_artist(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Artist::orderBy('id', 'DESC');

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

                $this->common->imageNameToUrl($data, 'image', $this->folder_artist);
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_category(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Category::orderBy('id', 'DESC');

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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_language(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Language::orderBy('id', 'DESC');

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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'city_id.required' => __('api_msg.city_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'artist_id.required' => __('api_msg.artist_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
                    $this->common->get_all_count_for_content(1, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'language_id.required' => __('api_msg.language_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'category_id.required' => __('api_msg.category_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'package_id.required' => __('api_msg.package_id_is_required'),
                    'price.required' => __('api_msg.price_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $user_id = $request->user_id;
            $package_id = $request->package_id;
            $price = $request->price;
            $transaction_id = isset($request->transaction_id) ? $request->transaction_id : "";
            $description = isset($request->description) ? $request->description : "";

            $Pdata = Package::where('id', $package_id)->where('status', '1')->first();
            if (!empty($Pdata)) {
                $Edate = date("Y-m-d", strtotime("$Pdata->time $Pdata->type"));
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
                    $this->common->Send_Mail(2, $user_data->email);
                }

                return $this->common->API_Response(200, __('api_msg.transaction_add_successfully'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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

                    $value['data'] = $value['created_at']->format('Y-m-d');

                    unset($value['package']);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'type.required' => __('api_msg.type_is_required'),
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'content_id.required' => __('api_msg.content_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $user_id = $request->user_id;
            $contet_id = $request->content_id;
            $type = $request->type;

            $song_favorites = Favorite::where('type', $type)->where('content_id', $contet_id)->where('user_id', $user_id)->first();

            if (isset($song_favorites) && $song_favorites != null) {

                $song_favorites->delete();
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'type.required' => __('api_msg.type_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $user_id = $request->user_id;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Favorite::where('type', 1)->where('user_id', $user_id)->with('song')->orderBy('id', 'DESC');
            } else {
                $data = Favorite::where('type', 2)->where('user_id', $user_id)->with('podcast')->orderBy('id', 'DESC');
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
                            if ($item['song']['song_upload_type'] == "server_video") {
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

                            $item['podcast']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['podcast']['is_favorite'] = $this->common->isFavorite(2, $item['podcast']['id'], $user_id);
                            $this->common->get_all_count_for_content(2, $item['podcast']);
                            $this->common->getAllIdByName(array($item['podcast']));

                            $result[] = $item['podcast'];
                        }
                    }
                }
                if (count($result) >     0) {
                    return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result, $pagination);
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_found'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function search_content(Request $request)
    {
        try {

            $validation = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'type' => 'required|numeric',
                ],
                [
                    'name.required' => __('api_msg.name_is_required'),
                    'type.required' => __('api_msg.type_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $name = isset($request->name) ? $request->name : "";
            $user_id = isset($request->user_id) ? $request->user_id : 0;
            $type = $request->type;

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Song::where('name', 'like', '%' . $name . '%')->where('status', 1)->orderBy('id', "DESC");
            } else if ($type == 2) {
                $data = Podcast::where('title', 'like', '%' . $name . '%')->where('status', 1)->orderBy('id', "DESC");
            } else if ($type == 3) {
                $data = Live_Event::where('title', 'like', '%' . $name . '%')->where('status', 1)->orderBy('id', "DESC");
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

                if ($type == 1) {

                    $this->common->imageNameToUrl($data, 'image', $this->folder_song);
                    $this->common->songNameToUrl($data, 'song_url', $this->folder_song);
                    $this->common->getAllIdByName($data);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(1, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $value);
                    }
                } else if ($type == 2) {

                    $this->common->imageNameToUrl($data, 'portrait_img', $this->folder_podcast);
                    $this->common->imageNameToUrl($data, 'landscape_img', $this->folder_podcast);
                    $this->common->getAllIdByName($data);
                    foreach ($data as $key => $value) {
                        $value['is_favorite'] = $this->common->isFavorite(2, $value->id, $user_id);
                        $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $value);
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
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value['is_favorite'] = $this->common->isFavorite(2, $value['id'], $user_id);
                    $this->common->get_all_count_for_content(2, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value['is_favorite'] = $this->common->isFavorite(2, $value['id'], $user_id);
                    $this->common->get_all_count_for_content(2, $value);
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'podcast_id.required' => __('api_msg.podcast_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
                    if ($data[$i]['episode_upload_type'] == "server_video") {
                        $data[$i]['episode_audio'] =  $this->common->Get_Song($this->folder_podcast, $data[$i]['episode_audio']);
                    }
                    $data[$i]['podcast_title'] = $this->common->GetPodcastNameByIds($data[$i]['podcasts_id']);
                    $data[$i]['total_comment'] = Comment::where('type', 2)->where('content_id', $podcast_id)->where('episode_id', $data[$i]['id'])->where('status', 1)->count();
                }

                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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

                $value['is_buy'] = $this->common->is_any_package_buy($user_id);
            }

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $result);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'live_event_id.required' => __('api_msg.live_event_id_is_required'),
                    'type.required' => __('api_msg.type_is_required'),
                    'price.required' => __('api_msg.price_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_section_list(Request $request) // type = 1-Song, 2-Podcat, 3-Live Event, 4-Artist, 5-Category, 6-Language, 7-City
    {
        try {
            $this->common->update_liveevent_status();

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Section::where('status', 1)->orderBy('sortable', 'asc')->latest();

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
                    if ($data[$i]['type'] == 1 || $data[$i]['type'] == 2) {

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

                        $content = Artist::where('status', 1);

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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'section_id.required' => __('api_msg.section_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $section_id = $request['section_id'];
            $user_id = isset($request['user_id']) ? $request['user_id'] : 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            $section = Section::where('id', $section_id)->first();
            if ($section != null && isset($section)) {

                if ($section['type'] == 1 || $section['type'] == 2) {
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

                        $content = Artist::where('status', 1);
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
                        if ($data[$i]['song_upload_type'] == "server_video") {
                            $data[$i]['song_url'] = $this->common->Get_Song($this->folder_song, $data[$i]['song_url']);
                        }
                        $data[$i]['is_favorite'] = $this->common->isFavorite(1, $data[$i]['id'], $user_id);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $this->common->get_all_count_for_content(1, $data[$i]);
                        $this->common->getAllIdByName(array($data[$i]));
                    } else if ($section['type'] == 2) {

                        $data[$i]['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['portrait_img']);
                        $data[$i]['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $data[$i]['landscape_img']);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['is_favorite'] = $this->common->isFavorite(2, $data[$i]['id'], $user_id);
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
                    }
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_banner(Request $request)
    {
        try {

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Banner::with('song', 'podcast')->orderBy('id', 'desc');

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
                            if ($item['song']['song_upload_type'] == "server_video") {
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

                            $item['podcast']['is_buy'] = $this->common->is_any_package_buy($user_id);
                            $item['podcast']['is_favorite'] = $this->common->isFavorite(2, $item['podcast']['id'], $user_id);
                            $this->common->get_all_count_for_content(2, $item['podcast']);
                            $item['podcast']['type'] = 2;

                            $result[] = $item['podcast'];
                        }
                    }
                    unset($item['song'], $item['podcast']);
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
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'notification_id.required' => __('api_msg.notification_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'content_id.required' => __('api_msg.content_id_is_required'),
                    'type.required' => __('api_msg.type_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            if ($request->type == 2) {
                $validation1 = Validator::make(
                    $request->all(),
                    [
                        'episode_id' => 'required|numeric',
                    ],
                    [
                        'episode_id.required' => __('api_msg.episode_id_required'),
                    ]
                );
                if ($validation1->fails()) {

                    $errors = $validation1->errors()->first();
                    $data['status'] = 400;
                    if ($errors) {
                        $data['message'] = $errors;
                    }
                    return $data;
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

                        $song = Song::where('id', $content_id)->first();
                        if (!empty($song)) {
                            $song->increment('total_play');
                        }
                    } else if ($type == 2) {

                        $podcast = Podcast::where('id', $content_id)->first();
                        $episode = Episode::where('podcasts_id', $content_id)->where('id', $episode_id)->first();
                        if (!empty($podcast) || !empty($episode)) {

                            $podcast->increment('total_play', 1);
                            $episode->increment('total_play', 1);
                        }
                    }
                    return $this->common->API_Response(200, __('api_msg.play_added'));
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.previously_played'));
            }
            return $this->common->API_Response(200, "Song Play SuccessFully", []);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function add_comment(Request $request)
    {
        try {

            if ($request->type == 1) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'content_id' => 'required|numeric',
                        'comment' => 'required',
                    ],
                    [
                        'user_id.required' => __('api_msg.user_id_is_required'),
                        'content_id.required' => __('api_msg.content_id_is_required'),
                        'comment.required' => __('api_msg.comment_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
            } elseif ($request->type == 2) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|numeric',
                        'content_id' => 'required|numeric',
                        'episode_id' => 'required|numeric',
                        'comment' => 'required',
                    ],
                    [
                        'user_id.required' => __('api_msg.user_id_is_required'),
                        'content_id.required' => __('api_msg.content_id_is_required'),
                        'episode_id.required' => __('api_msg.episode_id_is_required'),
                        'comment.required' => __('api_msg.comment_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
            } else {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                    [
                        'type.required' => __('api_msg.type_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_comment(Request $request)
    {
        try {

            if ($request->type == 1) {

                $validation = Validator::make(
                    $request->all(),
                    [
                        'content_id' => 'required|numeric',
                    ],
                    [
                        'content_id.required' => __('api_msg.content_id_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
            } elseif ($request->type == 2) {

                $validation = Validator::make(
                    $request->all(),
                    [

                        'content_id' => 'required|numeric',
                        'episode_id' => 'required|numeric',
                    ],
                    [
                        'content_id.required' => __('api_msg.content_id_is_required'),
                        'episode_id.required' => __('api_msg.episode_id_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
            } else {
                $validation = Validator::make(
                    $request->all(),
                    [
                        'type' => 'required|numeric',
                    ],
                    [
                        'type.required' => __('api_msg.type_is_required'),
                    ]
                );
                if ($validation->fails()) {
                    $data['status'] = 400;
                    $data['message'] = $validation->errors()->first();
                    return $data;
                }
            }

            $type = $request->type;
            $content_id = $request->content_id;
            $episode_id = $request->episode_id;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($type == 1) {
                $data = Comment::where('content_id', $content_id)->where('type', 1)->where('status', 1)->with('user')->orderBy('id', 'desc');
            } else {
                $data = Comment::where('content_id', $content_id)->where('episode_id', $episode_id)->where('type', 2)->where('status', 1)->with('user')->orderBy('id', 'desc');
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'user_id.required' => __('api_msg.user_id_is_required'),
                    'comment.required' => __('api_msg.comment_is_required'),
                    'comment_id.required' => __('api_msg.comment_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
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
            return $this->common->API_Response(200, __('api_msg.comment_not_found'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'comment_id.required' => __('api_msg.comment_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $comment_id = $request['comment_id'];
            Comment::where('id', $comment_id)->delete();
            return $this->common->API_Response(200, __('api_msg.comment_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function get_podcast_section_list(Request $request) // type = 1-Song, 2-Podcat, 3-Live Event, 4-Artist, 5-Category, 6-Language, 7-City
    {
        try {
            $this->common->update_liveevent_status();

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_id = isset($request->user_id) ? $request->user_id : 0;

            $data = Podcast_Section::where('status', 1)->orderBy('sortable', 'asc')->latest();

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

                    $query = Podcast::where('status', 1);

                    if ($data[$i]['category_id'] != 0) {
                        $query->where('category_id', $data[$i]['category_id']);
                    }

                    if ($data[$i]['language_id'] != 0) {
                        $query->where('language_id', $data[$i]['language_id']);
                    }

                    if ($data[$i]['order_by_play'] == 1) {
                        $query->orderBy('total_play', 'desc');
                    } else if ($data[$i]['order_by_play'] == 0) {
                        $query->orderBy('total_play', 'asc');
                    }

                    if ($data[$i]['order_by_upload'] == 1) {
                        $query->orderBy('id', 'desc');
                    } else if ($data[$i]['order_by_upload'] == 0) {
                        $query->orderBy('id', 'asc');
                    }

                    if ($data[$i]['is_premium'] == 0) {
                        $query->where('is_premium', 0);
                    } else if ($data[$i]['is_premium'] == 1) {
                        $query->where('is_premium', 1);
                    }

                    $content = $query->take($data[$i]['no_of_content'])->get();

                    $this->common->getAllIdByName($content);
                    for ($j = 0; $j < count($content); $j++) {

                        $content[$j]['portrait_img'] = $this->common->Get_Image($this->folder_podcast, $content[$j]['portrait_img']);
                        $content[$j]['landscape_img'] = $this->common->Get_Image($this->folder_podcast, $content[$j]['landscape_img']);

                        $content[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $content[$j]['is_favorite'] = $this->common->isFavorite(2, $content[$j]['id'], $user_id);
                        $this->common->get_all_count_for_content(2, $content[$j]);
                    }
                    $data[$i]['data'] = $content;
                }
                return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                [
                    'section_id.required' => __('api_msg.section_id_is_required'),
                ]
            );
            if ($validation->fails()) {
                $data['status'] = 400;
                $data['message'] = $validation->errors()->first();
                return $data;
            }

            $section_id = $request['section_id'];
            $user_id = isset($request['user_id']) ? $request['user_id'] : 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            $section = Podcast_Section::where('id', $section_id)->first();
            if ($section != null && isset($section)) {

                $content = Podcast::where('status', 1);

                if ($section['category_id'] != 0) {
                    $content->where('category_id', $section['category_id']);
                }

                if ($section['language_id'] != 0) {
                    $content->where('language_id', $section['language_id']);
                }

                if ($section['order_by_play'] == 1) {
                    $content->orderBy('total_play', 'desc');
                } else if ($section['order_by_play'] == 0) {
                    $content->orderBy('total_play', 'asc');
                }

                if ($section['order_by_upload'] == 1) {
                    $content->orderBy('id', 'desc');
                } else if ($section['order_by_upload'] == 0) {
                    $content->orderBy('id', 'asc');
                }

                if ($section['is_premium'] == 0) {
                    $content->where('is_premium', 0);
                } else if ($section['is_premium'] == 1) {
                    $content->where('is_premium', 1);
                }

                $data = $content;
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
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
