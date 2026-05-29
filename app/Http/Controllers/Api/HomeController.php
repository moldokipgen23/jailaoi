<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Ads_View_Click_Count;
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
use App\Models\General_Setting;
use App\Models\Gift;
use App\Models\Gift_Transaction;
use App\Models\History;
use App\Models\Notification;
use App\Models\Onboarding_Screen;
use App\Models\Package;
use App\Models\Package_Detail;
use App\Models\Page;
use App\Models\Payment_Option;
use App\Models\Playlist_Content;
use App\Models\Read_Notification;
use App\Models\Reason;
use App\Models\Rent_Section;
use App\Models\Rent_Transaction;
use App\Models\Send_Gift;
use App\Models\Social_Link;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Watch_later;
use App\Models\Withdrawal_Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

// 1- Video, 2- Music, 3- Reels, 4- Podcasts, 5- Playlist, 6- Radio	
class HomeController extends Controller
{
    private $folder_setting = "setting";
    private $folder_package = "package";
    private $folder_user = "user";
    private $folder_content = "content";
    private $folder_feed = "feed";
    private $folder_ads = "ads";
    private $folder_gift = "gift";
    private $folder_notification = "notification";
    public $common;
    public $page_limit;
    public function __construct()
    {
        try {

            $this->common = new Common();
            $this->page_limit = env('PAGE_LIMIT');
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function general_setting()
    {
        try {

            $list = General_Setting::get();
            return $this->common->API_Response(200, __('api_msg.data_retrieved'), $list);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_payment_option()
    {
        try {

            $return['status'] = 200;
            $return['message'] = __('api_msg.data_retrieved');
            $return['result'] = [];

            $data = Payment_Option::get();
            foreach ($data as $key => $value) {
                $return['result'][$value['name']] = $value;
            }

            return $return;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_pages()
    {
        try {
            $return['status'] = 200;
            $return['message'] = __('api_msg.data_retrieved');
            $return['result'] = [];

            $data = Page::where('status', 1)->get();
            for ($i = 0; $i < count($data); $i++) {
                $return['result'][$i]['title'] = $data[$i]['title'];
                $return['result'][$i]['url'] = route('page.view', $data[$i]['title']);
                $return['result'][$i]['icon'] = $this->common->getImage($this->folder_setting, $data[$i]['icon'], $data[$i]['storage_type']);
            }
            return $return;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_social_link()
    {
        try {
            $data = Social_Link::get();
            if (sizeof($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_setting);
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_onboarding_screen()
    {
        try {
            $data = Onboarding_Screen::get();
            if (sizeof($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_setting);
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_report_reason(Request $request)
    {
        try {
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Reason::where('status', 1)->orderBy('id', 'desc');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get();

            if (count($data) > 0) {
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
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
            $this->common->package_expiry();

            $user_id = $request['user_id'] ?? 0;

            $data['status'] = 200;
            $data['message'] = __('api_msg.data_retrieved');
            $data['result'] = [];

            $package_data = Package::where('status', 1)->orderBy('price', 'asc')->latest()->get();
            foreach ($package_data as $key => $value) {

                $value['image'] = $this->common->getImage($this->folder_package, $value['image'], $value['storage_type']);
                $value['is_buy'] = $this->common->is_package_buy($user_id, $value['id']);

                $detail = Package_Detail::select('package_key', 'package_value')->where('package_id', $value['id'])->get();
                $value['data'] = $detail;

                $data['result'][] = $value;
            }
            return $data;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_coin_package(Request $request)
    {
        try {
            $data = Coin_Package::where('status', 1)->orderBy('price', 'asc')->latest()->get();
            if (count($data) > 0) {

                $this->common->imageNameToUrl($data, 'image', $this->folder_package);
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_view(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
                'episode_id' => 'numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;

            if ($content_type == 1 || $content_type == 2 || $content_type == 3 || $content_type == 5 || $content_type == 6) {

                $content_view = Content_View::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->first();
                if (!$content_view) {

                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = 0;
                    $insert['status'] = 1;
                    Content_View::insertGetId($insert);
                    Content::where('id', $content_id)->increment('total_view', 1);

                    $this->common->add_interests($user_id, $content_id, 1, 1);
                }
            } else if ($content_type == 4) {

                $content_view = Content_View::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->first();
                if (!$content_view) {

                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = $episode_id;
                    $insert['status'] = 1;
                    Content_View::insertGetId($insert);
                    Content::where('id', $content_id)->increment('total_view', 1);
                    Episode::where('id', $episode_id)->increment('total_view', 1);

                    $this->common->add_interests($user_id, $content_id, 1, 1);
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
            return $this->common->API_Response(200, __('api_msg.view_add_successfully'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_content_report(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'report_user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
                'message' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $report_user_id = $request['report_user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;
            $message = $request['message'];

            $report = Content_Report::where('user_id', $user_id)->where('report_user_id', $report_user_id)->where('episode_id', $episode_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('status', 1)->first();
            if (!$report) {

                $insert['user_id'] = $user_id;
                $insert['report_user_id'] = $report_user_id;
                $insert['content_type'] = $content_type;
                $insert['content_id'] = $content_id;
                $insert['episode_id'] = $episode_id;
                $insert['message'] = $message;
                $insert['status'] = 1;
                Content_Report::insertGetId($insert);

                // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                $user = User::where('id', $report_user_id)->first();
                $content = Content::where('id', $content_id)->first();
                if ($user != null && isset($user) && $user['email'] != "" && isset($user['email']) && $content != null && isset($content)) {
                    $this->common->Send_Mail(3, $user['email'], $content['title'], $message);
                }
            }
            return $this->common->API_Response(200, __('api_msg.report_added'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_like_dislike(Request $request) // 0-Remove, 1-Like, 2-Dislike
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
                'status' => 'required|numeric',
                'episode_id' => 'numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;
            $status = $request['status'];

            if ($content_type == 1 || $content_type == 2 || $content_type == 3 || $content_type == 6) {

                $like = Content_Like::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->first();
                if ($like) {

                    $old_status = $like['status'];

                    $like['status'] = $status;
                    if ($like->save()) {

                        if ($old_status == 1) {

                            if ($status == 2) {
                                Content::where('id', $content_id)->decrement('total_like', 1);
                                Content::where('id', $content_id)->increment('total_dislike', 1);
                            } else if ($status == 0) {
                                Content::where('id', $content_id)->decrement('total_like', 1);
                            }
                        } else if ($old_status == 2) {

                            if ($status == 1) {
                                Content::where('id', $content_id)->increment('total_like', 1);
                                Content::where('id', $content_id)->decrement('total_dislike', 1);
                            } else if ($status == 0) {
                                Content::where('id', $content_id)->decrement('total_dislike', 1);
                            }
                        } else if ($old_status == 0) {

                            if ($status == 1) {
                                Content::where('id', $content_id)->increment('total_like', 1);
                            } else if ($status == 2) {
                                Content::where('id', $content_id)->increment('total_dislike', 1);
                            }
                        }

                        if ($status == 1) {

                            $this->common->add_interests($user_id, $content_id, 2, 1);
                            return $this->common->API_Response(200, __('api_msg.like_successfully'));
                        } else if ($status == 2) {

                            $this->common->add_interests($user_id, $content_id, -2, 2);
                            return $this->common->API_Response(200, __('api_msg.dislike_successfully'));
                        } else {

                            $this->common->add_interests($user_id, $content_id, -2, 2);
                            return $this->common->API_Response(200, __('api_msg.removed'));
                        }
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                } else {

                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = 0;
                    $insert['status'] = $status;
                    Content_Like::insertGetId($insert);

                    if ($status == 1) {
                        Content::where('id', $content_id)->increment('total_like', 1);

                        // Send Notification
                        $user = User::find($user_id);
                        $content = Content::where('id', $content_id)->with('channel')->first();
                        if ($user && $content && $content['channel'] != null && $user_id != $content['channel']['id'] && $content['channel']['push_notification_status'] == 1) {

                            $title = $user['channel_name'] . ' Liked your Post.';
                            $this->common->save_notification(2, $title, $user_id, $content['channel']['id'], $content_id);
                        }
                    } else if ($status == 2) {
                        Content::where('id', $content_id)->increment('total_dislike', 1);
                    }

                    if ($status == 1) {

                        $this->common->add_interests($user_id, $content_id, 2, 1);
                        return $this->common->API_Response(200, __('api_msg.like_successfully'));
                    } else if ($status == 2) {

                        $this->common->add_interests($user_id, $content_id, -2, 2);
                        return $this->common->API_Response(200, __('api_msg.dislike_successfully'));
                    } else {

                        $this->common->add_interests($user_id, $content_id, -2, 2);
                        return $this->common->API_Response(200, __('api_msg.removed'));
                    }
                }
            } else if ($content_type == 4) {

                $like = Content_Like::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->first();
                if ($like) {
                    $old_status = $like['status'];

                    $like['status'] = $status;
                    if ($like->save()) {

                        if ($old_status == 1) {

                            if ($status == 2) {
                                Content::where('id', $content_id)->decrement('total_like', 1);
                                Content::where('id', $content_id)->increment('total_dislike', 1);

                                Episode::where('id', $episode_id)->decrement('total_like', 1);
                                Episode::where('id', $episode_id)->increment('total_dislike', 1);
                            } else if ($status == 0) {
                                Content::where('id', $content_id)->decrement('total_like', 1);
                                Episode::where('id', $episode_id)->decrement('total_like', 1);
                            }
                        } else if ($old_status == 2) {

                            if ($status == 1) {
                                Content::where('id', $content_id)->increment('total_like', 1);
                                Content::where('id', $content_id)->decrement('total_dislike', 1);

                                Episode::where('id', $episode_id)->increment('total_like', 1);
                                Episode::where('id', $episode_id)->decrement('total_dislike', 1);
                            } else if ($status == 0) {
                                Content::where('id', $content_id)->decrement('total_dislike', 1);
                                Episode::where('id', $episode_id)->decrement('total_dislike', 1);
                            }
                        } else if ($old_status == 0) {

                            if ($status == 1) {
                                Content::where('id', $content_id)->increment('total_like', 1);
                                Episode::where('id', $episode_id)->increment('total_like', 1);
                            } else if ($status == 2) {
                                Content::where('id', $content_id)->increment('total_dislike', 1);
                                Episode::where('id', $episode_id)->increment('total_dislike', 1);
                            }
                        }

                        if ($status == 1) {

                            $this->common->add_interests($user_id, $content_id, 2, 1);
                            return $this->common->API_Response(200, __('api_msg.like_successfully'));
                        } else if ($status == 2) {

                            $this->common->add_interests($user_id, $content_id, -2, 2);
                            return $this->common->API_Response(200, __('api_msg.dislike_successfully'));
                        } else {

                            $this->common->add_interests($user_id, $content_id, -2, 2);
                            return $this->common->API_Response(200, __('api_msg.removed'));
                        }
                    } else {
                        return $this->common->API_Response(400, __('api_msg.data_not_save'));
                    }
                } else {

                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = $episode_id;
                    $insert['status'] = $status;
                    Content_Like::insertGetId($insert);

                    if ($status == 1) {
                        Content::where('id', $content_id)->increment('total_like', 1);
                        Episode::where('id', $episode_id)->increment('total_like', 1);

                        // Send Notification
                        $user = User::find($user_id);
                        $content = Content::where('id', $content_id)->with('channel')->first();
                        if ($user && $content && $content['channel'] != null && $user_id != $content['channel']['id'] && $content['channel']['push_notification_status'] == 1) {

                            $title = $user['channel_name'] . ' Liked your Post.';
                            $this->common->save_notification(2, $title, $user_id, $content['channel']['id'], $content_id);
                        }
                    } else if ($status == 2) {
                        Content::where('id', $content_id)->increment('total_dislike', 1);
                        Episode::where('id', $episode_id)->increment('total_dislike', 1);
                    }

                    if ($status == 1) {

                        $this->common->add_interests($user_id, $content_id, 2, 1);
                        return $this->common->API_Response(200, __('api_msg.like_successfully'));
                    } else if ($status == 2) {

                        $this->common->add_interests($user_id, $content_id, -2, 2);
                        return $this->common->API_Response(200, __('api_msg.dislike_successfully'));
                    } else {

                        $this->common->add_interests($user_id, $content_id, -2, 2);
                        return $this->common->API_Response(200, __('api_msg.removed'));
                    }
                }
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
                'comment' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'] ?? 0;
            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;
            $comment = $request['comment'];

            $insert = new Comment();
            $insert['comment_id'] = $comment_id;
            $insert['user_id'] = $user_id;
            $insert['content_type'] = $content_type;
            $insert['content_id'] = $content_id;
            $insert['episode_id'] = $episode_id;
            $insert['comment'] = $comment;
            $insert->save();

            $this->common->add_interests($user_id, $content_id, 3, 1);
            // Send Notification
            if ($content_type == 1 || $content_type == 2 || $content_type == 3 || $content_type == 4) {

                $user = User::find($user_id);
                $content = Content::where('id', $content_id)->with('channel')->first();
                if ($user && $content && $content['channel'] != null && $user_id != $content['channel']['id'] && $content['channel']['push_notification_status'] == 1) {

                    $title = $user['channel_name'] . ' Commented on your Post.';
                    $this->common->save_notification(3, $title, $user_id, $content['channel']['id'], $content_id);
                }
            }
            return $this->common->API_Response(200, __('api_msg.comment_added'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'comment_id' => 'required|numeric',
                'comment' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $comment_id = $request['comment_id'];
            $comment = $request['comment'];

            $update = Comment::where('id', $comment_id)->first();
            if ($update) {

                $update['comment'] = $comment;
                $update->save();
                return $this->common->API_Response(200, __('api_msg.comment_edited'));
            }
            return $this->common->API_Response(200, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            Comment::where('id', $request['comment_id'])->delete();
            return $this->common->API_Response(200, __('api_msg.comment_deleted'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            if ($content_type == 1 || $content_type == 2 || $content_type == 3 || $content_type == 6) {
                $data = Comment::where('comment_id', 0)->where('content_type', $content_type)->where('content_id', $content_id)->where('status', 1)->orderBy('id', 'desc')->with('user');
            } else if ($content_type == 4) {
                $data = Comment::where('comment_id', 0)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->where('status', 1)->orderBy('id', 'desc')->with('user');
            }

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image']) : asset('assets/imgs/default.png');
                    unset($data[$i]['user']);

                    $data[$i]['is_reply'] = 0;
                    $data[$i]['total_reply'] = 0;
                    $reply = Comment::where('comment_id', $data[$i]['id'])->count();
                    if ($reply != 0) {
                        $data[$i]['is_reply'] = 1;
                        $data[$i]['total_reply'] = $reply;
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_reply_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Comment::where('comment_id', $comment_id)->where('status', 1)->orderBy('id', 'desc')->with('user');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image']) : asset('/assets/imgs/default.png');

                    unset($data[$i]['user']);

                    $data[$i]['is_reply'] = 0;
                    $data[$i]['total_reply'] = 0;
                    $reply = Comment::where('comment_id', $data[$i]['id'])->count();
                    if ($reply != 0) {
                        $data[$i]['is_reply'] = 1;
                        $data[$i]['total_reply'] = $reply;
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_remove_watch_later(Request $request) // Type = 0-Remove, 1-Add
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required',
                'type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $type = $request['type'];
            $episode_id = $request['episode_id'] ?? 0;

            if ($type == 1) {

                $watch_later = Watch_later::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->first();
                if (!$watch_later) {

                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = $episode_id;
                    $insert['status'] = 1;
                    Watch_later::insertGetId($insert);
                    $this->common->add_interests($user_id, $content_id, 4, 1);
                }
                return $this->common->API_Response(200, __('api_msg.watch_later_added'));
            } else if ($type == 0) {

                Watch_later::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                $this->common->add_interests($user_id, $content_id, -4, 2);
                return $this->common->API_Response(200, __('api_msg.watch_later_removed'));
            } else {
                return $this->common->API_Response(200, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_content_detail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'] ?? 0;
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];

            $content = Content::where('id', $content_id)->where('content_type', $content_type)->where('is_rent', 0)->where('status', 1)->with('channel')->first();
            if ($content) {

                $content['portrait_img'] = $this->common->getImage($this->folder_content, $content['portrait_img'], $content['portrait_img_storage_type']);
                $content['landscape_img'] = $this->common->getImage($this->folder_content, $content['landscape_img'], $content['landscape_img_storage_type']);
                if ($content['content_upload_type'] == 'server_video') {
                    $content['content'] = $this->common->getVideo($this->folder_content, $content['content'], $content['content_storage_type']);
                }

                $content['user_id'] = $this->common->getUserId($content['channel_id']);
                $content['channel_name'] = $this->common->getChannelName($content['channel_id']);
                $content['channel_image'] = $this->common->getChannelImage($content['channel_id']);
                $content['category_name'] = $this->common->getCategoryName($content['category_id']);
                $content['language_name'] = $this->common->getLanguageName($content['language_id']);
                $content['is_subscribe'] = isset($content['channel']) ? $this->common->is_subscribe(1, $user_id, $content['channel']['id']) : 0;
                unset($content['channel']);
                $content['total_comment'] = $this->common->getTotalComment($content['id']);
                $content['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $content['content_type'], $content['id'], 0);
                $content['total_subscriber'] = $this->common->total_subscriber($content['user_id']);
                $content['is_buy'] = $this->common->is_any_package_buy($user_id);
                $content['stop_time'] = $this->common->getContentStopTime($user_id, $content['content_type'], $content['id'], 0);
                $content['is_user_download'] = $this->common->is_user_download_content($user_id);

                $content['playlist_image'] = [];
                if ($content_type == 5) {

                    // Playlist Image
                    $image_array = [];
                    $playlist_content = Playlist_Content::where('playlist_id', $content['id'])->whereIn('content_type', [1, 2])->orderBy('sort_order', 'asc')->with('Content')->latest()->get();
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
                    $content['playlist_image'] = $image_array;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), array($content));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_like_content(Request $request)
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

            $data = Content_Like::where('user_id', $user_id)->where('content_type', $content_type)->where('status', 1)->with('content', 'episode')->orderBy('id', 'desc')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get()->toArray();

            if (count($data) > 0) {

                $content_data = [];
                for ($i = 0; $i < count($data); $i++) {

                    if (isset($data[$i]['content']) && $data[$i]['content'] != null) {

                        $data[$i]['content']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['portrait_img'], $data[$i]['content']['portrait_img_storage_type']);
                        $data[$i]['content']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['landscape_img'], $data[$i]['content']['landscape_img_storage_type']);
                        if ($data[$i]['content']['content_upload_type'] == 'server_video') {
                            $data[$i]['content']['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content']['content'], $data[$i]['content']['content_storage_type']);
                        }
                        $data[$i]['content']['user_id'] = $this->common->getUserId($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_name'] = $this->common->getChannelName($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_image'] = $this->common->getChannelImage($data[$i]['content']['channel_id']);
                        $data[$i]['content']['category_name'] = $this->common->getCategoryName($data[$i]['content']['category_id']);
                        $data[$i]['content']['language_name'] = $this->common->getLanguageName($data[$i]['content']['language_id']);
                        $data[$i]['content']['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['content']['user_id']);
                        $data[$i]['content']['total_comment'] = $this->common->getTotalComment($data[$i]['content']['id']);
                        $data[$i]['content']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], 0);
                        $data[$i]['content']['total_subscriber'] = $this->common->total_subscriber($data[$i]['content']['user_id']);
                        $data[$i]['content']['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['content']['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], 0);

                        $data[$i]['content']['episode'] = [];
                        if (isset($data[$i]['episode']) && $data[$i]['episode'] != null) {

                            $data[$i]['episode']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['portrait_img'], $data[$i]['episode']['portrait_img_storage_type']);
                            $data[$i]['episode']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['landscape_img'], $data[$i]['episode']['landscape_img_storage_type']);
                            if ($data[$i]['episode']['episode_upload_type'] == 'server_video') {
                                $data[$i]['episode']['episode_audio'] = $this->common->getVideo($this->folder_content, $data[$i]['episode']['episode_audio'], $data[$i]['episode']['episode_storage_type']);
                            }
                            $data[$i]['episode']['podcast_name'] = $data[$i]['content']['title'];
                            $data[$i]['episode']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], $data[$i]['episode']['id']);
                            $data[$i]['episode']['is_buy'] = $this->common->is_any_package_buy($user_id);

                            $data[$i]['content']['episode'][] = $data[$i]['episode'];
                        }

                        $content_data[] = $data[$i]['content'];
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $content_data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_content_by_channel(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'channel_id' => 'required',
                'content_type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $channel_id = $request['channel_id'];
            $content_type = $request['content_type'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Content::where('content_type', $content_type)->where('channel_id', $channel_id)->where('status', 1)->where('is_rent', 0)->orderby('id', 'desc');

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
    public function get_watch_later_content(Request $request)
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

            $data = Watch_later::where('user_id', $user_id)->where('content_type', $content_type)->where('status', 1)->with('content', 'episode')->orderBy('id', 'desc')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get()->toArray();

            if (count($data) > 0) {

                $content_data = [];
                for ($i = 0; $i < count($data); $i++) {

                    if (isset($data[$i]['content']) && $data[$i]['content'] != null) {

                        $data[$i]['content']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['portrait_img'], $data[$i]['content']['portrait_img_storage_type']);
                        $data[$i]['content']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['landscape_img'], $data[$i]['content']['landscape_img_storage_type']);
                        if ($data[$i]['content']['content_upload_type'] == 'server_video') {
                            $data[$i]['content']['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content']['content'], $data[$i]['content']['content_storage_type']);
                        }
                        $data[$i]['content']['user_id'] = $this->common->getUserId($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_name'] = $this->common->getChannelName($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_image'] = $this->common->getChannelImage($data[$i]['content']['channel_id']);
                        $data[$i]['content']['category_name'] = $this->common->getCategoryName($data[$i]['content']['category_id']);
                        $data[$i]['content']['language_name'] = $this->common->getLanguageName($data[$i]['content']['language_id']);
                        $data[$i]['content']['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['content']['user_id']);
                        $data[$i]['content']['total_comment'] = $this->common->getTotalComment($data[$i]['content']['id']);
                        $data[$i]['content']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], 0);
                        $data[$i]['content']['total_subscriber'] = $this->common->total_subscriber($data[$i]['content']['user_id']);
                        $data[$i]['content']['is_buy'] = $this->common->is_any_package_buy($user_id);
                        $data[$i]['content']['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], 0);

                        $data[$i]['content']['episode'] = [];
                        if ($data[$i]['episode'] != null && isset($data[$i]['episode'])) {

                            $data[$i]['episode']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['portrait_img'], $data[$i]['episode']['portrait_img_storage_type']);
                            $data[$i]['episode']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['landscape_img'], $data[$i]['episode']['landscape_img_storage_type']);
                            if ($data[$i]['episode']['episode_upload_type'] == 'server_video') {
                                $data[$i]['episode']['episode_audio'] = $this->common->getVideo($this->folder_content, $data[$i]['episode']['episode_audio'], $data[$i]['episode']['episode_storage_type']);
                            }
                            $data[$i]['episode']['podcast_name'] = $data[$i]['content']['title'];
                            $data[$i]['episode']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], $data[$i]['episode']['id']);
                            $data[$i]['episode']['stop_time'] = $this->common->getContentStopTime($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], $data[$i]['episode']['id']);

                            $data[$i]['content']['episode'][] = $data[$i]['episode'];
                        }
                        $content_data[] = $data[$i]['content'];
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $content_data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_content_to_history(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
                'stop_time' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;
            $stop_time = $request['stop_time'];

            $content = History::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->first();
            if ($content != null && isset($content)) {

                if ($content_type == 1 || $content_type == 2 || $content_type == 4 || $content_type == 6) {

                    $content['stop_time'] = $stop_time;
                    $content->save();

                    // Total Watch Time
                    Content::where('id', $content_id)->increment('total_watch_time', $stop_time);
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_save'));
                }
            } else {

                if ($content_type == 1 || $content_type == 2 || $content_type == 4 || $content_type == 6) {

                    $insert = new History();
                    $insert['user_id'] = $user_id;
                    $insert['content_type'] = $content_type;
                    $insert['content_id'] = $content_id;
                    $insert['episode_id'] = $episode_id;
                    $insert['stop_time'] = $stop_time;
                    $insert['status'] = 1;
                    $insert->save();

                    // Total Watch Time
                    Content::where('id', $content_id)->increment('total_watch_time', $stop_time);
                } else {
                    return $this->common->API_Response(400, __('api_msg.data_not_save'));
                }
            }
            return $this->common->API_Response(200, __('api_msg.content_added'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function remove_content_to_history(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_type = $request['content_type'];
            $content_id = $request['content_id'];
            $episode_id = $request['episode_id'] ?? 0;

            History::where('user_id', $user_id)->where('content_type', $content_type)->where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
            return $this->common->API_Response(200, __('api_msg.content_deleted'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_content_to_history(Request $request)
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

            $data = History::where('user_id', $user_id)->where('content_type', $content_type)->where('status', 1)->with('content', 'episode')->orderBy('id', 'desc')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get()->toArray();

            if (count($data) > 0) {

                $content_data = [];
                for ($i = 0; $i < count($data); $i++) {

                    if (isset($data[$i]['content']) && $data[$i]['content'] != null) {

                        $data[$i]['content']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['portrait_img'], $data[$i]['content']['portrait_img_storage_type']);
                        $data[$i]['content']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['content']['landscape_img'], $data[$i]['content']['landscape_img_storage_type']);
                        if ($data[$i]['content']['content_upload_type'] == 'server_video') {
                            $data[$i]['content']['content'] = $this->common->getVideo($this->folder_content, $data[$i]['content']['content'], $data[$i]['content']['content_storage_type']);
                        }

                        $data[$i]['content']['user_id'] = $this->common->getUserId($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_name'] = $this->common->getChannelName($data[$i]['content']['channel_id']);
                        $data[$i]['content']['channel_image'] = $this->common->getChannelImage($data[$i]['content']['channel_id']);
                        $data[$i]['content']['category_name'] = $this->common->getCategoryName($data[$i]['content']['category_id']);
                        $data[$i]['content']['language_name'] = $this->common->getLanguageName($data[$i]['content']['language_id']);
                        $data[$i]['content']['is_subscribe'] = $this->common->is_subscribe($user_id, $data[$i]['content']['user_id']);
                        $data[$i]['content']['total_comment'] = $this->common->getTotalComment($data[$i]['content']['id']);
                        $data[$i]['content']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], 0);
                        $data[$i]['content']['total_subscriber'] = $this->common->total_subscriber($data[$i]['content']['user_id']);
                        $data[$i]['content']['stop_time'] = $data[$i]['stop_time'];
                        $data[$i]['content']['is_buy'] = $this->common->is_any_package_buy($user_id);

                        $data[$i]['content']['episode'] = [];
                        if ($data[$i]['episode'] != null && isset($data[$i]['episode'])) {

                            $data[$i]['episode']['portrait_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['portrait_img'], $data[$i]['episode']['portrait_img_storage_type']);
                            $data[$i]['episode']['landscape_img'] = $this->common->getImage($this->folder_content, $data[$i]['episode']['landscape_img'], $data[$i]['episode']['landscape_img_storage_type']);
                            if ($data[$i]['episode']['episode_upload_type'] == 'server_video') {
                                $data[$i]['episode']['episode_audio'] = $this->common->getVideo($this->folder_content, $data[$i]['episode']['episode_audio'], $data[$i]['episode']['episode_storage_type']);
                            }
                            $data[$i]['episode']['podcast_name'] = $data[$i]['content']['title'];
                            $data[$i]['episode']['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$i]['content']['content_type'], $data[$i]['content']['id'], $data[$i]['episode']['id']);
                            $data[$i]['episode']['stop_time'] = $data[$i]['stop_time'];

                            $data[$i]['content']['episode'][] = $data[$i]['episode'];
                        }
                        $content_data[] = $data[$i]['content'];
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $content_data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_episode_by_podcasts(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'podcasts_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'] ?? 0;
            $podcasts_id = $request['podcasts_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Episode::where('podcasts_id', $podcasts_id)->with('Content')->orderBy('sort_order', 'asc')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->get();

            foreach ($data as $key => $value) {

                $value['portrait_img'] = $this->common->getImage($this->folder_content, $value['portrait_img'], $value['portrait_img_storage_type']);
                $value['landscape_img'] = $this->common->getImage($this->folder_content, $value['landscape_img'], $value['landscape_img_storage_type']);
                if ($value['episode_upload_type'] == 'server_audio') {
                    $value['episode_audio'] = $this->common->getVideo($this->folder_content, $value['episode_audio'], $value['episode_storage_type']);
                }
                $value['is_buy'] = $this->common->is_any_package_buy($user_id);

                $value['podcasts_name'] = $value['Content']['title'] ?? "";
                $value['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $value['Content']['content_type'], $value['Content']['id'], $value['id']) ?? 0;
                $value['user_id'] = $this->common->getUserId($value['Content']['channel_id']) ?? 0;
                $value['stop_time'] = $this->common->getContentStopTime($user_id, $value['Content']['content_type'], $value['Content']['id'], 0) ?? 0;
                unset($value['Content']);
            }
            return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_feed(Request $request)
    {
        try {
            $this->common->package_expiry();

            $user_id = $request['user_id'];
            $page_no = $request['page_no'] ?? 1;

            if ($user_id != 0) {

                $block_channel_list = $this->common->get_block_channel($user_id);
                $get_subscriber = $this->common->get_subscriber($user_id);
                $get_interests_hashtag = $this->common->get_interests_hashtag($user_id);
                $last_24_hours = now()->subHours(24)->toDateTimeString();

                // Step 1: Get IDs
                $content_ids = Feed::where('status', 1)->whereNotIn('channel_id', $block_channel_list)->latest()->pluck('id')->toArray();

                // Step 2: Recent Content
                $recent_data = Feed::whereIn('id', $content_ids)->whereIn('channel_id', $get_subscriber)->where('created_at', '>', $last_24_hours)->orderByDesc('total_like')->pluck('id')->toArray();

                // Step 3: Interests Content
                $interests_data = Feed::whereIn('id', $content_ids)->whereNotIn('id', $recent_data)->whereIn('hashtag_id', $get_interests_hashtag)->orderByDesc('total_like')->pluck('id')->toArray();

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
                    $data = Feed::whereIn('id', $current_page_ids)->orderByRaw("FIELD(id, " . implode(',', $current_page_ids) . ")")->with('channel')->get()->shuffle()->toArray();
                } else {
                    $data = [];
                }
            } else {
                $data = Feed::where('status', 1)->with('channel')->latest()->get()->shuffle()->toArray();
                $total = count($data);
            }

            $paginator = new LengthAwarePaginator($data, $total, $this->page_limit, $page_no);
            $more_page = $this->common->more_page($page_no, $paginator->lastPage());
            $pagination = $this->common->pagination_array($total, $paginator->lastPage(), $page_no, $more_page);

            if (count($data) > 0) {

                $feed = array();
                foreach ($data as $value) {

                    $value['feed_content'] = [];
                    $feed_content = Feed_Content::where('feed_id', $value['id'])->get();
                    foreach ($feed_content as $content) {

                        $content['image'] = $this->common->getImage($this->folder_feed, $content['image'], $content['image_storage_type']);
                        if ($content['content_type'] == 2) {
                            $content['video'] = $this->common->getVideo($this->folder_feed, $content['video'], $content['video_storage_type']);
                        }
                        $value['feed_content'][] = $content;
                    }
                    $value['hastegs'] = $this->common->getHashTag($value['hashtag_id']);

                    // User
                    $value['user_id'] = $value['channel']['id'] ?? 0;
                    $value['channel_name'] = $value['channel']['channel_name'] ?? "";
                    $value['full_name'] = $value['channel']['full_name'] ?? "";
                    $value['email'] = $value['channel']['email'] ?? "";
                    $value['country_code'] = $value['channel']['country_code'] ?? "";
                    $value['mobile_number'] = $value['channel']['mobile_number'] ?? "";
                    $value['country_name'] = $value['channel']['country_name'] ?? "";
                    $value['profile_img'] = isset($value['channel']) ? $this->common->getImage($this->folder_user, $value['channel']['image']) : asset('/assets/imgs/default.png');
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value = $this->common->get_all_count_for_feed($value, $user_id);

                    unset($value['channel']);
                    $feed[] = $value;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $feed, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_channel_feed(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'channel_id' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $channel_id = $request['channel_id'];

            $data = Feed::where('channel_id', $channel_id)->where('status', 1)->orderBy('id', 'desc')->with('channel');

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->latest()->get()->toArray();

            if (count($data) > 0) {

                $feed = [];
                foreach ($data as $value) {
                    $value['feed_content'] = [];

                    $feed_content = Feed_Content::where('feed_id', $value['id'])->get();
                    foreach ($feed_content as $content) {

                        $content['image'] = $this->common->getImage($this->folder_feed, $content['image'], $content['image_storage_type']);
                        if ($content['content_type'] == 2) {
                            $content['video'] = $this->common->getVideo($this->folder_feed, $content['video'], $content['video_storage_type']);
                        }
                        $value['feed_content'][] = $content;
                    }
                    $value['hastegs'] = $this->common->getHashTag($value['hashtag_id']);

                    // User
                    $value['user_id'] = $value['channel']['id'] ?? 0;
                    $value['channel_name'] = $value['channel']['channel_name'] ?? "";
                    $value['full_name'] = $value['channel']['full_name'] ?? "";
                    $value['email'] = $value['channel']['email'] ?? "";
                    $value['country_code'] = $value['channel']['country_code'] ?? "";
                    $value['mobile_number'] = $value['channel']['mobile_number'] ?? "";
                    $value['country_name'] = $value['channel']['country_name'] ?? "";
                    $value['profile_img'] = isset($value['channel']) ? $this->common->getImage($this->folder_user, $value['channel']['image']) : asset('/assets/imgs/default.png');
                    $value['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $value = $this->common->get_all_count_for_feed($value, $user_id);

                    unset($value['channel']);
                    $feed[] = $value;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $feed, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_feed_detail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'feed_id' => 'required',
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $feed_id = $request['feed_id'];

            $data = Feed::where('id', $feed_id)->with('channel')->first();
            if ($data) {

                $data = $data->toArray();
                $data['feed_content'] = [];

                $feed_content = Feed_Content::where('feed_id', $data['id'])->get();
                foreach ($feed_content as $content) {

                    $content['image'] = $this->common->getImage($this->folder_feed, $content['image'], $content['image_storage_type']);
                    if ($content['content_type'] == 2) {
                        $content['video'] = $this->common->getVideo($this->folder_feed, $content['video'], $content['video_storage_type']);
                    }
                    $data['feed_content'][] = $content;
                }
                $data['hastegs'] = $this->common->getHashTag($data['hashtag_id']);

                // User
                $data['user_id'] = $data['channel']['id'] ?? 0;
                $data['channel_name'] = $data['channel']['channel_name'] ?? "";
                $data['full_name'] = $data['channel']['full_name'] ?? "";
                $data['email'] = $data['channel']['email'] ?? "";
                $data['country_code'] = $data['channel']['country_code'] ?? "";
                $data['mobile_number'] = $data['channel']['mobile_number'] ?? "";
                $data['country_name'] = $data['channel']['country_name'] ?? "";
                $data['profile_img'] = isset($data['channel']) ? $this->common->getImage($this->folder_user, $data['channel']['image']) : asset('/assets/imgs/default.png');
                $data['is_buy'] = $this->common->is_any_package_buy($user_id);
                $data = $this->common->get_all_count_for_feed($data, $user_id);

                unset($data['channel']);

                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_feed(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'feed_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $feed = Feed::where('id', $request['feed_id'])->first();
            if ($feed) {

                $feed_content = Feed_Content::where('feed_id', $feed['id'])->get();
                foreach ($feed_content as $data) {

                    $this->common->deleteImageToFolder($this->folder_feed, $data['image'], $data['image_storage_type']);
                    $this->common->deleteImageToFolder($this->folder_feed, $data['video'], $data['video_storage_type']);
                    $data->delete();
                }
                $this->common->deleted_feed_all_data($feed['id']);

                $feed->delete();
            }
            return $this->common->API_Response(200, __('api_msg.content_deleted'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_feed_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'feed_id' => 'required|numeric',
                'comment' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'] ?? 0;
            $user_id = $request['user_id'];
            $feed_id = $request['feed_id'];
            $comment = $request['comment'];

            $insert = new Feed_Comment();
            $insert['comment_id'] = $comment_id;
            $insert['user_id'] = $user_id;
            $insert['feed_id'] = $feed_id;
            $insert['comment'] = $comment;
            if ($insert->save()) {

                $user = User::find($user_id);
                $content = Feed::where('id', $feed_id)->with('channel')->first();
                if ($user && $content && $content['channel'] != null && $user_id != $content['channel']['id'] && $content['channel']['push_notification_status'] == 1) {

                    $title = $user['channel_name'] . ' Commented on your Feed.';
                    $this->common->save_notification(3, $title, $user_id, $content['channel']['id'], $feed_id, 0);
                }
                return $this->common->API_Response(200, __('api_msg.comment_added'));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit_feed_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment_id' => 'required|numeric',
                'comment' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'];
            $comment = $request['comment'];

            $update = Feed_Comment::where('id', $comment_id)->first();
            if ($update) {

                $update['comment'] = $comment;
                $update->save();
                return $this->common->API_Response(200, __('api_msg.comment_edited'));
            }
            return $this->common->API_Response(200, __('api_msg.data_not_found'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_feed_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'feed_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $feed_id = $request['feed_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Feed_Comment::where('feed_id', $feed_id)->where('comment_id', 0)->where('status', 1)->with('user')->orderBy('id', 'desc');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image']) : asset('/assets/imgs/default.png');
                    unset($data[$i]['user']);

                    $data[$i]['is_reply'] = 0;
                    $data[$i]['total_reply'] = 0;
                    $reply = Feed_Comment::where('comment_id', $data[$i]['id'])->count();
                    if ($reply != 0) {
                        $data[$i]['is_reply'] = 1;
                        $data[$i]['total_reply'] = $reply;
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_feed_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            Feed_Comment::where('id', $request['comment_id'])->delete();
            return $this->common->API_Response(200, __('api_msg.comment_deleted'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_feed_report(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required',
                'report_user_id' => 'required',
                'feed_id' => 'required',
                'message' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $report_user_id = $request['report_user_id'];
            $feed_id = $request['feed_id'];
            $message = $request['message'];

            $report = Feed_Report::where('user_id', $user_id)->where('report_user_id', $report_user_id)->where('feed_id', $feed_id)->where('status', 1)->first();
            if (!$report) {

                $insert['user_id'] = $user_id;
                $insert['report_user_id'] = $report_user_id;
                $insert['feed_id'] = $feed_id;
                $insert['message'] = $message;
                $insert['status'] = 1;
                Feed_Report::insertGetId($insert);
            }
            return $this->common->API_Response(200, __('api_msg.report_added'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_feed_reply_comment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $comment_id = $request['comment_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Feed_Comment::where('comment_id', $comment_id)->where('status', 1)->orderBy('id', 'desc')->with('user');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image'], $data[$i]['user']['image_storage_type']) : asset('/assets/imgs/default.png');
                    unset($data[$i]['user']);

                    $data[$i]['is_reply'] = 0;
                    $data[$i]['total_reply'] = 0;
                    $reply = Feed_Comment::where('comment_id', $data[$i]['id'])->count();
                    if ($reply != 0) {
                        $data[$i]['is_reply'] = 1;
                        $data[$i]['total_reply'] = $reply;
                    }
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function like_unlike_feed(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'feed_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $feed_id = $request['feed_id'];

            $data = Feed_Like::where('user_id', $user_id)->where('feed_id', $feed_id)->first();
            if ($data) {

                Feed_Like::where('id', $data['id'])->delete();
                return $this->common->API_Response(200, __('api_msg.unlike_successfully'));
            } else {

                $data['user_id'] = $user_id;
                $data['feed_id'] = $feed_id;
                $data['status'] = 1;
                Feed_Like::insertGetId($data);

                $user = User::find($user_id);
                $content = Feed::where('id', $feed_id)->with('channel')->first();
                if ($user && $content && $content['channel'] != null && $user_id != $content['channel']['id'] && $content['channel']['push_notification_status'] == 1) {

                    $title = $user['channel_name'] . ' Liked your Feed.';
                    $this->common->save_notification(2, $title, $user_id, $content['channel']['id'], $feed_id, 0);
                }
                return $this->common->API_Response(200, __('api_msg.like_successfully'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_rent_section(Request $request)
    {
        try {

            $user_id = $request['user_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Rent_Section::where('status', 1)->orderBy('sort_order', 'asc')->latest();

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

                    $query = Content::where('content_type', 1)->where('is_rent', 1)->where('category_id', $data[$i]['category_id'])->where('status', 1)->orderBy('id', 'desc')->latest()->take($data[$i]['no_of_content'])->get();
                    for ($j = 0; $j < count($query); $j++) {

                        $query[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $query[$j]['portrait_img'], $query[$j]['portrait_img_storage_type']);
                        $query[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $query[$j]['landscape_img'], $query[$j]['landscape_img_storage_type']);
                        if ($query[$j]['content_upload_type'] == 'server_video') {
                            $query[$j]['content'] = $this->common->getVideo($this->folder_content, $query[$j]['content'], $query[$j]['content_storage_type']);
                        }

                        $query[$j]['user_id'] = $this->common->getUserId($query[$j]['channel_id']);
                        $query[$j]['channel_name'] = $this->common->getChannelName($query[$j]['channel_id']);
                        $query[$j]['channel_image'] = $this->common->getChannelImage($query[$j]['channel_id']);
                        $query[$j]['category_name'] = $this->common->getCategoryName($query[$j]['category_id']);
                        $query[$j]['language_name'] = $this->common->getLanguageName($query[$j]['language_id']);
                        $query[$j]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $query[$j]['content_type'], $query[$j]['id'], 0);
                        $query[$j]['is_rent_buy'] = $this->common->getRentBuy($user_id, $query[$j]['id']);
                        $query[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    }
                    $data[$i]['data'] = $query;
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_rent_section_detail(Request $request)
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

            $section = Rent_Section::where('id', $section_id)->first();
            if ($section) {

                $page_size = 0;
                $current_page = 0;
                $more_page = false;

                $data = Content::where('content_type', 1)->where('is_rent', 1)->where('category_id', $section['category_id'])->where('status', 1)->orderBy('id', 'desc')->latest();

                $total_rows = $data->count();
                $total_page = $this->page_limit;
                $page_size = ceil($total_rows / $total_page);
                $current_page = $request['page_no'] ?? 1;
                $offset = $current_page * $total_page - $total_page;

                $more_page = $this->common->more_page($current_page, $page_size);
                $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
                $data = $data->take($total_page)->offset($offset)->latest()->get();

                if (count($data) > 0) {

                    for ($j = 0; $j < count($data); $j++) {

                        $data[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $data[$j]['portrait_img'], $data[$j]['portrait_img_storage_type']);
                        $data[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $data[$j]['landscape_img'], $data[$j]['landscape_img_storage_type']);
                        if ($data[$j]['content_upload_type'] == 'server_video') {
                            $data[$j]['content'] = $this->common->getVideo($this->folder_content, $data[$j]['content'], $data[$j]['content_storage_type']);
                        }

                        $data[$j]['user_id'] = $this->common->getUserId($data[$j]['channel_id']);
                        $data[$j]['channel_name'] = $this->common->getChannelName($data[$j]['channel_id']);
                        $data[$j]['channel_image'] = $this->common->getChannelImage($data[$j]['channel_id']);
                        $data[$j]['category_name'] = $this->common->getCategoryName($data[$j]['category_id']);
                        $data[$j]['language_name'] = $this->common->getLanguageName($data[$j]['language_id']);
                        $data[$j]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $data[$j]['content_type'], $data[$j]['id'], 0);
                        $data[$j]['is_rent_buy'] = $this->common->getRentBuy($user_id, $data[$j]['id']);
                        $data[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
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
    public function get_user_rent_content(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $content_id = Rent_Transaction::where('user_id', $user_id)->where('status', 1)->orderBy('id', 'desc')->pluck('content_id')->toArray();
            if (count($content_id) > 0) {

                $ids_ordered = implode(',', $content_id);
                $data = Content::whereIn('id', $content_id)->orderByRaw("FIELD(id, $ids_ordered)");

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
                        $data[$i]['is_rent_buy'] = $this->common->getRentBuy($user_id, $data[$i]['id']);
                        $data[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
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
    public function get_rent_content_by_channel(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'channel_id' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $channel_id = $request['channel_id'];
            $content_type = 1;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Content::where('content_type', $content_type)->where('channel_id', $channel_id)->where('status', 1)->where('is_rent', 0)->orderby('id', 'desc')->latest();

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
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function delete_content(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_type' => 'required|numeric',
                'content_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'] ?? 0;
            $content_id = $request['content_id'];
            $content_type = $request['content_type'];
            $episode_id = $request['episode_id'] ?? 0;

            $content = Content::where('content_type', $content_type)->where('id', $content_id)->first();
            if ($content) {

                if ($content_type == 1 || $content_type == 2 || $content_type == 3 || $content_type == 5 || $content_type == 6) {

                    // Content Delete
                    $this->common->deleteImageToFolder($this->folder_content, $content['portrait_img'], $content['portrait_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder_content, $content['landscape_img'], $content['landscape_img_storage_type']);
                    $this->common->deleteImageToFolder($this->folder_content, $content['content'], $content['content_storage_type']);
                    $content->delete();

                    // Content Releted Data Delete
                    Comment::where('content_id', $content_id)->delete();
                    Content_Report::where('content_id', $content_id)->delete();
                    History::where('content_id', $content_id)->delete();
                    Notification::where('content_id', $content_id)->delete();
                    Content_Like::where('content_id', $content_id)->delete();
                    Content_View::where('content_id', $content_id)->delete();
                    Watch_later::where('content_id', $content_id)->delete();
                    if ($content_type == 5) {
                        Playlist_Content::where('playlist_id', $content_id)->delete();
                    }
                } else if ($content_type == 4) {

                    if ($episode_id != 0) {

                        $episode = Episode::where('podcasts_id', $content_id)->where('id', $episode_id)->first();
                        if ($episode) {
                            $this->common->deleteImageToFolder($this->folder_content, $episode['portrait_img'], $episode['portrait_img_storage_type']);
                            $this->common->deleteImageToFolder($this->folder_content, $episode['landscape_img'], $episode['landscape_img_storage_type']);
                            $this->common->deleteImageToFolder($this->folder_content, $episode['episode_audio'], $episode['episode_storage_type']);
                            $episode->delete();
                        }

                        // Content Releted Data Delete
                        Comment::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                        Content_Report::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                        History::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                        Content_Like::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                        Content_View::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                        Watch_later::where('content_id', $content_id)->where('episode_id', $episode_id)->delete();
                    } else {

                        // Content Delete
                        $this->common->deleteImageToFolder($this->folder_content, $content['portrait_img'], $content['portrait_img_storage_type']);
                        $this->common->deleteImageToFolder($this->folder_content, $content['landscape_img'], $content['landscape_img_storage_type']);
                        $this->common->deleteImageToFolder($this->folder_content, $content['content'], $content['content_storage_type']);
                        $content->delete();

                        // Content Releted Data Delete
                        Comment::where('content_id', $content_id)->delete();
                        Content_Report::where('content_id', $content_id)->delete();
                        History::where('content_id', $content_id)->delete();
                        Notification::where('content_id', $content_id)->delete();
                        Content_Like::where('content_id', $content_id)->delete();
                        Content_View::where('content_id', $content_id)->delete();
                        Watch_later::where('content_id', $content_id)->delete();

                        // Episodes
                        $episode = Episode::where('podcasts_id', $content_id)->get();
                        for ($i = 0; $i < count($episode); $i++) {

                            $this->common->deleteImageToFolder($this->folder_content, $episode[$i]['portrait_img'], $episode[$i]['portrait_img_storage_type']);
                            $this->common->deleteImageToFolder($this->folder_content, $episode[$i]['landscape_img'], $episode[$i]['landscape_img_storage_type']);
                            $this->common->deleteImageToFolder($this->folder_content, $episode[$i]['episode_audio'], $episode[$i]['episode_storage_type']);
                            $episode[$i]->delete();
                        }
                    }
                }
                return $this->common->API_Response(200, __('api_msg.content_deleted'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function search_content(Request $request) // Type = 1- Video, 2- Music
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $name = $request['name'];
            $user_id = $request['user_id'] ?? 0;
            $type = $request['type'] ?? 0;

            $data['status'] = 200;
            $data['message'] = __('api_msg.data_retrieved');
            $data['result'] = array();
            $data['video'] = array();
            $data['channel'] = array();
            $data['music'] = array();
            $data['podcast'] = array();
            $data['radio'] = array();
            $data['reels'] = array();

            if ($type == 1) {

                $video = Content::where('content_type', 1)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($video); $j++) {

                    $video[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $video[$j]['portrait_img'], $video[$j]['portrait_img_storage_type']);
                    $video[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $video[$j]['landscape_img'], $video[$j]['landscape_img_storage_type']);
                    if ($video[$j]['content_upload_type'] == 'server_video') {
                        $video[$j]['content'] = $this->common->getVideo($this->folder_content, $video[$j]['content'], $video[$j]['content_storage_type']);
                    }
                    $video[$j]['user_id'] = $this->common->getUserId($video[$j]['channel_id']);
                    $video[$j]['channel_name'] = $this->common->getChannelName($video[$j]['channel_id']);
                    $video[$j]['channel_image'] = $this->common->getChannelImage($video[$j]['channel_id']);
                    $video[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['video'][] = $video[$j];
                }

                $channel = User::where('channel_name', 'LIKE', "%{$name}%")->where('status', 1)->orderBy('id', 'desc')->latest()->take(10)->get();
                for ($i = 0; $i < count($channel); $i++) {

                    $channel[$i]['image'] = $this->common->getImage($this->folder_user, $channel[$i]['image'], $channel[$i]['image_storage_type']);
                    $channel[$i]['cover_img'] = $this->common->getImage($this->folder_user, $channel[$i]['cover_img'], $channel[$i]['cover_img_storage_type']);
                    $channel[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['channel'][] = $channel[$i];
                }
            } else if ($type == 2) {

                $music = Content::where('content_type', 2)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($music); $j++) {

                    $music[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $music[$j]['portrait_img'], $music[$j]['portrait_img_storage_type']);
                    $music[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $music[$j]['landscape_img'], $music[$j]['landscape_img_storage_type']);
                    if ($music[$j]['content_upload_type'] == 'server_video') {
                        $music[$j]['content'] = $this->common->getVideo($this->folder_content, $music[$j]['content'], $music[$j]['content_storage_type']);
                    }
                    $music[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['music'][] = $music[$j];
                }

                $podcast = Content::where('content_type', 4)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($podcast); $j++) {

                    $podcast[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $podcast[$j]['portrait_img'], $podcast[$j]['portrait_img_storage_type']);
                    $podcast[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $podcast[$j]['landscape_img'], $podcast[$j]['landscape_img_storage_type']);

                    $podcast[$j]['user_id'] = $this->common->getUserId($podcast[$j]['channel_id']);
                    $podcast[$j]['channel_name'] = $this->common->getChannelName($podcast[$j]['channel_id']);
                    $podcast[$j]['channel_image'] = $this->common->getChannelImage($podcast[$j]['channel_id']);
                    $podcast[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['podcast'][] = $podcast[$j];
                }

                $radio = Content::where('content_type', 6)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($radio); $j++) {

                    $radio[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $radio[$j]['portrait_img'], $radio[$j]['portrait_img_storage_type']);
                    $radio[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $radio[$j]['landscape_img'], $radio[$j]['landscape_img_storage_type']);
                    if ($radio[$j]['content_upload_type'] == 'server_video') {
                        $radio[$j]['content'] = $this->common->getVideo($this->folder_content, $radio[$j]['content'], $radio[$j]['content_storage_type']);
                    }
                    $radio[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['radio'][] = $radio[$j];
                }
            } else {

                $video = Content::where('content_type', 1)->where('title', 'LIKE', "%{$name}%")->where('is_rent', 0)->where('status', 1)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($video); $j++) {

                    $video[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $video[$j]['portrait_img'], $video[$j]['portrait_img_storage_type']);
                    $video[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $video[$j]['landscape_img'], $video[$j]['landscape_img_storage_type']);
                    if ($video[$j]['content_upload_type'] == 'server_video') {
                        $video[$j]['content'] = $this->common->getVideo($this->folder_content, $video[$j]['content'], $video[$j]['content_storage_type']);
                    }
                    $video[$j]['user_id'] = $this->common->getUserId($video[$j]['channel_id']);
                    $video[$j]['channel_name'] = $this->common->getChannelName($video[$j]['channel_id']);
                    $video[$j]['channel_image'] = $this->common->getChannelImage($video[$j]['channel_id']);
                    $video[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['video'][] = $video[$j];
                }

                $channel = User::where('channel_name', 'LIKE', "%{$name}%")->where('status', 1)->orderBy('id', 'desc')->latest()->take(10)->get();
                for ($i = 0; $i < count($channel); $i++) {

                    $channel[$i]['image'] = $this->common->getImage($this->folder_user, $channel[$i]['image'], $channel[$i]['image_storage_type']);
                    $channel[$i]['cover_img'] = $this->common->getImage($this->folder_user, $channel[$i]['cover_img'], $channel[$i]['cover_img_storage_type']);
                    $channel[$i]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data['channel'][] = $channel[$i];
                }

                $music = Content::where('content_type', 2)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($music); $j++) {

                    $music[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $music[$j]['portrait_img'], $music[$j]['portrait_img_storage_type']);
                    $music[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $music[$j]['landscape_img'], $music[$j]['landscape_img_storage_type']);
                    if ($music[$j]['content_upload_type'] == 'server_video') {
                        $music[$j]['content'] = $this->common->getVideo($this->folder_content, $music[$j]['content'], $music[$j]['content_storage_type']);
                    }
                    $music[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);
                    $data['music'][] = $music[$j];
                }

                $podcast = Content::where('content_type', 4)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($podcast); $j++) {

                    $podcast[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $podcast[$j]['portrait_img'], $podcast[$j]['portrait_img_storage_type']);
                    $podcast[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $podcast[$j]['landscape_img'], $podcast[$j]['landscape_img_storage_type']);

                    $podcast[$j]['user_id'] = $this->common->getUserId($podcast[$j]['channel_id']);
                    $podcast[$j]['channel_name'] = $this->common->getChannelName($podcast[$j]['channel_id']);
                    $podcast[$j]['channel_image'] = $this->common->getChannelImage($podcast[$j]['channel_id']);
                    $podcast[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['podcast'][] = $podcast[$j];
                }

                $radio = Content::where('content_type', 6)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($radio); $j++) {

                    $radio[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $radio[$j]['portrait_img'], $radio[$j]['portrait_img_storage_type']);
                    $radio[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $radio[$j]['landscape_img'], $radio[$j]['landscape_img_storage_type']);
                    $radio[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['radio'][] = $radio[$j];
                }

                $reels = Content::where('content_type', 3)->where('title', 'LIKE', "%{$name}%")->where('status', 1)->where('is_rent', 0)->orderBy('total_view', 'desc')->latest()->take(10)->get();
                for ($j = 0; $j < count($reels); $j++) {

                    $reels[$j]['portrait_img'] = $this->common->getImage($this->folder_content, $reels[$j]['portrait_img'], $reels[$j]['portrait_img_storage_type']);
                    $reels[$j]['landscape_img'] = $this->common->getImage($this->folder_content, $reels[$j]['landscape_img'], $reels[$j]['landscape_img_storage_type']);
                    $reels[$j]['content'] = $this->common->getVideo($this->folder_content, $reels[$j]['content'], $reels[$j]['content_storage_type']);
                    $reels[$j]['user_id'] = $this->common->getUserId($reels[$j]['channel_id']);
                    $reels[$j]['channel_name'] = $this->common->getChannelName($reels[$j]['channel_id']);
                    $reels[$j]['channel_image'] = $this->common->getChannelImage($reels[$j]['channel_id']);
                    $reels[$j]['total_comment'] = $this->common->getTotalComment($reels[$j]['id']);
                    $reels[$j]['is_user_like_dislike'] = $this->common->getUserLikeDislike($user_id, $reels[$j]['content_type'], $reels[$j]['id'], 0);
                    $reels[$j]['is_subscribe'] = $this->common->is_subscribe($user_id, $reels[$j]['user_id']);
                    $reels[$j]['is_buy'] = $this->common->is_any_package_buy($user_id);

                    $data['reels'][] = $reels[$j];
                }
            }
            return $data;
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_rent_transaction(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'content_id' => 'required|numeric',
                'price' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $content_id = $request['content_id'];
            $price = $request['price'];
            $transaction_id = $request['transaction_id'] ?? "";
            $description = $request['description'] ?? "";

            $Cdata = Content::where('id', $content_id)->where('status', 1)->where('is_rent', 1)->first();
            if (!$Cdata) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $insert = new Rent_Transaction();
            $insert['user_id'] = $user_id;
            $insert['content_id'] = $content_id;
            $insert['transaction_id'] = $transaction_id;
            $insert['price'] = $price;
            $insert['description'] = $description;
            $insert['status'] = 1;
            $setting = Setting_Data();
            $admin_commission = $setting['rent_commission'];
            $persentage = round(($admin_commission / 100) * $price);
            $user_wallet_amount = $price - $persentage;
            $insert['admin_commission'] = $persentage;
            $insert['user_wallet_amount'] = $user_wallet_amount;
            $insert['expiry_date'] = date('Y-m-d', strtotime($Cdata['rent_day'] . ' days'));

            if ($insert->save()) {

                // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                $user_email = User::where('id', $user_id)->first();
                if ($user_email) {
                    $this->common->Send_Mail(2, $user_email);
                }

                // User Wallet Add Amount
                User::where('channel_id', $Cdata['channel_id'])->increment('wallet_earning', $user_wallet_amount);

                return $this->common->API_Response(200, __('api_msg.transaction_completed'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_transaction(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'package_id' => 'required|numeric',
                'price' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $package_id = $request['package_id'];
            $price = $request['price'];
            $transaction_id = $request['transaction_id'] ?? "";
            $description = $request['description'] ?? "";

            // Expriy
            Transaction::where('user_id', $user_id)->update(['status' => 0]);

            $Pdata = Package::where('id', $package_id)->where('status', '1')->first();
            if (!empty($Pdata)) {
                $Edate = date("Y-m-d", strtotime("$Pdata->time $Pdata->type"));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $insert = new Transaction();
            $insert['user_id'] = $user_id;
            $insert['package_id'] = $package_id;
            $insert['transaction_id'] = $transaction_id;
            $insert['price'] = $price;
            $insert['description'] = $description;
            $insert['expiry_date'] = $Edate;
            $insert['status'] = 1;
            if ($insert->save()) {

                // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                $user_email = User::where('id', $user_id)->first();
                if ($user_email) {
                    $this->common->Send_Mail(2, $user_email);
                }

                return $this->common->API_Response(200, __('api_msg.transaction_completed'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_transaction_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Transaction::where('user_id', $user_id)->with('user')->latest()->orderBy('id', 'desc');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['mobile_number'] = $data[$i]['user']['mobile_number'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image'], $data[$i]['user']['image_storage_type']) : asset('assets/imgs/default.png');
                    unset($data[$i]['user']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_coin_transaction(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'package_id' => 'required|numeric',
                'price' => 'required|numeric',
                'coin' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $package_id = $request['package_id'];
            $price = $request['price'];
            $coin = $request['coin'];
            $transaction_id = $request['transaction_id'] ?? "";
            $description = $request['description'] ?? "";

            $insert = new Coin_Transaction();
            $insert['user_id'] = $user_id;
            $insert['package_id'] = $package_id;
            $insert['transaction_id'] = $transaction_id;
            $insert['price'] = $price;
            $insert['coin'] = $coin;
            $insert['description'] = $description;
            $insert['status'] = 1;
            if ($insert->save()) {

                // Send Mail (Type = 1- Register, 2- Transaction, 3- Report, 4- User Penal Active)
                $user_email = User::where('id', $user_id)->first();
                if ($user_email) {
                    $this->common->Send_Mail(2, $user_email);
                }
                User::where('id', $user_id)->increment('wallet_balance', $coin);

                return $this->common->API_Response(200, __('api_msg.transaction_completed'), []);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_coin_transaction_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Coin_Transaction::where('user_id', $user_id)->with('user')->latest()->orderBy('id', 'desc');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['mobile_number'] = $data[$i]['user']['mobile_number'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image'], $data[$i]['user']['image_storage_type']) : asset('assets/imgs/default.png');
                    unset($data[$i]['user']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_ads(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            // Ads Inactive
            $this->common->inactive_ads();

            $data = Ads::where('is_hide', 0)->where('status', 1)->where('type', $request['type'])->inRandomOrder()->first();
            if ($data) {

                $data['image'] = $this->common->getImage($this->folder_ads, $data['image'], $data['image_storage_type']);
                if ($data['type'] == 3) {
                    $data['video'] = $this->common->getVideo($this->folder_ads, $data['video'], $data['video_storage_type']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function add_ads_view_click_count(Request $request) // Type = 1- CPV, 2- CPC
    {
        try {
            $rules = [
                'type' => 'required|numeric',
                'ads_type' => 'required|numeric',
                'ads_id' => 'required|numeric',
                'device_type' => 'required|numeric',
                'device_token' => 'required',
            ];
            if ($request['ads_type'] == 3) {
                $rules['content_id'] = 'required|numeric';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->first()]);
            }

            $type = $request['type'];
            $ads_type = $request['ads_type'];
            $ads_id = $request['ads_id'];
            $device_type = $request['device_type'];
            $device_token = $request['device_token'];
            $content_id = $request['content_id'] ?? 0;

            // Ads Inactive
            $this->common->inactive_ads($ads_id);

            $check_ads = Ads::where('id', $ads_id)->where('status', 1)->where('is_hide', 0)->latest()->first();
            if ($check_ads) {

                $settingData = Setting_Data();
                if ($type == 1) {

                    if ($ads_type == 1 || $ads_type == 2) {

                        if ($ads_type == 1) {
                            $ads_coin = $settingData['banner_ads_cpv'];
                        } else {
                            $ads_coin = $settingData['interstital_ads_cpv'];
                        }

                        $insert = new Ads_View_Click_Count();
                        $insert['ads_type'] = $ads_type;
                        $insert['ads_id'] = $ads_id;
                        $insert['device_type'] = $device_type;
                        $insert['device_token'] = $device_token;
                        $insert['content_id'] = 0;
                        $insert['type'] = 1;
                        $insert['total_coin'] = $ads_coin;
                        $insert['admin_commission'] = $ads_coin;
                        $insert['user_wallet_earning'] = 0;
                        $insert['status'] = 1;
                        if ($insert->save()) {
                            User::where('id', $check_ads['user_id'])->decrement('wallet_balance', $ads_coin);
                        }
                    } else if ($ads_type == 3) {

                        $check_content = Content::where('id', $content_id)->latest()->first();
                        if ($check_content) {

                            $ads_coin = $settingData['reward_ads_cpv'];

                            $insert = new Ads_View_Click_Count();
                            $insert['ads_type'] = $ads_type;
                            $insert['ads_id'] = $ads_id;
                            $insert['device_type'] = $device_type;
                            $insert['device_token'] = $device_token;
                            $insert['content_id'] = $content_id;
                            $insert['type'] = 1;
                            $insert['total_coin'] = $ads_coin;

                            $commission = $settingData['ads_commission'];
                            $admin_commission = round(($commission / 100) * $ads_coin);

                            $insert['admin_commission'] = $admin_commission;
                            $insert['user_wallet_earning'] = $ads_coin - $admin_commission;
                            $insert['status'] = 1;

                            if ($insert->save()) {
                                User::where('id', $check_ads['user_id'])->decrement('wallet_balance', $ads_coin);
                                User::where('channel_id', $check_content['channel_id'])->increment('wallet_earning', $insert['user_wallet_earning']);
                            }
                        }
                    }
                } else if ($type == 2) {

                    if ($request['ads_type'] == 1 || $request['ads_type'] == 2) {

                        if ($request['ads_type'] == 1) {
                            $ads_cpc = $settingData['banner_ads_cpc'];
                        } else {
                            $ads_cpc = $settingData['interstital_ads_cpc'];
                        }

                        $user_wallet_coin = $this->common->get_user_budget($check_ads['user_id']);
                        $total_view_click_coin = $this->common->get_total_view_click_coin($check_ads['id']);
                        $remening_budget = $check_ads['budget'] - $total_view_click_coin;

                        if ($ads_cpc <= $user_wallet_coin && $ads_cpc <= $remening_budget) {

                            $insert = new Ads_View_Click_Count();
                            $insert['ads_type'] = $ads_type;
                            $insert['ads_id'] = $ads_id;
                            $insert['device_type'] = $device_type;
                            $insert['device_token'] = $device_token;
                            $insert['content_id'] = 0;
                            $insert['type'] = 2;
                            $insert['total_coin'] = $ads_cpc;
                            $insert['admin_commission'] = $ads_cpc;
                            $insert['user_wallet_earning'] = 0;
                            $insert['status'] = 1;

                            if ($insert->save()) {
                                User::where('id', $check_ads['user_id'])->decrement('wallet_balance', $ads_cpc);
                            }
                        }
                    } else if ($request['type'] == 3) {

                        $ads_cpc = $settingData['reward_ads_cpc'];
                        $user_wallet_coin = $this->common->get_user_budget($check_ads['user_id']);
                        $total_view_click_coin = $this->common->get_total_view_click_coin($check_ads['id']);
                        $remening_budget = $check_ads['budget'] - $total_view_click_coin;

                        if ($ads_cpc <= $user_wallet_coin && $ads_cpc <= $remening_budget) {

                            $check_content = Content::where('id', $content_id)->latest()->first();
                            if ($check_content) {

                                $insert = new Ads_View_Click_Count();
                                $insert['ads_type'] = $ads_type;
                                $insert['ads_id'] = $ads_id;
                                $insert['device_type'] = $device_type;
                                $insert['device_token'] = $device_token;
                                $insert['content_id'] = $content_id;
                                $insert['type'] = 2;
                                $insert['total_coin'] = $ads_cpc;

                                $commission = $settingData['ads_commission'];
                                $admin_commission = round(($commission / 100) * $ads_cpc);

                                $insert['admin_commission'] = $admin_commission;
                                $insert['user_wallet_earning'] = $ads_cpc - $admin_commission;
                                $insert['status'] = 1;

                                if ($insert->save()) {
                                    User::where('id', $check_ads['user_id'])->decrement('wallet_balance', $ads_cpc);
                                    User::where('channel_id', $check_content['channel_id'])->increment('wallet_earning', $insert['user_wallet_earning']);
                                }
                            }
                        }
                    }
                }
            }
            return $this->common->API_Response(200, __('api_msg.view_add_successfully'), []);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_ads_coin_history(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_ads = Ads::where('user_id', $user_id)->orderBy('id', 'desc')->latest()->get();
            $user_ads_ids = [];
            for ($i = 0; $i < count($user_ads); $i++) {
                $user_ads_ids[] = $user_ads[$i]['id'];
            }
            if (count($user_ads_ids) > 0) {
                $data = Ads_View_Click_Count::selectRaw('ads_id, sum(total_coin) as total_coin')->whereIn('ads_id', $user_ads_ids)->with('ads')->groupBy('ads_id');
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request['page_no'] ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);
            $data = $data->take($total_page)->offset($offset)->orderBy('total_coin', 'desc')->get();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {

                    $data[$i]['title'] = $data[$i]['ads']['title'] ?? "";
                    unset($data[$i]['ads']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_gift(Request $request)
    {
        try {

            $user_id = $request['user_id'] ?? 0;
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Gift::where('status', 1)->orderBy('id', 'desc')->latest();

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
                    $data[$i]['image'] = $this->common->getImage($this->folder_gift, $data[$i]['image'], $data[$i]['storage_type']);
                    $data[$i]['is_buy'] = $this->common->gift_buy($user_id, $data[$i]['id']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_user_gift(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $gift_ids = Gift_Transaction::where('user_id', $user_id)->latest()->pluck('gift_id')->toArray();
            $data = Gift::whereIn('id', $gift_ids)->orderBy('id', 'DESC')->latest();

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
                    $data[$i]['image'] = $this->common->getImage($this->folder_gift, $data[$i]['image'], $data[$i]['storage_type']);
                    $data[$i]['is_buy'] = $this->common->gift_buy($user_id, $data[$i]['id']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function buy_gift(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'gift_id' => 'required|numeric',
                'coin' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_wallet = User::where('id', $request['user_id'])->first();
            if ($user_wallet && $user_wallet['wallet_balance'] >= $request['coin']) {

                $data = [
                    'user_id' => $request['user_id'],
                    'gift_id' => $request['gift_id'],
                    'coin'    => $request['coin'],
                    'status'  => 1,
                ];
                $result = Gift_Transaction::insertGetId($data);
                if ($result) {

                    User::where('id', $request['user_id'])->decrement('wallet_balance', $request['coin']);
                    return $this->common->API_Response(200, __('api_msg.gift_purchase_success'));
                }
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
            return $this->common->API_Response(400, __('api_msg.wallet_insufficient'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_withdrawal_request_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Withdrawal_Request::where('user_id', $user_id)->with('user')->orderBy('id', 'desc');

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

                    $data[$i]['channel_name'] = $data[$i]['user']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['user']['full_name'] ?? "";
                    $data[$i]['email'] = $data[$i]['user']['email'] ?? "";
                    $data[$i]['mobile_number'] = $data[$i]['user']['mobile_number'] ?? "";
                    $data[$i]['image'] = isset($data[$i]['user']) ? $this->common->getImage($this->folder_user, $data[$i]['user']['image']) : asset('assets/imgs/default.png');
                    unset($data[$i]['user']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function feed_content_upload(Request $request) // 1- Image, 2- Video	
    {
        try {
            $validation = Validator::make($request->all(), [
                'content_type' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $content_type = $request['content_type'];
            $storage_type = Storage_Type();

            if ($content_type == 1) {

                $validation = Validator::make($request->all(), [
                    'image' => 'required|file',
                ]);
                if ($validation->fails()) {
                    return $this->common->API_Response(400, $validation->errors()->first());
                }

                $content_type = $request['content_type'];
                $image = $this->common->saveImage($request['image'], $this->folder_feed, 'feed_img_', $storage_type);

                $data['content_type'] = $content_type;
                $data['image'] = $image;
                $data['image_url'] = $this->common->getImage($this->folder_feed, $image, $storage_type);
                $data['video'] = "";
                $data['video_url'] = "";
                return $this->common->API_Response(200, __('api_msg.content_saved'), $data);
            } else if ($content_type == 2) {

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

                        $filename = 'feed_vid' . date('Y_m_d_') . uniqid() . '.mp4';
                        $finalPath = storage_path("app/public/feed/{$filename}");
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
                            'full_url' => Storage::disk('public')->exists('feed/' . $filename),
                        ]);
                    }
                    return $this->common->API_Response(206, __('api_msg.upload_progress', ['current' => $chunkIndex, 'total' => $totalChunks]), ['directory' => $uploadId]);
                }

                $validation1 = Validator::make($request->all(), [
                    'image' => 'required|file',
                    'video' => 'required|string',
                ]);
                if ($validation1->fails()) {
                    return $this->common->API_Response(400, $validation1->errors()->first());
                }

                $image = $this->common->saveImage($request['image'], $this->folder_feed, 'feed_img_', $storage_type);
                $video = $request['video'] ?? "";

                $data['content_type'] = $content_type;
                $data['image'] = $image;
                $data['image_url'] = $this->common->getImage($this->folder_feed, $image, $storage_type);
                $data['video'] = $video;
                $data['video_url'] = $this->common->getVideo($this->folder_feed, $video, $storage_type);
                return $this->common->API_Response(200, __('api_msg.content_saved'), $data);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function upload_feed(Request $request) // 1- Image, 2- Video	
    {
        try {
            $validation = Validator::make($request->all(), [
                'channel_id' => 'required',
                'description' => 'required',
                'is_like' => 'required|numeric',
                'is_comment' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $data['channel_id'] = $request['channel_id'];
            $hashtag_id = $this->common->checkHashTag($request['description']);
            $hashtagId = 0;
            if (count($hashtag_id) > 0) {
                $hashtagId = implode(',', $hashtag_id);
            }
            $data['hashtag_id'] = $hashtagId;
            $data['description'] = $request['description'];
            $data['is_like'] = $request['is_like'];
            $data['is_comment'] = $request['is_comment'];
            $data['total_like'] = 0;
            $data['status'] = 1;
            $feed_id = Feed::insertGetId($data);

            if (isset($feed_id)) {

                $post_content = json_decode($request['post_content'], true) ?? $request['post_content'];
                if (!empty($post_content)) {

                    $storage_type = Storage_Type();
                    foreach ($post_content as $item) {
                        Feed_Content::Create([
                            'feed_id' => $feed_id,
                            'content_type' => $item['content_type'],
                            'image_storage_type' => $storage_type,
                            'image' => $item['image'],
                            'video_storage_type' => $storage_type,
                            'video' => $item['video'] ?? "",
                            'status' => 1,
                        ]);
                    }
                }
                return $this->common->API_Response(200, __('api_msg.feed_uploaded'));
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function get_notification(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $user_notification_id = Read_Notification::where('user_id', $user_id)->where('status', 1)->get();
            $NotiIds = [];
            foreach ($user_notification_id as $key => $value) {
                $NotiIds[] = $value['notification_id'];
            }
            $data = Notification::where('from_user_id', $user_id)->where('status', 1)->orwhere('type', 1)->whereNotIn('id', $NotiIds)->with('user', 'content')->orderBy('id', 'desc')->latest();

            $total_rows = $data->count();
            $total_page = $this->page_limit;
            $page_size = ceil($total_rows / $total_page);
            $current_page = $request->page_no ?? 1;
            $offset = $current_page * $total_page - $total_page;

            $more_page = $this->common->more_page($current_page, $page_size);
            $pagination = $this->common->pagination_array($total_rows, $page_size, $current_page, $more_page);

            $data->take($total_page)->offset($offset);
            $data = $data->get();

            $this->common->imageNameToUrl($data, 'image', $this->folder_notification);

            foreach ($data as $key => $value) {

                $value['user_name'] = "";
                $value['user_image'] = asset('assets/imgs/default.png');;
                if ($value['user'] != null) {
                    $value['user_name'] = $value['user']['channel_name'];
                    $value['user_image'] = $this->common->getImage($this->folder_user, $value['user']['image'], $value['user']['image_storage_type']);
                }

                $value['content_name'] = "";
                $value['content_image'] = asset('assets/imgs/no_img.png');
                if ($value['content'] != null) {
                    $value['content_name'] = $value['content']['title'];
                    $value['content_image'] = $this->common->getImage($this->folder_content, $value['content']['portrait_img'], $value['content']['portrait_img_storage_type']);
                }
                unset($value['user'], $value['content']);
            }
            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $data, $pagination);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function read_notification(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'notification_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];
            $notification_id = $request['notification_id'];

            $get_data = Notification::where('id', $notification_id)->first();
            if ($get_data) {

                if ($get_data['type'] == 1) {

                    $check_read = Read_Notification::where('user_id', $user_id)->where('notification_id', $notification_id)->where('status', 1)->first();
                    if (!$check_read) {

                        $insert = new Read_Notification();
                        $insert['user_id'] = $user_id;
                        $insert['notification_id'] = $notification_id;
                        $insert['status'] = 1;
                        $insert->save();
                    }
                } else {
                    $get_data->delete();
                }
            }
            return $this->common->API_Response(200, __('api_msg.read_notification_successfully'), []);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function send_gift(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'gift_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'channel_id' => 'required',
                'content_id' => 'required|numeric',
                'price' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_wallet = User::where('id', $request['user_id'])->first();
            if ($user_wallet && $user_wallet['wallet_balance'] >= $request['price']) {

                $data = [
                    'gift_id' => $request['gift_id'],
                    'user_id' => $request['user_id'],
                    'channel_id' => $request['channel_id'],
                    'content_id' => $request['content_id'],
                    'price'    => $request['price'],
                    'status'  => 1,
                ];
                $result = Send_Gift::insertGetId($data);
                if ($result) {

                    User::where('id', $request['user_id'])->decrement('wallet_balance', $request['price']);
                    User::where('channel_id', $request['channel_id'])->increment('wallet_balance', $request['price']);

                    return $this->common->API_Response(200, __('api_msg.gift_send_successfully'));
                }
                return $this->common->API_Response(400, __('api_msg.data_not_save'));
            }
            return $this->common->API_Response(400, __('api_msg.wallet_insufficient'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function send_gift_transaction(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user_id = $request['user_id'];

            $page_size = 0;
            $current_page = 0;
            $more_page = false;

            $data = Send_Gift::where('user_id', $user_id)->with('channel', 'content', 'gift')->latest()->orderBy('id', 'desc');

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

                    $data[$i]['gift_name'] = $data[$i]['gift']['name'] ?? "";
                    $data[$i]['channel_name'] = $data[$i]['channel']['channel_name'] ?? "";
                    $data[$i]['full_name'] = $data[$i]['channel']['full_name'] ?? "";
                    $data[$i]['content_name'] = $data[$i]['content']['title'] ?? "";
                    unset($data[$i]['channel'], $data[$i]['content'], $data[$i]['gift']);
                }
                return $this->common->API_Response(200, __('api_msg.data_retrieved'), $data, $pagination);
            } else {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
