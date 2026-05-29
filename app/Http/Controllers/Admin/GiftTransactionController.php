<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Common;
use App\Models\Gift;
use App\Models\Gift_Transaction;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class GiftTransactionController extends Controller
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
            $params['gift'] = Gift::latest()->get();
            $params['user'] = User::latest()->get();

            if ($request->ajax()) {

                $input_user = $request['input_user'];
                $input_gift = $request['input_gift'];
                $input_type = $request['input_type'];

                $query = Gift_Transaction::with('gift', 'user');
                if ($input_user != 0) {
                    $query->where('user_id', $input_user);
                }
                if ($input_gift != 0) {
                    $query->where('gift_id', $input_gift);
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

                        $delete = '<form onsubmit="return confirm(\'' . $transaction_delete . '\');" method="POST" action="' . route('admin.gifttransaction.destroy', [$row->id]) . '">
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
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.gift_transaction.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            Gift_Transaction::where('id', $id)->delete();
            return redirect()->route('admin.gifttransaction.index')->with('success', __('label.transaction_delete'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
