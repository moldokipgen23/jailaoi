<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Common;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private $folder_user = "user";
    private $folder_artist = "artist";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            $user = Auth::guard('artist')->user();
            $artist = Artist::where('user_id', $user->id)->first();

            $this->common->imageNameToUrl([$user], 'image', $this->folder_user);
            if ($artist) {
                $this->common->imageNameToUrl([$artist], 'image', $this->folder_artist);
            }

            $params['user'] = $user;
            $params['artist'] = $artist;
            return view('artist.profile.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::guard('artist')->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'bio' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $artist = Artist::where('user_id', $user->id)->first();
            if ($artist) {
                $artist->name = $request->name;
                $artist->bio = $request->bio ?? '';
                if ($request->hasFile('image')) {
                    $artist->image = $this->common->storeImage($request->file('image'), $this->folder_artist);
                }
                $artist->save();
            }

            $user->channel_name = $request->name;
            $user->description = $request->bio ?? '';
            if ($request->hasFile('avatar')) {
                $user->image = $this->common->storeImage($request->file('avatar'), $this->folder_user);
            }
            $user->save();

            return response()->json(['status' => 200, 'success' => __('label.update_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
