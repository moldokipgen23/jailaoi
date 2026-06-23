<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Ads_View_Click_Count;
use App\Models\Common;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdsController extends Controller
{
    private $folder = "images/ads";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['data'] = [];
            $params['user'] = User::latest()->get();

            $input_search = $request['input_search'];
            $input_user = $request['input_user'];
            $input_type = $request['input_type'];

            $query = Ads::query();
            if ($input_search) {
                $query->where('title', 'LIKE', "%{$input_search}%");
            }
            if ($input_user != 0) {
                $query->where('user_id', $input_user);
            }
            if ($input_type != 0) {
                $query->where('type', $input_type);
            }
            $params['data'] = $query->with('user')->orderBy('id', 'DESC')->paginate(18);

            for ($i = 0; $i < count($params['data']); $i++) {

                $params['data'][$i]['image'] = $this->common->getImage($this->folder, $params['data'][$i]['image'], $params['data'][$i]['image_storage_type']);
                if ($params['data'][$i]['type'] == 3) {
                    $params['data'][$i]['video'] = $this->common->getVideo($this->folder, $params['data'][$i]['video'], $params['data'][$i]['video_storage_type']);
                }
            }
            return view('admin.ads.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['data'] = [];
            $params['user'] = User::latest()->get();

            return view('admin.ads.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required',
                'user_id' => 'required',
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
            $user_budget = $this->common->get_user_budget($request['user_id']);
            if ($user_budget < $request['budget']) {
                return response()->json(['status' => 400, 'errors' => __('label.recharge_you_wallet')]);
            }

            $storage_type = Storage_Type();

            $requestData = $request->all();
            $requestData['image_storage_type'] = $storage_type;
            $requestData['video_storage_type'] = $storage_type;

            $file = $requestData['image'];
            // JAILAOI: folder is images/ads so saveImage mirrors to Bunny CDN.
            // Do NOT pass storage_type as 4th arg — that slot is $artistSlug now.
            $requestData['image'] = $this->common->saveImage($file, $this->folder, 'ads_');
            if ($requestData['type'] == 3) {

                if ($requestData['video_storage_type'] == 1) {
                    $requestData['video'] = $requestData['video'];
                } else {
                    $requestData['video'] = $this->common->saveImage($requestData['video'], $this->folder, 'vid_');
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
            return view('admin.ads.edit', $params);
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
            return redirect()->route('admin.ads.index')->with('success', __('label.ads_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function changeStatus(Request $request)
    {
        try {

            $data = Ads::where('id', $request['id'])->first();
            if (isset($data)) {

                $data['is_hide'] = $data['is_hide'] === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data['is_hide']]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function saveChunk()
    {
        @set_time_limit(5 * 60);

        $datePath = date('Y') . '/' . date('m');
        $targetDir = storage_path('/app/public/ads/' . $datePath);
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds

        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

        // Remove old temp files
        if ($cleanupTargetDir && is_dir($targetDir) && $dir = opendir($targetDir)) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // Remove temp file if it is older than the max age and not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
        }

        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$filePath}.part", $filePath);

            // Generate a new filename based on the current date and time
            $extension = pathinfo($fileName, PATHINFO_EXTENSION); // Get the file extension from the original filename
            $newFileName = 'ads' . date('_Y_m_d_') . uniqid() . '.' . $extension; // Use the extracted extension
            $newFilePath = $targetDir . DIRECTORY_SEPARATOR . $newFileName;

            // Rename the uploaded file to the new filename
            rename($filePath, $newFilePath);

            // Send the new file name back to the client
            die(json_encode(array('jsonrpc' => '2.0', 'result' => $datePath . "/" . $newFileName, 'id' => 'id')));
        }

        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
}
