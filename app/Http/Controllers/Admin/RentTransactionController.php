<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Common;
use App\Models\Content;
use App\Models\Rent_Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class RentTransactionController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $this->common->rent_expiry();

            $params['data'] = [];
            $params['user'] = User::latest()->get();
            $params['content'] = Content::where('content_type', 1)->where('status', 1)->where('is_rent', 1)->latest()->get();
            // Year
            $params['year_sum'] = Rent_Transaction::whereYear('created_at', date('Y'))->selectRaw('SUM(admin_commission) as total_admin_commission, SUM(user_wallet_amount) as total_user_wallet_amount')->first();
            // Month
            $params['month_sum'] = Rent_Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->selectRaw('SUM(admin_commission) as total_admin_commission, SUM(user_wallet_amount) as total_user_wallet_amount')->first();
            // Today
            $params['today_sum'] = Rent_Transaction::whereDate('created_at', date('Y-m-d'))->selectRaw('SUM(admin_commission) as total_admin_commission, SUM(user_wallet_amount) as total_user_wallet_amount')->first();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_content = $request['input_content'];
                $input_user = $request['input_user'];
                $input_type = $request['input_type'];

                $query = Rent_Transaction::with('content', 'user');
                if ($input_user != 0) {
                    $query->where('user_id', $input_user);
                }
                if ($input_content != 0) {
                    $query->where('content_id', $input_content);
                }
                if (!empty($input_search)) {
                    $query->where('transaction_id', 'LIKE', "%{$input_search}%");
                }
                if ($input_type == "today") {
                    $query->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                } elseif ($input_type == "month") {
                    $query->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                } elseif ($input_type == "year") {
                    $query->whereYear('created_at', date('Y'));
                }
                $data = $query->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {

                        $transaction_delete = __('label.delete_transaction');

                        $delete = '<form onsubmit="return confirm(\'' . $transaction_delete . '\');" method="POST" action="' . route('admin.renttransaction.destroy', [$row->id]) . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="edit-delete-btn" style="outline: none;"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('date', function ($row) {
                        return date("Y-m-d", strtotime($row['created_at']));
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            $showLabel = __('label.active');
                            return "<button type='button' class='show-btn'>$showLabel</button>";
                        } else {
                            $hideLabel = __('label.expiry');
                            return "<button type='button' class='hide-btn'>$hideLabel</button>";
                        }
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.rent_transaction.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create(Request $request)
    {
        try {

            $params['data'] = [];
            $params['user'] = User::where('id', $request['user_id'])->first();
            $params['content'] = Content::where('content_type', 1)->where('status', 1)->where('is_rent', 1)->latest()->get();

            return view('admin.rent_transaction.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function search_user(Request $request)
    {
        try {

            $name = $request['name'];
            $users = User::where(function ($query) use ($name) {
                $query->orWhere('channel_name', 'like', '%' . $name . '%')
                    ->orWhere('full_name', 'like', '%' . $name . '%')
                    ->orWhere('mobile_number', 'like', '%' . $name . '%')
                    ->orWhere('email', 'like', '%' . $name . '%');
            })->latest()->get()->take(30);

            $url = url('admin/renttransaction/create');

            $html = '<table width="100%" class="table table-striped category-table text-center table-bordered">';
            $html .= '<tr>';
            $html .= '<th>' . __('label.channel_name') . '</th>';
            $html .= '<th>' . __('label.full_name') . '</th>';
            $html .= '<th>' . __('label.mobile_number') . '</th>';
            $html .= '<th>' . __('label.email') . '</th>';
            $html .= '<th>' . __('label.action') . '</th>';
            $html .= '</tr>';

            if ($users->isNotEmpty()) {
                foreach ($users as $user) {
                    $actionLink = '<a class="btn-link" href="' . $url . '?user_id=' . $user->id . '">Select</a>';
                    $html .= '<tr>';
                    $html .= '<td>' . $user['channel_name'] ?? '' . '</td>';
                    $html .= '<td>' . $user['full_name'] ?? '' . '</td>';
                    $html .= '<td>' . $user['mobile_number'] ?? '' . '</td>';
                    $html .= '<td>' . $user['email'] ?? '' . '</td>';
                    $html .= '<td>' . $actionLink . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td colspan="5">' . __('label.user_not_found') . '</td></tr>';
            }
            return response()->json(['status' => 200, 'result' => $html]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'content_id' => 'required'
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $content = Content::where('id', $request['content_id'])->first();

            $insert = new Rent_Transaction();
            $insert['user_id'] = $request['user_id'];
            $insert['content_id'] = $request['content_id'];
            $insert['transaction_id'] = 'admin';
            $insert['price'] = $content['rent_price'];
            $insert['description'] = 'admin';

            $setting = Setting_Data();
            $admin_commission = $setting['rent_commission'];
            $persentage = round(($admin_commission / 100) * $content['rent_price']);
            $user_wallet_amount = $content['rent_price'] - $persentage;

            $insert['admin_commission'] = $persentage;
            $insert['user_wallet_amount'] = $user_wallet_amount;
            $insert['expiry_date'] = date('Y-m-d', strtotime($content['rent_day'] . ' days'));
            $insert['status'] = 1;

            if ($insert->save()) {

                User::where('channel_id', $content['channel_id'])->increment('wallet_earning', $user_wallet_amount);
                return response()->json(['status' => 200, 'success' => __('label.success_add_transaction')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_transaction')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            Rent_Transaction::where('id', $id)->delete();
            return redirect()->route('admin.renttransaction.index')->with('success', __('label.transaction_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
