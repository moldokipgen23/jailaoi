<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live_Event;
use App\Models\Common;
use App\Models\Event_Join_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class LiveEventController extends Controller
{
    private $folder = "live_event";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $this->common->update_liveevent_status();
            
            $params['data'] = [];

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                if ($input_search != null && isset($input_search)) {
                    $data = Live_Event::where('title', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Live_Event::latest()->get();
                }

                $this->common->imageNameToUrl($data, 'portrait_img', $this->folder);
                $this->common->imageNameToUrl($data, 'landscape_img', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($row){
                        if ($row->status == 1) {
                            return "<button type='button' style='background:#058f00; font-weight:bold; border: none; color: white; padding: 5px 15px; outline: none;border-radius: 5px;cursor: pointer;'>Open</button>";
                        } else {
                            return "<button type='button' style='background:#e3000b; font-weight:bold; border: none; color: white; padding: 5px 20px; outline: none;border-radius: 5px;cursor: pointer;'>Close</button>";
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'Are you sure !!! You want to Delete this Live Event ?\');" method="POST"  action="' . route('liveevent.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn" style="outline: none;" title="Delete"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around" title="Edit">';
                        $btn .= '<a class="edit-delete-btn edit_live_event" title="Edit" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-title="' . $row->title . '" data-date="' . $row->date . '" data-portrait_img="' . $row->portrait_img . '" data-landscape_img="' . $row->landscape_img . '" data-description="' . $row->description . '" data-start_time="' . $row->start_time . '" data-end_time="' . $row->end_time . '" data-is_paid="' . $row->is_paid . '" data-price="' . $row->price . '" data-link="' . $row->link . '" data-type="' . $row->type . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->addColumn('join_user', function ($row) {
                        $btn = '<a href="' . route('liveevent.user.index', $row->id) . '" class="btn text-white p-1 font-weight-bold" style="background:#4e45b8;">Join User</a> ';
                        return $btn;
                    })
                    ->rawColumns(['status', 'action', 'join_user'])
                    ->make(true);
            }
            return view('admin.live_event.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'is_paid' => 'required',
                'type' => 'required',
                'link' => 'required',
                'description' => 'required',
                'portrait_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            if ($request->is_paid == 1) {
                $validator1 = Validator::make($request->all(), [
                    'price' => 'required',
                ]);
                if ($validator1->fails()) {
                    $errs1 = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs1));
                }
            }

            $requestData = $request->all();
            if ($requestData['is_paid'] == 1) {
                $requestData['price'] = $requestData['price'];
            } else {
                $requestData['price'] = 0;
            }

            $files = $requestData['portrait_img'];
            $files1 = $requestData['landscape_img'];
            $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder, "live_event_");
            $requestData['landscape_img'] = $this->common->saveImage($files1, $this->folder, "live_event_");

            $data = Live_Event::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function update(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'is_paid' => 'required',
                'type' => 'required',
                'link' => 'required',
                'description' => 'required',
                'portrait_img' => 'image|mimes:jpeg,png,jpg|max:2048',
                'landscape_img' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }
            if ($request->is_paid == 1) {
                $validator1 = Validator::make($request->all(), [
                    'price' => 'required',
                ]);
                if ($validator1->fails()) {
                    $errs1 = $validator1->errors()->all();
                    return response()->json(array('status' => 400, 'errors' => $errs1));
                }
            }
            $requestData = $request->all();

            if ($requestData['is_paid'] == 1) {
                $requestData['price'] = $requestData['price'];
            } else {
                $requestData['price'] = 0;
            }

            if (isset($requestData['portrait_img'])) {
                $files = $requestData['portrait_img'];
                $requestData['portrait_img'] = $this->common->saveImage($files, $this->folder, "live_event_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_portrait_img']));
            }
            if (isset($requestData['landscape_img'])) {
                $files = $requestData['landscape_img'];
                $requestData['landscape_img'] = $this->common->saveImage($files, $this->folder, "live_event_");

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_landscape_img']));
            }
            unset($requestData['old_portrait_img'], $requestData['old_landscape_img']);

            $data = Live_Event::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($data->id)) {
                return response()->json(array('status' => 200, 'success' => __('Label.data_edit_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_updated')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function destroy($id)
    {
        try {
            $data = Live_Event::where('id', $id)->first();

            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['portrait_img']);
                $this->common->deleteImageToFolder($this->folder, $data['landscape_img']);
                $data->delete();
            }
            return redirect()->route('liveevent.index')->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    // Join User
    public function LiveEventIndex($id, Request $request)
    {
        try {

            $params['data'] = [];
            $params['liveevent_id'] = $id;

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_type = $request['input_type'];

                if ($input_search != null && isset($input_search)) {

                    if ($input_type == "today") {

                        $data = Event_Join_User::with('user')
                            ->whereHas('user', function ($query) use ($input_search) {
                                $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                            ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))->latest()->get();
                    } else if ($input_type == "month") {

                        $data = Event_Join_User::with('user')
                            ->whereHas('user', function ($query) use ($input_search) {
                                $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                            ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                    } else if ($input_type == "year") {

                        $data = Event_Join_User::with('user')
                            ->whereHas('user', function ($query) use ($input_search) {
                                $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                            ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereYear('created_at', date('Y'))->latest()->get();
                    } else {

                        $data = Event_Join_User::with('user')
                            ->whereHas('user', function ($query) use ($input_search) {
                                $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                            })
                            ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->latest()->get();
                    }
                } else {

                    if ($input_type == "today") {
                        $data = Event_Join_User::where('live_event_id', $id)->with('user')->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                    } else if ($input_type == "month") {
                        $data = Event_Join_User::where('live_event_id', $id)->with('user')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest()->get();
                    } else if ($input_type == "year") {
                        $data = Event_Join_User::where('live_event_id', $id)->with('user')->whereYear('created_at', date('Y'))->latest()->get();
                    } else {
                        $data = Event_Join_User::where('live_event_id', $id)->with('user')->latest()->get();
                    }
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) use ($id) {
                        $delete = '<form onsubmit="return confirm(\'Are you sure !!! You want to Delete this User ?\');" method="POST"  action="' . route('liveevent.user.delete', [$id, $row->id]) . '">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <button type="submit" class="edit-delete-btn" style="outline: none;" title="Delete"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.live_event.join_user', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function LiveEventDelete($liveevent_id, $id)
    {
        try {

            $data = Event_Join_User::where('id', $id)->first();
            if (isset($data)) {
                $data->delete();
            }
            return redirect()->route('liveevent.user.index', $liveevent_id)->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
