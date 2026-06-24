<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\User;
use App\Models\Common;
use App\Services\InvoiceService;
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

                $query = Transaction::with(['user', 'package']);

                if (!empty($input_search)) {
                    $query->where(function ($q) use ($input_search) {
                        $q->where('transaction_id', 'LIKE', "%{$input_search}%")
                            ->orWhereHas('user', function ($d) use ($input_search) {
                                $d->where('full_name', 'LIKE', "%{$input_search}%");
                            });
                    });
                }

                if (!empty($input_package)) {
                    $query->where('package_id', $input_package);
                }

                if ($input_type == 'today') {
                    $query->whereDate('created_at', date('Y-m-d'));
                } elseif ($input_type == 'month') {
                    $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'));
                } elseif ($input_type == 'year') {
                    $query->whereYear('created_at', date('Y'));
                }

                $data = $query->latest()->get();

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $invoice = '<a href="' . route('invoice.download', [$row->id]) . '" class="edit-delete-btn" title="' . __('label.invoice') . '" target="_blank"><i class="fa-solid fa-file-invoice fa-xl"></i></a>';
                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_transaction') . '\');" method="POST"  action="' . route('transaction.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn"  title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= $invoice;
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            return "<button class='btn show-btn' type='button' >" . __('label.active') . "</button>";
                        } else {
                            return "<button class='btn hide-btn' type='button' >" . __('label.expired') . "</button>";
                        }
                    })
                    ->addColumn('date', function ($row) {
                        $date = date("d M Y H:i", strtotime($row->created_at));
                        return $date;
                    })
                    ->addColumn('expiry_date', function ($row) {
                        $date = date("d M Y H:i", strtotime($row->expiry_date));
                        return $date;
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
            $params['user'] = User::where('id', $request->user_id)->first();
            $params['package'] = Package::get();

            return view('admin.transaction.add', $params);
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
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $package = Package::where('id', $request->package_id)->first();
            $expiry_date = date('Y-m-d H:i', strtotime('+' . $package->time . ' ' . strtolower($package->type), time()));

            $Transction = new Transaction();
            $Transction->user_id = $request->user_id;
            $Transction->package_id = $request->package_id;
            $Transction->transaction_id = 'admin';
            $Transction->price = $package->price;
            $Transction->description = $package->name;
            $Transction->expiry_date = $expiry_date;
            $Transction->status = 1;
            if ($Transction->save()) {

                $invoicePath = null;
                try {
                    $invoicePath = (new InvoiceService)->generate($Transction);
                } catch (\Exception $e) {
                    // Invoice generation failed — log silently
                }

                // Send Mail (Type = 1- Register Mail, 2 Transaction Mail)
                $user_data = User::where('id', $Transction->user_id)->first();
                if (isset($user_data)) {
                    $check = $this->common->basic_notification_configuration('package-buy');

                    if ($check['status'] == 1 && $check['send_mail'] == 1) {
                        $this->common->Send_Mail(2, $user_data->email, $user_data->full_name, $package->name, $package->price, 'admin', $expiry_date, $invoicePath);
                    }

                    if ($check['status'] == 1 && $check['send_notification'] == 1) {
                        $title = __('label.package_purchase');
                        $message = __('label.package_buy_msg');
                        $this->common->send_push_notification($user_data['device_type'], $user_data['device_token'], $title, $message);
                    }
                }

                $transactions = Transaction::where('user_id', $request->user_id)->where('id', "!=", $Transction->id)->get();
                foreach ($transactions as $data) {
                    $data->update(['status' => 0]);
                }
                return response()->json(['status' => 200, 'success' => __('label.success_add_transaction')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.transaction_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function searchUser(Request $request)
    {
        try {

            $name = $request->name;
            $user = User::where(function ($q) use ($name) {
                $q->where('full_name', 'LIKE', "%{$name}%")
                    ->orWhere('mobile_number', 'LIKE', "%{$name}%")
                    ->orWhere('email', 'LIKE', "%{$name}%");
            })->latest()->get();

            $url = url('admin/transaction/create?user_id');
            $text = '<table width="100%" class="table table-striped category-table text-center table-bordered"><tr class="bg-table"><th>Full Name</th><th>Mobile</th><th>Email</th><th>Action</th></tr>';
            if ($user->count() > 0) {
                foreach ($user as $row) {

                    $a = '<a class="btn-link" href="' . $url . '=' . $row->id . '">Select</a>';
                    $text .= '<tr><td>' . $row->full_name . '</td><td>' . $row->mobile_number . '</td><td>' . $row->email . '</td><td>' . $a . '</td></tr>';
                }
            } else {
                $text .= '<tr><td colspan="4">User Not Found</td></tr>';
            }
            $text .= '</table>';

            return response()->json(['status' => 200, 'success' => __('label.search_user'), 'result' => $text]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Transaction::where('id', $id)->first();
            if (isset($data)) {
                $data->delete();
            }
            return redirect()->route('transaction.index')->with('success', __('label.success_delete_transaction'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
