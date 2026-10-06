<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\RevenueSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;
class RevenueController extends Controller {
    public function index(Request $request) {
        $month=$request->query('month',now()->subMonthNoOverflow()->format('Y-m'));
        $request->validate(['month'=>'nullable|date_format:Y-m']);
        $service=app(RevenueSettlement::class);try{$service->period($month);}catch(Throwable $e){abort(422,$e->getMessage());}
        $review=DB::table('tbl_revenue_reviews')->where('month',$month)->first();
        $snapshot=$review?json_decode($review->snapshot,true):$service->calculate($month);
        $settlements=DB::table('tbl_earnings_settlements')->orderByDesc('month')->limit(24)->get();
        $entries=DB::table('tbl_revenue_entries')->where('month',$month)->orderByDesc('id')->get();
        $names=DB::table('tbl_artist')->whereIn('id',array_keys($snapshot['streams']))->pluck('name','id');
        return view('admin.earnings.settlement',compact('month','review','snapshot','settlements','entries','names'));
    }
    private function administrator(): int {
        $admin=Auth::guard('admin')->user();abort_unless($admin&&$admin->role==='super_admin',403);return (int)$admin->id;
    }
    public function prepare(Request $request) {
        $id=$this->administrator();$request->validate(['month'=>'required|date_format:Y-m']);
        try{app(RevenueSettlement::class)->prepare($request->month,$id);return back()->with('success','Review updated. Wallet balances have not changed.');}
        catch(Throwable $e){return back()->with('error',$e->getMessage());}
    }
    public function approve(Request $request) {
        $id=$this->administrator();$request->validate(['month'=>'required|date_format:Y-m','fingerprint'=>'required|string|size:64','reconciled'=>'accepted']);
        try{app(RevenueSettlement::class)->approve($request->month,$request->fingerprint,$id);return back()->with('success','Settlement approved. Statements and wallet credits were recorded together.');}
        catch(Throwable $e){return back()->with('error',$e->getMessage());}
    }
    public function entry(Request $request) {
        $id=$this->administrator();$request->validate(['month'=>'required|date_format:Y-m','kind'=>'required|in:ad_income,subscription_income,refund,payment_fee,tax','amount'=>'required|numeric|min:0.01|max:9999999999.99|decimal:0,2','reference'=>'required|string|max:190|unique:tbl_revenue_entries,reference','note'=>'required|string|min:10|max:2000','confirmed'=>'accepted']);
        try{app(RevenueSettlement::class)->locked(function()use($request,$id){
            app(RevenueSettlement::class)->period($request->month);
            if(DB::table('tbl_earnings_settlements')->where('month',$request->month)->exists())throw new \RuntimeException('Settled months cannot be changed.');
            DB::table('tbl_revenue_entries')->insert(['month'=>$request->month,'kind'=>$request->kind,'amount_cents'=>RevenueSettlement::cents($request->amount),'reference'=>$request->reference,'note'=>$request->note,'created_by'=>$id,'created_at'=>now(),'updated_at'=>now()]);
        });return back()->with('success','Confirmed revenue entry added. Recalculate the review before approval.');}
        catch(Throwable $e){return back()->with('error',$e->getMessage());}
    }
}
