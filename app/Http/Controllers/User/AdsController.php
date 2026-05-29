<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Ads_View_Click_Count;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdsController extends Controller
{
    private $folder = "ads";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $user = User_Data();

            $input_search = $request['input_search'];
            $input_type = $request['input_type'];

            $query = Ads::where('user_id', $user['id']);
            if ($input_search) {
                $query->where('title', 'LIKE', "%{$input_search}%");
            }
            if ($input_type != 0) {
                $query->where('type', $input_type);
            }
            $params['data'] = $query->orderBy('id', 'DESC')->paginate(18);

            for ($i = 0; $i < count($params['data']); $i++) {

                $params['data'][$i]['image'] = $this->common->getImage($this->folder, $params['data'][$i]['image'], $params['data'][$i]['image_storage_type']);
                if ($params['data'][$i]['type'] == 3) {
                    $params['data'][$i]['video'] = $this->common->getVideo($this->folder, $params['data'][$i]['video'], $params['data'][$i]['video_storage_type']);
                }
            }
            return view('user.ads.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            return view('user.ads.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $user = User_Data();

            $rules = [
                'title' => 'required',
                'redirect_uri' => 'required',
                'budget' => 'required|numeric|min:1',
                'type' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg',
            ];
            if ($request['type'] == 3) {
                $rules['video'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            // Budget Check
            $user_budget = $this->common->get_user_budget($user['id']);
            if ($user_budget < $request['budget']) {
                return response()->json(['status' => 400, 'errors' => __('label.recharge_you_wallet')]);
            }

            $storage_type = Storage_Type();

            $requestData = $request->all();
            $requestData['user_id'] = $user['id'];
            $requestData['image_storage_type'] = $storage_type;
            $requestData['video_storage_type'] = $storage_type;

            $file = $requestData['image'];
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'ads_', $requestData['image_storage_type']);
            if ($requestData['type'] == 3) {

                if ($requestData['video_storage_type'] == 1) {
                    $requestData['video'] = $requestData['video'];
                } else {
                    $requestData['video'] = $this->common->saveImage($requestData['video'], $this->folder, 'vid_', $requestData['video_storage_type']);
                }
            } else {
                $requestData['video'] = "";
            }
            $requestData['status'] = 1;
            $requestData['is_hide'] = 0;

            $data = Ads::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_ads')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_ads')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($ads_id)
    {
        try {

            $params['ads_id'] = $ads_id;
            $params['data'] = Ads::where('id', $ads_id)->with('user')->first();
            $params['total_ads_cpv'] = Ads_View_Click_Count::where('ads_id', $ads_id)->where('type', 1)->count();
            $params['total_ads_cpc'] = Ads_View_Click_Count::where('ads_id', $ads_id)->where('type', 2)->count();
            $params['total_use_budget'] = Ads_View_Click_Count::where('ads_id', $ads_id)->sum('total_coin');
            $params['total_ads_cpv_coin'] = Ads_View_Click_Count::where('ads_id', $ads_id)->where('type', 1)->sum('total_coin');
            $params['total_ads_cpc_coin'] = Ads_View_Click_Count::where('ads_id', $ads_id)->where('type', 2)->sum('total_coin');
            return view('user.ads.edit', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Ads::where('id', $id)->first();
            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image'], $data['image_storage_type']);
                $this->common->deleteImageToFolder($this->folder, $data['video'], $data['video_storage_type']);
                $data->delete();

                Ads_View_Click_Count::where('ads_id', $id)->delete();
            }
            return redirect()->route('user.ads.index')->with('success', __('label.ads_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
