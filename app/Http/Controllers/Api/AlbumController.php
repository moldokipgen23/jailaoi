<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Common;
use App\Models\Content;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AlbumController extends Controller
{
    private $folder_content = "content";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function get_album_list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user = User::find($request['user_id']);
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $albums = Album::where('channel_id', $user->channel_id)->where('status', 1)->orderBy('id', 'desc')->get();
            $this->common->imageNameToUrl($albums, 'cover_image', 'content', 'cover_image_storage_type');

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), $albums->toArray());
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_album_detail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'album_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $album = Album::where('id', $request['album_id'])->where('status', 1)->first();
            if (!$album) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $albumArray = $album->toArray();
            $albumArray['cover_image'] = $this->common->getImage($this->folder_content, $album->cover_image, $album->cover_image_storage_type);

            $songs = Content::where('album_id', $album->id)->where('content_type', 2)->where('status', 1)->get();
            for ($i = 0; $i < count($songs); $i++) {
                $songs[$i]['portrait_img'] = $this->common->getImage($this->folder_content, $songs[$i]['portrait_img'], $songs[$i]['portrait_img_storage_type']);
                $songs[$i]['landscape_img'] = $this->common->getImage($this->folder_content, $songs[$i]['landscape_img'], $songs[$i]['landscape_img_storage_type']);
                if ($songs[$i]['content_upload_type'] == 'server_video') {
                    $songs[$i]['content'] = $this->common->getVideo($this->folder_content, $songs[$i]['content'], $songs[$i]['content_storage_type']);
                }
            }
            $albumArray['songs'] = $songs->toArray();

            return $this->common->API_Response(200, __('api_msg.get_record_successfully'), [$albumArray]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function create_album(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'name' => 'required|min:1',
                'cover_image' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $user = User::find($request['user_id']);
            if (!$user) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $album = new Album();
            $album->user_id = $user->id;
            $album->channel_id = $user->channel_id;
            $album->name = $request['name'];
            $album->description = $request['description'] ?? '';
            $album->release_date = $request['release_date'] ?? now();
            if ($request->hasFile('cover_image')) {
                $album->cover_image = $this->common->saveImage($request->file('cover_image'), $this->folder_content, 'album_', 1);
                $album->cover_image_storage_type = 1;
            }
            $album->status = 1;
            $album->save();

            return $this->common->API_Response(200, __('api_msg.success_add_album'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function edit_album(Request $request)
    {
        try {
            $album = Album::where('id', $request['album_id'])->first();
            if (!$album) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            $album->name = $request['name'] ?? $album->name;
            $album->description = $request['description'] ?? $album->description;
            if ($request->has('release_date')) {
                $album->release_date = $request['release_date'];
            }
            if ($request->hasFile('cover_image')) {
                $album->cover_image = $this->common->saveImage($request->file('cover_image'), $this->folder_content, 'album_', 1);
                $album->cover_image_storage_type = 1;
            }
            $album->save();

            return $this->common->API_Response(200, __('api_msg.success_edit_album'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function delete_album(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'album_id' => 'required|numeric',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first());
            }

            $album = Album::where('id', $request['album_id'])->first();
            if (!$album) {
                return $this->common->API_Response(400, __('api_msg.data_not_found'));
            }

            Content::where('album_id', $album->id)->update(['album_id' => null]);
            $album->delete();

            return $this->common->API_Response(200, __('api_msg.success_delete_album'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function get_album_songs(Request $request)
    {
        return $this->get_album_detail($request);
    }
}
