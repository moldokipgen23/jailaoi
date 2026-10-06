<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\General_Setting;
class ArtistRevenueOverview {
    public function forArtist($artist): array {
        $settings=General_Setting::whereIn('key',['platform_cut_pct','min_streams_for_payout','min_earnings_for_payout','min_withdrawal_amount','min_play_seconds','earnings_model'])->pluck('value','key');
        $out=['model'=>$settings['earnings_model']??'pool','artist_pct'=>100-(float)($settings['platform_cut_pct']??30),'min_streams'=>(int)($settings['min_streams_for_payout']??1000),'min_earned'=>(float)($settings['min_earnings_for_payout']??200),'min_withdrawal'=>(float)($settings['min_withdrawal_amount']??200),'min_seconds'=>(int)($settings['min_play_seconds']??30),'statements'=>[],'current_credits'=>0,'held'=>0,'paid'=>0];
        if(!$artist)return $out;
        $out['current_credits']=DB::table('tbl_artist_earnings')->where('artist_id',$artist->id)->where('created_at','>=',now()->startOfMonth())->whereNull('settled_month')->count();
        $out['held']=(float)DB::table('tbl_withdrawal_requests')->where('artist_id',$artist->id)->whereIn('status',['pending','approved'])->sum('amount');
        $out['paid']=(float)DB::table('tbl_withdrawal_requests')->where('artist_id',$artist->id)->where('status','paid')->sum('amount');
        $rows=DB::table('tbl_artist_earnings')->where('artist_id',$artist->id)->whereNotNull('settled_month')->select('settled_month')->selectRaw('COUNT(*) as credits, SUM(amount) as earned')->groupBy('settled_month')->orderByDesc('settled_month')->limit(12)->get();
        foreach($rows as $row){
            $statement=DB::table('tbl_artist_statements')->where('artist_id',$artist->id)->where('month',$row->settled_month)->first();
            $review=$statement?DB::table('tbl_revenue_reviews')->where('id',$statement->review_id)->first():null;
            $snapshot=$review?json_decode($review->snapshot,true):null;
            $out['statements'][]=['month'=>$row->settled_month,'credits'=>$statement?(int)$statement->stream_credits:(int)$row->credits,'earned'=>$statement?$statement->amount_cents/100:round((float)$row->earned,2),'pool'=>$snapshot?$snapshot['pool_cents']/100:null,'pool_share'=>$snapshot&&$snapshot['total_streams']?100*$statement->stream_credits/$snapshot['total_streams']:null,'approved_at'=>$review?->approved_at,'legacy'=>!$statement];
        }
        return $out;
    }
}
