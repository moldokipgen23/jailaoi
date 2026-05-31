<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Common;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Language;
use App\Models\Playlist_Content;
use App\Models\Section;
use App\Models\User;
use App\Models\Artist;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    private $folder_content = "content";
    private $folder_category = "category";
    private $folder_artist = "artist";
    private $folder_language = "language";
    private $folder_ffmpeg_content = "/app/public/content/";
    public $common;
    public $page_limit;
    public function __construct()
    {
        try {
            $this->common = new Common();
            $this->page_limit = env('PAGE_LIMIT', 20);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_video_list(Request $request)
    {
        try {
            $setting = Setting_Data();
            if (($setting['video_status'] ?? '1') == '0') {
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), []);
            }
            $validation = Validator::make($request->all(), [
                'is_home_page' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $is_home_page = $request['is_home_page'];
            $user_id = $request['user_id'] ?? 0;
            $category_id = $request['category_id'] ?? 0;
            $page_no = $request['page_no'] ?? 1;

            $block_channel_list = $this->common->get_block_channel($user_id);
            $get_subscriber = $this->common->get_subscriber($user_id);
            $get_interests_category = $this->common->get_interests_category($user_id);
            $last_3days = now()->subDays(3)->toDateTimeString();

            if ($is_home_page == 1) {

                // Step 1: Get IDs
                $content_ids = Content::where('content_type', 1)->where('is_rent', 0)->where('status', 1)->whereNotIn('channel_id', $block_channel_list)->latest()->pluck('id')->toArray();

                // Step 2: Recent Content
                $recent_data = Content::whereIn('id', $content_ids)->whereIn('channel_id', $get_subscriber)->where('created_at', '>', $last_3days)->orderByDesc('total_view')->pluck('id')->toArray();

                // Step 3: Interests Content
                $interests_data = Content::whereIn('id', $content_ids)->whereNotIn('id', $recent_data)->whereIn('category_id', $get_interests_category)->orderByDesc('total_view')->pluck('id')->toArray();

                // Step 4: Other Content
                $other_data = array_values(array_diff($content_ids, array_merge($recent_data, $interests_data)));

                // Step 5: Merge All IDs
                $final_ids = array_merge($recent_data, $interests_data, $other_data);

                // Step 6: Paginate IDs only (no shuffle on full set)
                $total = count($final_ids);
                $offset = $this->page_limit * ($page_no - 1);
                $current_page_ids = array_slice($final_ids, $offset, $this->page_limit);

                // Step 7: Fetch actual content records
                if (!empty($current_page_ids)) {
                    $data = Content::whereIn('id', $current_page_ids)->orderByRaw("FIELD(id, " . implode(',', $current_page_ids) . ")")->get()->shuffle()->toArray();
                } else {
                    $data = [];
                }

                // Step 8: Paginator
                $paginator = new LengthAwarePaginator($data, $total, $this->page_limit, $page_no);
                $more_page = $this->common->more_page($page_no, $paginator->lastPage());
                $pagination = $this->common->pagination_array($total, $paginator->lastPage(), $page_no, $more_page);
            } else if ($is_home_page == 0 && $category_id != 0) {

                // Step 1: Get IDs
                $content_ids = Content::where('content_type', 1)->where('is_rent', 0)->where('status', 1)->where('category_id', $category_id)->whereNotIn('channel_id', $block_channel_list)->latest()->pluck('id')->toArray();

                // Step 2: Recent Content
                $recent_data = Content::whereIn('id', $content_ids)->whereIn('channel_id', $get_subscriber)->where('created_at', '>', $last_3days)->orderByDesc('total_view')->pluck('id')->toArray();

                // Step 4: Other Content
                $other_data = array_values(array_diff($content_ids, $recent_data));

                // Step 5: Merge All IDs
                $final_ids = array_merge($recent_data, $other_data);

                // Step 6: Paginate IDs only (no shuffle on full set)
                $total = count($final_ids);
                $offset = $this->page_limit * ($page_no - 1);
                $current_page_ids = array_slice($final_ids, $offset, $this->page_limit);

                // Step 7: Fetch actual content records
                if (!empty($current_page_ids)) {
                    $data = Content::whereIn('id', $current_page_ids)->orderByRaw("FIELD(id, " . implode(',', $current_page_ids) . ")")->get()->shuffle()->toArray();
                } else {
                    $data = [];
                }

                // Step 8: Paginator
                $paginator = new LengthAwarePaginator($data, $total, $this->page_limit, $page_no);
                $more_page = $this->common->more_page($page_no, $paginator->lastPage());
                $pagination = $this->common->pagination_array($total, $paginator->lastPage(), $page_no, $more_page);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            if (count($data) > 0) {

                $return = array();
                foreach ($data as $key) {

                    $key['portrait_img'] = $this->common->getImage($this->folder_content, $key['portrait_img'], $key['portrait_img_storage_type']);
                    $key['landscape_img'] = $this->common->getImage($this->folder_content, $key['landscape_img'], $key['landscape_img_storage_type']);
                    if ($key['content_upload_type'] == 'server_video') {
                        $key['content'] = $this->common->getVideo($this->folder_content, $key['content'], $key['content_storage_type']);
                    }

                    $key['user_id'] = $this->common->getUserId($key['channel_id']);
                    $key['channel_name'] = $this->common->getChannelName($key['channel_id']);
                    $key['channel_image'] = $this->common->getChannelImage($key['channel_id']);
                    $key['category_name'] = $this->common->getCategoryName($key['category_id']);
                    $key['language_name'] = $this->common->getLanguageName($key['language_id']);
                    $key['is_subscribe'] = $this->common->is_subscribe($user_id, $key['user_id']);
                    $key['total_comment'] = $this->common->getTotalComment($key['id']);
                    $key['total_subscriber'] = $this->common->total_subscriber($key['user_id']);
                    $key['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $key['content_type'], $key['id'], 0);
                    $key['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $key['stop_time'] = $this->common->getContentStopTime($user_id, $key['content_type'], $key['id'], 0);

                    $return[] = $key;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $return, $pagination);
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

            $user_id = $request['user_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($user_id != 0) {

                $interests = $this->common->get_interests_category($user_id);
                $other_data = Category::whereNotIn('id', $interests)->pluck('id')->toArray();
                $cat_ids = array_merge($interests, $other_data);
                $data = Category::whereIn('id', $cat_ids)->where('status', 1)->orderByRaw("FIELD(id, " . implode(',', $cat_ids) . ")");
            } else {

                $data = Category::where('status', 1)->orderBy('sort_order', 'asc');
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_category,);
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
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

            $data = Language::where('status', 1)->orderBy('sort_order', 'asc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_language);
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_releted_video(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_id = $request['content_id'];
            $user_id = $request['user_id'] ?? 0;

            if (!$this->common->isContentTypeEnabled(1)) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $content = Content::where('id', $content_id)->where('status', 1)->first();
            if ($content) {

                $page_size = 0;
                $current_page = 0;
                $more_page = false;

                $data = Content::where('id', '!=', $content['id'])->where('category_id', $content['category_id'])->where('content_type', 1)->where('is_rent', 0)->where('status', 1)->orderby('total_view', 'desc');

                $total_rows = $data->count();
                $total_page = $this->page_limit;
                $page_size = ceil($total_rows / $total_page);
                $current_page = $request['page_no'] ?? 1;
                $offset = $current_page * $total_page - $total_page;

                $more_page = $this->common->more_page($current_page, $page_size);
                $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
                $data = $data->take($total_page)->offset($offset)->latest()->get();

                if (count($data) > 0) {

                    for ($i = 0; $i < count($data); $i++) {

                        $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                        $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                        if ($data[$i]['content_upload_type'] == 'server_video') {
                            $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                        }

                        $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                        $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                        $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                        $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                        $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                        $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                        $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                        $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                        $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    }
                    return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
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
    public function get_music_section(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'is_home_screen' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $is_home_screen = $request['is_home_screen'];
            $content_type = $request['content_type'] ?? 0;
            $user_id = $request['user_id'] ?? 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            if ($is_home_screen == 1) {
                $data = Section::where('is_home_screen', $is_home_screen)->where('status', 1)->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc');
            } else if ($is_home_screen == 2) {
                $data = Section::where('is_home_screen', $is_home_screen)->where('content_type', $content_type)->where('status', 1)->orderBy('is_fixed', 'desc')->orderBy('sort_order', 'asc');
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $offset = $page_no * $total_page - $total_page;

            $more_page = $this->common->more_page($page_no, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $page_no, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['data'] = [];
                    if ($data[$i]['content_type'] == 1) {

                        $data[$i]['data'] = $this->common->music_section_query($user_id, 2, $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['order_by_view'], $data[$i]['order_by_like'], $data[$i]['order_by_upload'], $data[$i]['no_of_content']);
                    } else if ($data[$i]['content_type'] == 2) {

                        $query = $this->common->music_section_query($user_id, 4, $data[$i]['category_id'], $data[$i]['language_id'], $data[$i]['order_by_view'], $data[$i]['order_by_like'], $data[$i]['order_by_upload'], $data[$i]['no_of_content']);

                        // Episodes
                        for ($j = 0; $j < count($query); $j++) {

                            $episode_array = [];

                            $episode = Episode::select('id', 'name', 'portrait_img', 'portrait_img_storage_type', 'description')->where('podcasts_id', $query[$j]['id'])->where('status', 1)->orderBy('sort_order', 'asc')->latest()->take(3)->get();
                            if (count($episode) > 0) {

                                $this->common->imageNameToUrl($episode, 'portrait_img', $this->folder_content, 'portrait_img_storage_type');
                                $episode_array = $episode;
                            }
                            $query[$j]['episode_array'] = $episode_array;
                        }
                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['content_type'] == 4) {

                        $query = $this->common->music_section_query($user_id, 5, 0, 0, $data[$i]['order_by_view'], $data[$i]['order_by_like'], $data[$i]['order_by_upload'], $data[$i]['no_of_content']);

                        // Playlist Image
                        for ($j = 0; $j < count($query); $j++) {

                            $image_array = [];
                            $playlist_content = Playlist_Content::where('playlist_id', $query[$j]['id'])->whereIn('content_type', [1, 2])->orderBy('sort_order', 'asc')->with('Content')->latest()->get();

                            if (count($playlist_content) > 0) {
                                $img_count = 0;
                                for ($k = 0; $k < count($playlist_content); $k++) {

                                    if ($playlist_content[$k]['Content'] != null & isset($playlist_content[$k]['Content'])) {

                                        $image_array[] = $this->common->getImage($this->folder_content, $playlist_content[$k]['Content']['portrait_img'], $playlist_content[$k]['Content']['portrait_img_storage_type']);
                                        $img_count = $img_count + 1;
                                        if ($img_count == 4) {
                                            break;
                                        }
                                    }
                                }
                            }
                            $query[$j]['playlist_image'] = $image_array;
                        }
                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['content_type'] == 5) {

                        $query = Category::orderBy('sort_order', 'asc')->get();
                        for ($j = 0; $j < count($query); $j++) {

                            $query[$j]['title'] = $query[$j]['name'];
                            $query[$j]['portrait_img'] = $this->common->getImage($this->folder_category, $query[$j]['image'], $query[$j]['storage_type']);
                            $query[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        }
                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['content_type'] == 6) {

                        $query = Language::orderBy('sort_order', 'asc')->get();
                        for ($j = 0; $j < count($query); $j++) {

                            $query[$j]['title'] = $query[$j]['name'];
                            $query[$j]['portrait_img'] = $this->common->getImage($this->folder_language, $query[$j]['image'], $query[$j]['storage_type']);
                            $query[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        }
                        $data[$i]['data'] = $query;
                    } else if ($data[$i]['content_type'] == 7) {

                        $query = Artist::with('user')->where('status', 1)->latest()->take($data[$i]['no_of_content'] > 0 ? $data[$i]['no_of_content'] : 10)->get();
                        for ($j = 0; $j < count($query); $j++) {
                            $query[$j]['name'] = $query[$j]['name'];
                            $query[$j]['title'] = $query[$j]['name'];
                            $query[$j]['artist_name'] = $query[$j]['name'];
                            $query[$j]['portrait_img'] = $this->common->getImage($this->folder_artist, $query[$j]['image'], 0);
                            $query[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        }
                        $data[$i]['data'] = $query;
                    }
                }
                $data = $data->values();
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_music_section_detail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'section_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $section_id = $request['section_id'];
            $user_id = $request['user_id'] ?? 0;
            $page_no = $request['page_no'] ?? 1;
            $page_size = 0;
            $more_page = false;

            $section = Section::where('id', $section_id)->where('status', 1)->first();
            if ($section) {

                if ($section['content_type'] == 1) {
                    $data = $this->common->music_section_details_query(2, $section['category_id'], $section['language_id'], $section['order_by_view'], $section['order_by_like'], $section['order_by_upload']);
                } else if ($section['content_type'] == 2) {
                    $data = $this->common->music_section_details_query(4, $section['category_id'], $section['language_id'], $section['order_by_view'], $section['order_by_like'], $section['order_by_upload']);
                } else if ($section['content_type'] == 4) {
                    $data = $this->common->music_section_details_query(5, 0, 0, $section['order_by_view'], $section['order_by_like'], $section['order_by_upload']);
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
            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                    $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                    if ($data[$i]['content_upload_type'] == 'server_video') {
                        $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                    }

                    $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                    $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                    $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                    $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                    $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                    $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                    $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                    $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                    $data[$i]['total_episode'] = $this->common->getTotalEpisode($data[$i]['id']);
                    $data[$i]['is_rent_buy'] = $this->common->getRentBuy($user_id, $data[$i]['id']);
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);

                    // Playlist Image
                    $image_array = [];
                    if ($data[$i]['content_type'] == 5) {

                        $playlist_content = Playlist_Content::where('playlist_id', $data[$i]['id'])->whereIn('content_type', [1, 2])->orderBy('sort_order', 'asc')->with('Content')->latest()->get();
                        if (count($playlist_content) > 0) {

                            $img_count = 0;
                            for ($j = 0; $j < count($playlist_content); $j++) {

                                if ($playlist_content[$j]['Content'] != null & isset($playlist_content[$j]['Content'])) {

                                    $image_array[] = $this->common->getImage($this->folder_content, $playlist_content[$j]['Content']['portrait_img'], $playlist_content[$j]['Content']['portrait_img_storage_type']);
                                    $img_count = $img_count + 1;
                                    if ($img_count == 4) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $data[$i]['playlist_image'] = $image_array;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_music_by_category(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'category_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $category_id = $request['category_id'];
            $user_id = $request['user_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Content::where('content_type', 2)->where('category_id', $category_id)->where('is_rent', 0)->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                    $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                    if ($data[$i]['content_upload_type'] == 'server_video') {
                        $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                    }

                    $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                    $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                    $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                    $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                    $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                    $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                    $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                    $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_music_by_language(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'language_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $language_id = $request['language_id'];
            $user_id = $request['user_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Content::where('content_type', 2)->where('language_id', $language_id)->where('is_rent', 0)->where('status', 1);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                    $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                    if ($data[$i]['content_upload_type'] == 'server_video') {
                        $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                    }

                    $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                    $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                    $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                    $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                    $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                    $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                    $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                    $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_releted_music(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_id = $request['content_id'];
            $user_id = $request['user_id'] ?? 0;

            $content = Content::where('id', $content_id)->first();
            if ($content) {

                $page_size = 0;
                $current_page = 0;
                $more_page = false;

                $data = Content::where('id', '!=', $content['id'])->where('content_type', 2)->where('category_id', $content['category_id'])->where('is_rent', 0)->where('status', 1)->orderby('total_view', 'desc');

                $total_rows = $data->count();
                $total_page = $this->page_limit;
                $page_size = ceil($total_rows / $total_page);
                $current_page = $request['page_no'] ?? 1;
                $offset = $current_page * $total_page - $total_page;

                $more_page = $this->common->more_page($current_page, $page_size);
                $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
                $data = $data->take($total_page)->offset($offset)->latest()->get();

                if (count($data) > 0) {

                    for ($i = 0; $i < count($data); $i++) {

                        $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                        $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                        if ($data[$i]['content_upload_type'] == 'server_video') {
                            $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                        }

                        $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                        $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                        $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                        $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                        $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                        $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                        $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                        $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                        $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    }
                    return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
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
    public function upload_video(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, __('api_msg.only_artists_can_upload'));
            }

            // Check if this is a chunk upload
            if ($request->has('chunk_index') && $request->has('total_chunks')) {

                $validation = Validator::make($request->all(), [
                    'chunk_index' => 'required|integer|min:0',
                    'total_chunks' => 'required|integer|min:1',
                    'file' => 'required|file',
                ]);
                if ($validation->fails()) {
                    return $this->common->API_Response(400, $validation->errors()->first());
                }

                $chunkIndex = (int) $request['chunk_index'];
                $totalChunks = (int) $request['total_chunks'];
                $uploadId = $request['directory'] ?? now()->format('ymd_His_') . uniqid();

                // Save chunk in temp directory
                $tempDir = storage_path("app/public/chunks/{$uploadId}");
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                // Save chunk
                $request->file('file')->move($tempDir, "chunk_{$chunkIndex}");

                // Merge when all chunks are received
                $uploadedChunks = glob($tempDir . '/chunk_*');
                if (count($uploadedChunks) == $totalChunks) {

                    $datePath = date('Y') . '/' . date('m');
                    $filename = 'vid_' . date('Y_m_d_') . uniqid() . '.mp4';
                    $contentDir = storage_path("app/public/content/{$datePath}");
                    if (!file_exists($contentDir)) { mkdir($contentDir, 0777, true); }
                    $finalPath = $contentDir . '/' . $filename;
                    $output = fopen($finalPath, 'wb');

                    for ($i = 0; $i < $totalChunks; $i++) {

                        $chunk = fopen("{$tempDir}/chunk_{$i}", 'rb');
                        stream_copy_to_stream($chunk, $output);
                        fclose($chunk);
                        unlink("{$tempDir}/chunk_{$i}");
                    }

                    fclose($output);
                    rmdir($tempDir);

                    return $this->common->API_Response(200, __('api_msg.upload_completed'), [
                        'file_path' => "{$filename}",
                        'full_url' => Storage::disk('public')->exists('content/' . $filename),
                    ]);
                }
                return $this->common->API_Response(206, __('api_msg.upload_progress', ['current' => $chunkIndex, 'total' => $totalChunks]), ['directory' => $uploadId]);
            }

            // Final save
            $validation = Validator::make($request->all(), [
                'title' => 'required',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'video' => 'required|string',
                'is_comment' => 'required',
                'is_download' => 'required',
                'is_like' => 'required',
                'is_rent' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json(['status' => 400, 'message' => $validation->errors()->first()]);
            }
            if ($request['is_rent'] == 1) {
                $validation1 = Validator::make($request->all(), [
                    'rent_price' => 'required|numeric|min:0',
                    'rent_day' => 'required|numeric|min:0',
                ]);
                if ($validation1->fails()) {
                    return response()->json(['status' => 400, 'message' => $validation1->errors()->first()]);
                }
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $insert = new Content();
            $insert['content_type'] = 1;
            $insert['channel_id'] = $requestData['channel_id'];
            $insert['category_id'] = $requestData['category_id'];
            $insert['language_id'] = $requestData['language_id'];
            $insert['description'] = $requestData['description'] ?? "";
            $hashtag_id = $this->common->checkHashTag($insert['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $insert['hashtag_id'] = $hashtagId;
            $insert['title'] = $requestData['title'];
            $insert['portrait_img_storage_type'] = $storage_type;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $insert['portrait_img'] = $this->common->saveImage($file1, $this->folder_content, 'port_', $insert['portrait_img_storage_type']);
            } else {
                $insert['portrait_img'] = "";
            }
            $insert['landscape_img_storage_type'] = $storage_type;
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $insert['landscape_img'] = $this->common->saveImage($file2, $this->folder_content, 'land_', $insert['landscape_img_storage_type']);
            } else {
                $insert['landscape_img'] = "";
            }
            $insert['content_upload_type'] = "server_video";
            if ($storage_type == 1) {

                $insert['content_storage_type'] = 1;
                $insert['content'] = $requestData['video'];
                $insert['content_duration'] = $requestData['content_duration'] ?? $this->common->ExtractDuration($requestData['video'], $this->folder_ffmpeg_content);
            } else if ($storage_type == 2) {

                $insert['content_storage_type'] = 2;
                $insert['content_duration'] = $requestData['content_duration'] ?? $this->common->ExtractDuration($requestData['video'], $this->folder_ffmpeg_content);

                $localPath = storage_path("app/{$this->folder_content}/" . $requestData['video']);
                // Upload to S3
                Storage::disk('s3')->put($this->folder_content . '/' . $requestData['video'], file_get_contents($localPath));

                // Optional: delete local file after upload
                if (Storage::disk('s3')->exists($this->folder_content . '/' . $requestData['video'])) {
                    $this->common->deleteImageToFolder($this->folder_content, $requestData['video'], 1);
                }
                $insert['content'] = $requestData['video'];
            }
            $insert['is_rent'] = $requestData['is_rent'];
            $insert['rent_price'] = $requestData['rent_price'] ?? 0;
            $insert['rent_day'] = $requestData['rent_day'] ?? 0;
            $insert['is_comment'] = $requestData['is_comment'];
            $insert['is_download'] = $requestData['is_download'];
            $insert['is_like'] = $requestData['is_like'];
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['total_dislike'] = 0;
            $insert['playlist_type'] = 0;
            $insert['total_watch_time'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {
                return response()->json(['status' => 200, 'success' => __('api_msg.success_add_video')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('api_msg.data_not_save')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function upload_music(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, __('api_msg.only_artists_can_upload'));
            }

            // Check if this is a chunk upload
            if ($request->has('chunk_index') && $request->has('total_chunks')) {

                $validation = Validator::make($request->all(), [
                    'chunk_index' => 'required|integer|min:0',
                    'total_chunks' => 'required|integer|min:1',
                    'file' => 'required|file',
                ]);
                if ($validation->fails()) {
                    return $this->common->API_Response(400, $validation->errors()->first());
                }

                $chunkIndex = (int) $request['chunk_index'];
                $totalChunks = (int) $request['total_chunks'];
                $uploadId = $request['directory'] ?? now()->format('ymd_His_') . uniqid();

                // Save chunk in temp directory
                $tempDir = storage_path("app/public/chunks/{$uploadId}");
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                // Save chunk
                $request->file('file')->move($tempDir, "chunk_{$chunkIndex}");

                // Merge when all chunks are received
                $uploadedChunks = glob($tempDir . '/chunk_*');
                if (count($uploadedChunks) == $totalChunks) {

                    $datePath = date('Y') . '/' . date('m');
                    $filename = 'music_' . date('Y_m_d_') . uniqid() . '.mp3';
                    $contentDir = storage_path("app/public/content/{$datePath}");
                    if (!file_exists($contentDir)) {
                        mkdir($contentDir, 0777, true);
                    }
                    $finalPath = $contentDir . '/' . $filename;
                    $output = fopen($finalPath, 'wb');

                    for ($i = 0; $i < $totalChunks; $i++) {

                        $chunk = fopen("{$tempDir}/chunk_{$i}", 'rb');
                        stream_copy_to_stream($chunk, $output);
                        fclose($chunk);
                        unlink("{$tempDir}/chunk_{$i}");
                    }

                    fclose($output);
                    rmdir($tempDir);

                    return $this->common->API_Response(200, __('api_msg.upload_completed'), [
                        'file_path' => $datePath . '/' . $filename,
                        'full_url' => Storage::disk('public')->exists('content/' . $datePath . '/' . $filename),
                    ]);
                }
                return $this->common->API_Response(206, __('api_msg.upload_progress', ['current' => $chunkIndex, 'total' => $totalChunks]), ['directory' => $uploadId]);
            }

            // Final save
            $validation = Validator::make($request->all(), [
                'title' => 'required',
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'music' => 'required|string',
                'is_comment' => 'required',
                'is_download' => 'required',
                'is_like' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json(['status' => 400, 'message' => $validation->errors()->first()]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $insert = new Content();
            $insert['content_type'] = 2;
            $insert['channel_id'] = $requestData['channel_id'];
            $insert['category_id'] = $requestData['category_id'];
            $insert['language_id'] = $requestData['language_id'];
            $insert['album_id'] = $requestData['album_id'] ?? null;
            $insert['description'] = $requestData['description'] ?? "";
            $insert['lyrics'] = $requestData['lyrics'] ?? "";
            $hashtag_id = $this->common->checkHashTag($insert['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $insert['hashtag_id'] = $hashtagId;
            $insert['title'] = $requestData['title'];
            $insert['portrait_img_storage_type'] = $storage_type;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $insert['portrait_img'] = $this->common->saveImage($file1, $this->folder_content, 'port_', $insert['portrait_img_storage_type']);
            } else {
                $insert['portrait_img'] = "";
            }
            $insert['landscape_img_storage_type'] = $storage_type;
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $insert['landscape_img'] = $this->common->saveImage($file2, $this->folder_content, 'land_', $insert['landscape_img_storage_type']);
            } else {
                $insert['landscape_img'] = "";
            }
            $insert['content_upload_type'] = "server_video";
            if ($storage_type == 1) {

                $insert['content_storage_type'] = 1;
                $insert['content'] = $requestData['music'];
                $insert['content_duration'] = $requestData['content_duration'] ?? $this->common->ExtractDuration($requestData['music'], $this->folder_ffmpeg_content);
                $insert['waveform_data'] = $this->common->generateWaveform($requestData['music'], $this->folder_ffmpeg_content);
            } else if ($storage_type == 2) {

                $insert['content_storage_type'] = 2;
                $insert['content_duration'] = $requestData['content_duration'] ?? $this->common->ExtractDuration($requestData['music'], $this->folder_ffmpeg_content);

                $localPath = storage_path("app/{$this->folder_ffmpeg_content}" . $requestData['music']);
                // Upload to S3
                Storage::disk('s3')->put($this->folder_content . '/' . $requestData['music'], file_get_contents($localPath));

                // Optional: delete local file after upload
                if (Storage::disk('s3')->exists($this->folder_content . '/' . $requestData['music'])) {
                    $this->common->deleteImageToFolder($this->folder_content, $requestData['music'], 1);
                }
                $insert['content'] = $requestData['music'];
            }
            $insert['is_rent'] = 0;
            $insert['rent_price'] = 0;
            $insert['rent_day'] = 0;
            $insert['is_comment'] = $requestData['is_comment'];
            $insert['is_download'] = $requestData['is_download'];
            $insert['is_like'] = $requestData['is_like'];
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['total_dislike'] = 0;
            $insert['playlist_type'] = 0;
            $insert['total_watch_time'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {
                return response()->json(['status' => 200, 'success' => __('api_msg.success_add_music')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('api_msg.data_not_save')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function upload_radio(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, __('api_msg.only_artists_can_upload'));
            }

            // Check if this is a chunk upload
            if ($request->has('chunk_index') && $request->has('total_chunks')) {

                $validation = Validator::make($request->all(), [
                    'chunk_index' => 'required|integer|min:0',
                    'total_chunks' => 'required|integer|min:1',
                    'file' => 'required|file',
                ]);
                if ($validation->fails()) {
                    return $this->common->API_Response(400, $validation->errors()->first());
                }

                $chunkIndex = (int) $request['chunk_index'];
                $totalChunks = (int) $request['total_chunks'];
                $uploadId = $request['directory'] ?? now()->format('ymd_His_') . uniqid();

                // Save chunk in temp directory
                $tempDir = storage_path("app/public/chunks/{$uploadId}");
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                // Save chunk
                $request->file('file')->move($tempDir, "chunk_{$chunkIndex}");

                // Merge when all chunks are received
                $uploadedChunks = glob($tempDir . '/chunk_*');
                if (count($uploadedChunks) == $totalChunks) {

                    $datePath = date('Y') . '/' . date('m');
                    $filename = 'radio_' . date('Y_m_d_') . uniqid() . '.mp3';
                    $contentDir = storage_path("app/public/content/{$datePath}");
                    if (!file_exists($contentDir)) { mkdir($contentDir, 0777, true); }
                    $finalPath = $contentDir . '/' . $filename;
                    $output = fopen($finalPath, 'wb');

                    for ($i = 0; $i < $totalChunks; $i++) {

                        $chunk = fopen("{$tempDir}/chunk_{$i}", 'rb');
                        stream_copy_to_stream($chunk, $output);
                        fclose($chunk);
                        unlink("{$tempDir}/chunk_{$i}");
                    }

                    fclose($output);
                    rmdir($tempDir);

                    return $this->common->API_Response(200, __('api_msg.upload_completed'), [
                        'file_path' => $datePath . '/' . $filename,
                        'full_url' => Storage::disk('public')->exists('content/' . $datePath . '/' . $filename),
                    ]);
                }
                return $this->common->API_Response(206, __('api_msg.upload_progress', ['current' => $chunkIndex, 'total' => $totalChunks]), ['directory' => $uploadId]);
            }

            // Final save
            $validation = Validator::make($request->all(), [
                'title' => 'required',
                'channel_id' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'radio' => 'required|string',
                'is_comment' => 'required',
                'is_like' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json(['status' => 400, 'message' => $validation->errors()->first()]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $insert = new Content();
            $insert['content_type'] = 6;
            $insert['channel_id'] = $requestData['channel_id'];
            $insert['category_id'] = 0;
            $insert['language_id'] = 0;
            $insert['description'] = $requestData['description'] ?? "";
            $hashtag_id = $this->common->checkHashTag($insert['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $insert['hashtag_id'] = $hashtagId;
            $insert['title'] = $requestData['title'];
            $insert['portrait_img_storage_type'] = $storage_type;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $insert['portrait_img'] = $this->common->saveImage($file1, $this->folder_content, 'port_', $insert['portrait_img_storage_type']);
            } else {
                $insert['portrait_img'] = "";
            }
            $insert['landscape_img_storage_type'] = $storage_type;
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $insert['landscape_img'] = $this->common->saveImage($file2, $this->folder_content, 'land_', $insert['landscape_img_storage_type']);
            } else {
                $insert['landscape_img'] = "";
            }
            $insert['content_upload_type'] = "server_video";
            if ($storage_type == 1) {

                $insert['content_storage_type'] = 1;
                $insert['content'] = $requestData['radio'];
            } else if ($storage_type == 2) {

                $insert['content_storage_type'] = 2;

                $localPath = storage_path("app/{$this->folder_content}/" . $requestData['radio']);
                // Upload to S3
                Storage::disk('s3')->put($this->folder_content . '/' . $requestData['radio'], file_get_contents($localPath));

                // Optional: delete local file after upload
                if (Storage::disk('s3')->exists($this->folder_content . '/' . $requestData['radio'])) {
                    $this->common->deleteImageToFolder($this->folder_content, $requestData['radio'], 1);
                }
                $insert['content'] = $requestData['radio'];
            }
            $insert['content_duration'] = 0;
            $insert['is_rent'] = 0;
            $insert['rent_price'] = 0;
            $insert['rent_day'] = 0;
            $insert['is_comment'] = $requestData['is_comment'];
            $insert['is_download'] = 0;
            $insert['is_like'] = $requestData['is_like'];
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['total_dislike'] = 0;
            $insert['playlist_type'] = 0;
            $insert['total_watch_time'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {
                return response()->json(['status' => 200, 'success' => __('api_msg.success_add_radio')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('api_msg.data_not_save')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create_playlist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'channel_id' => 'required',
                'title' => 'required',
                'playlist_type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $channel_id = $request['channel_id'];
            $title = $request['title'];
            $playlist_type = $request['playlist_type'];
            $description = $request['description'] ?? "";
            $storage_type = Storage_Type();

            $insert = new Content();
            $insert['content_type'] = 5;
            $insert['channel_id'] = $channel_id;
            $insert['category_id'] = 0;
            $insert['language_id'] = 0;
            $insert['hashtag_id'] = "";
            $insert['title'] = $title;
            $insert['description'] = $description;
            $insert['portrait_img_storage_type'] = $storage_type;
            $insert['portrait_img'] = "";
            $insert['landscape_img_storage_type'] = 0;
            $insert['landscape_img'] = "";
            $insert['content_storage_type'] = $storage_type;
            $insert['content_upload_type'] = "";
            $insert['content'] = "";
            $insert['content_duration'] = 0;
            $insert['is_rent'] = 0;
            $insert['rent_price'] = 0;
            $insert['rent_day'] = 0;
            $insert['is_comment'] = 0;
            $insert['is_download'] = 0;
            $insert['is_like'] = 0;
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['total_dislike'] = 0;
            $insert['playlist_type'] = $playlist_type;
            $insert['total_watch_time'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {
                return $this->common->API_Response(200, __('api_msg.playlist_created'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit_playlist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_id' => 'required|numeric',
                'title' => 'required',
                'playlist_type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_id = $request['content_id'];
            $title = $request['title'];
            $playlist_type = $request['playlist_type'];
            $description = $request['description'] ?? "";

            $update = Content::where('id', $content_id)->first();
            if ($update) {

                $update['title'] = $title;
                $update['playlist_type'] = $playlist_type;
                $update['description'] = $description;
                $update->save();
                return $this->common->API_Response(200, __('api_msg.playlist_updated'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_playlist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_id = $request['content_id'];

            Content::where('id', $content_id)->delete();
            Playlist_Content::where('playlist_id', $content_id)->delete();

            return $this->common->API_Response(200, __('api_msg.playlist_deleted'), []);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_content_to_playlist(Request $request) // Type = 0- Remove, 1- Add
    {
        try {
            $validation = Validator::make($request->all(), [
                'channel_id' => 'required',
                'playlist_id' => 'required|numeric',
                'type' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $channel_id = $request['channel_id'];
            $user_id = $this->common->getUserId($channel_id);
            $playlist_id = $request['playlist_id'];
            $type = $request['type'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];

            if ($type == 1) {

                $content = Playlist_Content::where('channel_id', $channel_id)->where('playlist_id', $playlist_id)->where('content_type', $content_type)->where('content_id', $content_id)->exists();
                if (!$content) {

                    $insert = new Playlist_Content();
                    $insert['channel_id'] = $channel_id;
                    $insert['playlist_id'] = $playlist_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['sort_order'] = 0;
                    $insert->save();

                    $this->common->add_interests($user_id, $content_id, 3, 1);
                }
                return $this->common->API_Response(200, __('api_msg.content_added'), []);
            } else if ($type == 0) {

                Playlist_Content::where('channel_id', $channel_id)->where('playlist_id', $playlist_id)->where('content_type', $content_type)->where('content_id', $content_id)->delete();
                $this->common->add_interests($user_id, $content_id, -3, 2);
                return $this->common->API_Response(200, __('api_msg.content_deleted'), []);
            } else {
                return $this->common->API_Response(200, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_multipal_content_to_playlist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'channel_id' => 'required',
                'playlist_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $channel_id = $request['channel_id'];
            $playlist_id = $request['playlist_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];

            $playlist = Content::where('content_type', 5)->where('id', $playlist_id)->exists();
            if ($playlist) {

                $content_ids = explode(",", $content_id);
                for ($i = 0; $i < count($content_ids); $i++) {

                    if ($content_ids[$i]) {

                        $check_ids = Playlist_Content::where('content_id', $content_ids[$i])->where('content_type', $content_type)->where('playlist_id', $playlist_id)->where('channel_id', $channel_id)->exists();
                        if (!$check_ids) {

                            $insert = new Playlist_Content();
                            $insert['channel_id'] = $channel_id;
                            $insert['playlist_id'] = $playlist_id;
                            $insert['content_type'] = $content_type;
                            $insert['content_id'] = $content_ids[$i];
                            $insert['sort_order'] = 1;
                            $insert['status'] = 1;
                            $insert->save();
                        }
                    }
                }
                return $this->common->API_Response(200, __('api_msg.content_added'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_playlist_content(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'playlist_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $playlist_id = $request['playlist_id'];
            $user_id = $request['user_id'] ?? 0;
            $content_type = $request['content_type'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $playlist_content = Playlist_Content::where('playlist_id', $playlist_id)->when($content_type != 0, fn($q) => $q->where('content_type', $content_type))->orderBy('sort_order', 'asc')->latest()->get();
            $content_id = $playlist_content->pluck('content_id')->toArray();
            if (empty($content_id)) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $data = Content::whereIn('id', $content_id)->orderByRaw("FIELD(id, " . implode(',', $content_id) . ")");

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                    $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                    if ($data[$i]['content_upload_type'] == 'server_video') {
                        $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                    }

                    $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                    $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                    $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                    $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                    $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                    $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                    $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                    $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_content_to_playlist(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Content::where('status', 1)->where('content_type', $content_type)->where('is_rent', 0);

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['portrait_img'], $data[$i]['portrait_img_storage_type']);
                    $data[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['landscape_img'], $data[$i]['landscape_img_storage_type']);
                    if ($data[$i]['content_upload_type'] == 'server_video') {
                        $data[$i]['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content'], $data[$i]['content_storage_type']);
                    }

                    $data[$i]['user_id'] = $this->common->getUserId($data[$i]['channel_id']);
                    $data[$i]['channel_name'] = $this->common->getChannelName($data[$i]['channel_id']);
                    $data[$i]['channel_image'] = $this->common->getChannelImage($data[$i]['channel_id']);
                    $data[$i]['category_name'] = $this->common->getCategoryName($data[$i]['category_id']);
                    $data[$i]['language_name'] = $this->common->getLanguageName($data[$i]['language_id']);
                    $data[$i]['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['user_id']);
                    $data[$i]['total_comment'] = $this->common->getTotalComment($data[$i]['id']);
                    $data[$i]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                    $data[$i]['total_subscriber'] = $this->common->total_subscriber($data[$i]['user_id']);
                    $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data[$i]['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content_type'], $data[$i]['id'], 0);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create_podcast(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'channel_id' => 'required',
                'category_id' => 'required',
                'language_id' => 'required',
                'title' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:10240',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $channel_id = $request['channel_id'];
            $category_id = $request['category_id'];
            $language_id = $request['language_id'];
            $title = $request['title'];
            $description = $request['description'] ?? "";
            $storage_type = Storage_Type();

            $insert = new Content();
            $insert['content_type'] = 4;
            $insert['channel_id'] = $channel_id;
            $insert['category_id'] = $category_id;
            $insert['language_id'] = $language_id;
            $hashtag_id = $this->common->checkHashTag($description);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $insert['hashtag_id'] = $hashtagId;
            $insert['title'] = $title;
            $insert['description'] = $description;
            $insert['portrait_img_storage_type'] = $storage_type;
            if (isset($requestData['portrait_img'])) {
                $file1 = $request['portrait_img'];
                $insert['portrait_img'] = $this->common->saveImage($file1, $this->folder_content, 'port_', $insert['portrait_img_storage_type']);
            } else {
                $insert['portrait_img'] = "";
            }
            $insert['landscape_img_storage_type'] = $storage_type;
            if (isset($requestData['landscape_img'])) {
                $file2 = $request['landscape_img'];
                $insert['landscape_img'] = $this->common->saveImage($file2, $this->folder_content, 'land_', $insert['landscape_img_storage_type']);
            } else {
                $insert['landscape_img'] = "";
            }
            $insert['content_storage_type'] = $storage_type;
            $insert['content_upload_type'] = "";
            $insert['content'] = "";
            $insert['content_duration'] = 0;
            $insert['is_rent'] = 0;
            $insert['rent_price'] = 0;
            $insert['rent_day'] = 0;
            $insert['is_comment'] = 0;
            $insert['is_download'] = 0;
            $insert['is_like'] = 0;
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['total_dislike'] = 0;
            $insert['playlist_type'] = 0;
            $insert['total_watch_time'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {

                $insert['portrait_img'] = $this->common->getImage($this->folder_content, $insert['portrait_img'], $storage_type);
                $insert['landscape_img'] = $this->common->getImage($this->folder_content, $insert['landscape_img'], $storage_type);

                return $this->common->API_Response(200, __('api_msg.podcasts_created'), $insert);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function upload_episode(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if (!$user || $user->role !== 'artist') {
                return $this->common->API_Response(400, __('api_msg.only_artists_can_upload'));
            }

            // Check if this is a chunk upload
            if ($request->has('chunk_index') && $request->has('total_chunks')) {

                $validation = Validator::make($request->all(), [
                    'chunk_index' => 'required|integer|min:0',
                    'total_chunks' => 'required|integer|min:1',
                    'file' => 'required|file',
                ]);
                if ($validation->fails()) {
                    return $this->common->API_Response(400, $validation->errors()->first());
                }

                $chunkIndex = (int) $request['chunk_index'];
                $totalChunks = (int) $request['total_chunks'];
                $uploadId = $request['directory'] ?? now()->format('ymd_His_') . uniqid();

                // Save chunk in temp directory
                $tempDir = storage_path("app/public/chunks/{$uploadId}");
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                // Save chunk
                $request->file('file')->move($tempDir, "chunk_{$chunkIndex}");

                // Merge when all chunks are received
                $uploadedChunks = glob($tempDir . '/chunk_*');
                if (count($uploadedChunks) == $totalChunks) {

                    $datePath = date('Y') . '/' . date('m');
                    $filename = 'ep_vid_' . date('Y_m_d_') . uniqid() . '.mp3';
                    $contentDir = storage_path("app/public/content/{$datePath}");
                    if (!file_exists($contentDir)) { mkdir($contentDir, 0777, true); }
                    $finalPath = $contentDir . '/' . $filename;
                    $output = fopen($finalPath, 'wb');

                    for ($i = 0; $i < $totalChunks; $i++) {

                        $chunk = fopen("{$tempDir}/chunk_{$i}", 'rb');
                        stream_copy_to_stream($chunk, $output);
                        fclose($chunk);
                        unlink("{$tempDir}/chunk_{$i}");
                    }

                    fclose($output);
                    rmdir($tempDir);

                    return $this->common->API_Response(200, __('api_msg.upload_completed'), [
                        'file_path' => $datePath . '/' . $filename,
                        'full_url' => Storage::disk('public')->exists('content/' . $datePath . '/' . $filename),
                    ]);
                }
                return $this->common->API_Response(206, __('api_msg.upload_progress', ['current' => $chunkIndex, 'total' => $totalChunks]), ['directory' => $uploadId]);
            }

            // Final save
            $validation = Validator::make($request->all(), [
                'podcasts_id' => 'required',
                'name' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:10240',
                'episode' => 'required|string',
                'is_comment' => 'required',
                'is_download' => 'required',
                'is_like' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json(['status' => 400, 'message' => $validation->errors()->first()]);
            }

            $requestData = $request->all();
            $storage_type = Storage_Type();

            $insert = new Episode();
            $insert['podcasts_id'] = $requestData['podcasts_id'];
            $insert['name'] = $requestData['name'];
            $insert['description'] = $requestData['description'] ?? "";
            $insert['portrait_img_storage_type'] = $storage_type;
            if (isset($requestData['portrait_img'])) {
                $file1 = $requestData['portrait_img'];
                $insert['portrait_img'] = $this->common->saveImage($file1, $this->folder_content, 'port_', $insert['portrait_img_storage_type']);
            } else {
                $insert['portrait_img'] = "";
            }
            $insert['landscape_img_storage_type'] = $storage_type;
            if (isset($requestData['landscape_img'])) {
                $file2 = $requestData['landscape_img'];
                $insert['landscape_img'] = $this->common->saveImage($file2, $this->folder_content, 'land_', $insert['landscape_img_storage_type']);
            } else {
                $insert['landscape_img'] = "";
            }
            $insert['episode_upload_type'] = "server_audio";
            if ($storage_type == 1) {

                $insert['episode_storage_type'] = 1;
                $insert['episode_audio'] = $requestData['episode'];
            } else if ($storage_type == 2) {

                $insert['episode_storage_type'] = 2;

                $localPath = storage_path("app/{$this->folder_content}/" . $requestData['episode']);
                // Upload to S3
                Storage::disk('s3')->put($this->folder_content . '/' . $requestData['episode'], file_get_contents($localPath));

                // Optional: delete local file after upload
                if (Storage::disk('s3')->exists($this->folder_content . '/' . $requestData['episode'])) {
                    $this->common->deleteImageToFolder($this->folder_content, $requestData['episode'], 1);
                }
                $insert['episode_audio'] = $requestData['episode'];
            }
            $insert['is_comment'] = $requestData['is_comment'];
            $insert['is_download'] = $requestData['is_download'];
            $insert['is_like'] = $requestData['is_like'];
            $insert['total_view'] = 0;
            $insert['total_like'] = 0;
            $insert['sort_order'] = 0;
            $insert['status'] = 1;
            if ($insert->save()) {
                return response()->json(['status' => 200, 'success' => __('api_msg.success_add_episode')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('api_msg.data_not_save')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
