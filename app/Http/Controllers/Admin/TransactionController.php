<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\User;
use App\Models\Common;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
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
            $params['package'] = Package::get();
            if ($request->ajax()) {

                $input_type = $request['input_type'];
                $input_search = $request['input_search'];
                $input_package = $request['input_package'];

                if ($input_package != 0) {
                    if ($input_type == "today") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->where('package_id', $input_package)
                                ->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {

                            $data = Transaction::with('package', 'user')->where('package_id', $input_package)->whereDay('created_at', date('d'))
                                ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else if ($input_type == "month") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->where('package_id', $input_package)
                                ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {

                            $data = Transaction::with('package', 'user')->where('package_id', $input_package)->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else if ($input_type == "year") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->where('package_id', $input_package)
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {

                            $data = Transaction::with('package', 'user')->where('package_id', $input_package)
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->where('package_id', $input_package)->orderBy('status', 'desc')->latest()->get();
                        } else {
                            $data = Transaction::with('package', 'user')->where('package_id', $input_package)->orderBy('status', 'desc')->latest()->get();
                        }
                    }
                } else {
                    if ($input_type == "today") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {

                            $data = Transaction::with('package', 'user')->whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else if ($input_type == "month") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {

                            $data = Transaction::with('package', 'user')->whereMonth('created_at', date('m'))
                                ->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else if ($input_type == "year") {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        } else {
                            $data = Transaction::with('package', 'user')->whereYear('created_at', date('Y'))->orderBy('status', 'desc')->latest()->get();
                        }
                    } else {

                        if ($input_search != null && isset($input_search)) {

                            $data = Transaction::with('package', 'user')
                                ->whereHas('user', function ($query) use ($input_search) {
                                    $query->Where('full_name', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('email', 'LIKE', "%{$input_search}%");
                                    $query->orWhere('mobile_number', 'LIKE', "%{$input_search}%");
                                })
                                ->orWhere('transaction_id', 'LIKE', "%{$input_search}%")->orderBy('status', 'desc')->latest()->get();
                        } else {
                            $data = Transaction::with('package', 'user')->orderBy('status', 'desc')->latest()->get();
                        }
                    }
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'Are you sure !!! You want to Delete this Transaction ?\');" method="POST"  action="' . route('transaction.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn" style="outline: none;" title="Delete"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            return "<button type='button' style='background:#058f00; font-size:14px; font-weight:bold; border: none;  color: white; padding: 4px 20px; outline: none;'>Active</button>";
                        } else {
                            return "<button type='button' style='background:#e3000b; font-size:14px; font-weight:bold; letter-spacing:0.1px; border: none; color: white; padding: 5px 15px; outline: none;'>Expiry</button>";
                        }
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("Y-m-d", strtotime($row->created_at));
                        return $date;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.transaction.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function create(Request $request)
    {
        try {

            $params['data'] = [];
            $params['user'] = User::where('id', $request->user_id)->first();
            $params['package'] = Package::get();

            return view('admin.transaction.add', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $package = Package::where('id', $request->package_id)->first();
            $expiry_date = date('Y-m-d', strtotime('+' . $package->time . ' ' . strtolower($package->type)));

            $Transction = new Transaction();
            $Transction->user_id = $request->user_id;
            $Transction->package_id = $request->package_id;
            $Transction->transaction_id = 'admin';
            $Transction->price = $package->price;
            $Transction->description = $package->name;
            $Transction->expiry_date = $expiry_date;
            $Transction->status = 1;
            if ($Transction->save()) {

                $transactions = Transaction::where('user_id', $request->user_id)->where('id', "!=", $Transction->id)->get();
                foreach ($transactions as $data) {
                    $data->update(['status' => 0]); 
                }
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('label.Transction_Not_Add')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function searchUser(Request $request)
    {
        try {
            $name = $request->name;
            $user = User::orWhere('full_name', 'like', '%' . $name . '%')->orWhere('mobile_number', 'like', '%' . $name . '%')->orWhere('email', 'like', '%' . $name . '%')->latest()->get();

            $url = url('admin/transaction/create?user_id');
            $text = '<table width="100%" class="table table-striped category-table text-center table-bordered"><tr style="background: #F9FAFF;"><th>Full Name</th><th>Mobile</th><th>Email</th><th>Action</th></tr>';
            if ($user->count() > 0) {
                foreach ($user as $row) {

                    $a = '<a class="btn-link" href="' . $url . '=' . $row->id . '">Select</a>';
                    $text .= '<tr><td>' . $row->full_name . '</td><td>' . $row->mobile_number . '</td><td>' . $row->email . '</td><td>' . $a . '</td></tr>';
                }
            } else {
                $text .= '<tr><td colspan="4">User Not Found</td></tr>';
            }
            $text .= '</table>';

            return response()->json(array('status' => 200, 'success' => 'Search User', 'result' => $text));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function destroy($id)
    {
        try {

            $data = Transaction::where('id', $id)->first();
            if (isset($data)) {
                $data->delete();
            }
            return redirect()->route('transaction.index')->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
