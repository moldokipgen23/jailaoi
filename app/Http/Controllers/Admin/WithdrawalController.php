<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal_Request;
use App\Models\Common;
use App\Models\General_Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
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
            $params['setting'] = Setting_Data();

            if ($request->ajax()) {

                $input_user = $request['input_user'];
                $input_status = $request['input_status'];

                $query = Withdrawal_Request::with('user');
                if ($input_user !== '0') {
                    $query->where('user_id', $input_user);
                }
                if ($input_status !== 'all') {
                    $query->where('status', $input_status);
                }
                $data = $query->orderby('status', 'asc')->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('date', function ($row) {
                        return date("Y-m-d", strtotime($row->created_at));
                    })
                    ->addColumn('action', function ($row) {
                        if ($row->status == 1) {
                            $showLabel = __('label.completed');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.pending');
                            return "<button type='button' id='$row->id' onclick='change_status($row->id)' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->make(true);
            }
            return view('admin.withdrawal.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Withdrawal_Request::where('id', $id)->first();
            if (isset($data)) {

                $data['status'] = $data['status'] === 1 ? 0 : 1;
                $data->save();
                return response()->json(['status' => 200, 'success' => __('label.status_changed'), 'status_code' => $data['status']]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function save_amount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'min_withdrawal_amount' => 'numeric|min:1',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $data = $request->all();
            $data["min_withdrawal_amount"] = $data['min_withdrawal_amount'] ?? 0;

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting['id'])) {
                    $setting['value'] = $value;
                    $setting->save();
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.setting_save_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
