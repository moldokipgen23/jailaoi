<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Common;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class TransactionController extends Controller
{
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $this->common->package_expiry();

            $params['data'] = [];
            $params['package'] = Package::latest()->get();
            $params['user'] = User::latest()->get();
            // Year
            $params['year_sum'] = Transaction::whereYear('created_at', date('Y'))->selectRaw('SUM(price) as total_price')->first();
            // Month
            $params['month_sum'] = Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->selectRaw('SUM(price) as total_price')->first();
            // Today
            $params['today_sum'] = Transaction::whereDate('created_at', date('Y-m-d'))->selectRaw('SUM(price) as total_price')->first();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                $input_user = $request['input_user'];
                $input_package = $request['input_package'];
                $input_type = $request['input_type'];

                $query = Transaction::with('package', 'user');
                if ($input_user != 0) {
                    $query->where('user_id', $input_user);
                }
                if ($input_package != 0) {
                    $query->where('package_id', $input_package);
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

                        $delete = '<form onsubmit="return confirm(\'' . $transaction_delete . '\');" method="POST" action="' . route('admin.transaction.destroy', [$row->id]) . '">
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
            return view('admin.transaction.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create(Request $request)
    {
        try {
            $params['data'] = [];
            $params['user'] = User::where('id', $request['user_id'])->first();
            $params['package'] = Package::get();

            return view('admin.transaction.add', $params);
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

            $url = url('admin/transaction/create');

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
                'package_id' => 'required'
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            Transaction::where('user_id', $request['user_id'])->update(['status' => 0]);

            $package = Package::where('id', $request['package_id'])->first();
            $expiry_date = date('Y-m-d', strtotime('+' . $package['time'] . ' ' . strtolower($package['type'])));

            $insert = new Transaction();
            $insert['user_id'] = $request['user_id'];
            $insert['package_id'] = $request['package_id'];
            $insert['transaction_id'] = 'admin';
            $insert['price'] = $package['price'];
            $insert['expiry_date'] = $expiry_date;
            $insert['description'] = '';
            $insert['status'] = 1;
            if ($insert->save()) {
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

            Transaction::where('id', $id)->delete();
            return redirect()->route('admin.transaction.index')->with('success', __('label.transaction_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
