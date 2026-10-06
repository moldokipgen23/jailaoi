<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\General_Setting;
use RuntimeException;

class RevenueSettlement
{
    public static function cents($value): int {
        if (!preg_match('/^([0-9]{1,10})(?:\.([0-9]{1,2}))?$/D', (string)$value, $m)) throw new RuntimeException('Revenue amounts must be positive INR amounts with at most two decimal places.');
        return ((int)$m[1])*100+(int)str_pad($m[2]??'',2,'0');
    }
    public function period(string $month): array {
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/D',$month) || $month >= now()->format('Y-m')) throw new RuntimeException('Choose a completed month in YYYY-MM format.');
        $start=$month.'-01 00:00:00'; return [$start,date('Y-m-d H:i:s',strtotime($start.' +1 month'))];
    }
    public function locked(callable $callback) {
        $locked=false;
        try {
            if(DB::getDriverName()==='mysql') {
                $locked=(int)DB::selectOne("SELECT GET_LOCK('jailaoi:earnings-settle', 0) AS acquired")->acquired===1;
                if(!$locked) throw new RuntimeException('Another revenue operation is running. Please try again.');
            }
            return DB::transaction($callback);
        } finally { if($locked)DB::selectOne("SELECT RELEASE_LOCK('jailaoi:earnings-settle')"); }
    }
    public function calculate(string $month): array {
        [$start,$end]=$this->period($month);
        if((General_Setting::where('key','earnings_model')->value('value')??'pool')!=='pool')throw new RuntimeException('Revenue review requires pool mode.');
        if((General_Setting::where('key','payout_currency')->value('value')??'INR')!=='INR')throw new RuntimeException('Revenue review currently supports INR only.');
        $cut=(string)(General_Setting::where('key','platform_cut_pct')->value('value')??'30');
        if(!is_numeric($cut)||(float)$cut<0||(float)$cut>100)throw new RuntimeException('Platform share must be between 0 and 100%.');
        $hash=hash_init('sha256'); hash_update($hash,$month.'|'.$cut);
        $subscription=0;$excluded=0;$unmatched=[];$seen=[];$ignoreEntries=[];
        $reconciled=DB::table('tbl_revenue_entries')->where('month',$month)->where('kind','subscription_income')->get()->keyBy('reference');
        $payments=DB::table('tbl_transaction')->where('created_at','>=',$start)->where('created_at','<',$end)->orderBy('id')->get();
        foreach($payments as $p){
            hash_update($hash,json_encode((array)$p));
            $verified=false;
            if($p->description==='cashfree') {
                $order=DB::table('tbl_cashfree_orders')->where('order_id',$p->transaction_id)->first();
                hash_update($hash,json_encode($order));
                $verified=$order && $order->status==='paid' && (int)$order->user_id===(int)$p->user_id && (int)$order->package_id===(int)$p->package_id && self::cents($order->amount)===self::cents($p->price);
            } elseif($p->description==='cashfree_subscription' && !empty($p->cf_subscription_id)) {
                $sub=DB::table('tbl_cashfree_subscriptions')->where('subscription_id',$p->cf_subscription_id)->first();
                hash_update($hash,json_encode($sub));
                $verified=$sub && (int)$sub->user_id===(int)$p->user_id && (int)$sub->package_id===(int)$p->package_id && !empty($p->transaction_id) && self::cents($sub->plan_amount)===self::cents($p->price);
            }
            if($verified && !isset($seen[$p->transaction_id])){$subscription+=self::cents($p->price);$seen[$p->transaction_id]=true;$proof=$reconciled->get('transaction:'.$p->id);if($proof)$ignoreEntries[$proof->id]=true;}else { $proof=$reconciled->get('transaction:'.$p->id); if($verified || !$proof || (int)$proof->amount_cents!==self::cents($p->price)){ $excluded++;$unmatched[]=['id'=>$p->id,'amount'=>$p->price,'provider'=>$p->description]; } }
        }
        $ads=0;$deductions=0;$adjustment=0;
        foreach(DB::table('tbl_revenue_entries')->where('month',$month)->orderBy('id')->get() as $entry){
            hash_update($hash,json_encode((array)$entry));
            if(isset($ignoreEntries[$entry->id]))continue;
            if($entry->kind==='ad_income')$ads+=(int)$entry->amount_cents;
            elseif($entry->kind==='subscription_income')$adjustment+=(int)$entry->amount_cents;
            else $deductions+=(int)$entry->amount_cents;
        }
        $net=$subscription+$adjustment+$ads-$deductions;
        $blockers=[];if(!config('services.revenue.identity_enforced',false))$blockers[]='Verified listener login enforcement must be activated before payouts.';if($net<0)$blockers[]='Deductions exceed confirmed revenue.';
        if($excluded)$blockers[]=$excluded.' unmatched or duplicate payment records need reconciliation.';
        $streams=[];$ids=[];$daily=[];$duplicates=0;
        $artists=DB::table('tbl_artist')->orderBy('id')->get();
        $eligible=[];
        foreach($artists as $a){
            $approved=DB::table('tbl_monetization_applications')->where('artist_id',$a->id)->where('status','approved')->exists();
            $enabled=(int)$a->status===1 && (int)($a->is_suspended??0)===0 && $approved;
            $eligible[$a->id]=$enabled;hash_update($hash,$a->id.'|'.(int)$enabled);
        }
        DB::table('tbl_artist_earnings')->where('created_at','>=',$start)->where('created_at','<',$end)->orderBy('id')->chunkById(1000,function($rows)use(&$streams,&$ids,&$daily,&$duplicates,$eligible,$hash){
            foreach($rows as $row){
                hash_update($hash,json_encode((array)$row));
                if($row->settled_month!==null)continue;
                $type=(int)$row->content_type===3?8:(int)$row->content_type;
                if(!in_array($type,[1,8],true)||!($eligible[$row->artist_id]??false))continue;
                $key=$row->artist_id.'|'.$row->user_id.'|'.$row->content_id.'|'.$type.'|'.substr($row->created_at,0,10);
                if(isset($daily[$key]))$duplicates++;$daily[$key]=true;
                $streams[$row->artist_id]=($streams[$row->artist_id]??0)+1;
                $ids[$row->artist_id]=['last_id'=>$row->id];
            }
        });
        if($duplicates)$blockers[]=$duplicates.' duplicate eligible stream credits must be reconciled.';
        $total=array_sum($streams);if(!$total)$blockers[]='No eligible stream credits. No money will be distributed.';
        $pool=(int)round(max(0,$net)*(100-(float)$cut)/100);
        $allocations=self::allocate($pool,$streams);
        return ['month'=>$month,'currency'=>'INR','subscription_cents'=>$subscription,'subscription_adjustment_cents'=>$adjustment,'ad_cents'=>$ads,'deduction_cents'=>$deductions,'net_cents'=>$net,'platform_pct'=>(float)$cut,'platform_cents'=>max(0,$net)-$pool,'pool_cents'=>$pool,'total_streams'=>$total,'excluded_payments'=>$excluded,'unmatched_payments'=>$unmatched,'blockers'=>$blockers,'allocations'=>$allocations,'streams'=>$streams,'last_rows'=>$ids,'fingerprint'=>hash_final($hash)];
    }
    public static function allocate(int $pool,array $streams): array {
        $total=array_sum($streams);if(!$total)return [];
        $out=[];$remainders=[];$used=0;
        foreach($streams as $id=>$count){$product=$pool*$count;if(!is_int($product))throw new RuntimeException('Revenue allocation exceeds supported limits.');$out[$id]=intdiv($product,$total);$used+=$out[$id];$remainders[$id]=$product%$total;}
        uksort($remainders,fn($a,$b)=>($remainders[$b]<=>$remainders[$a])?:($a<=>$b));
        foreach(array_keys($remainders) as $id){if($used>=$pool)break;$out[$id]++;$used++;}return $out;
    }
    public function prepare(string $month,?int $admin=null): array {
        return $this->locked(function()use($month,$admin){
            if(DB::table('tbl_earnings_settlements')->where('month',$month)->exists())throw new RuntimeException('This month is already settled. Historical credits cannot be overwritten.');
            $snapshot=$this->calculate($month);
            DB::table('tbl_revenue_reviews')->updateOrInsert(['month'=>$month],['status'=>'review','snapshot'=>json_encode($snapshot),'fingerprint'=>$snapshot['fingerprint'],'prepared_by'=>$admin,'approved_by'=>null,'approved_at'=>null,'updated_at'=>now(),'created_at'=>now()]);
            return $snapshot;
        });
    }
    public function approve(string $month,string $fingerprint,int $admin): void {
        $this->locked(function()use($month,$fingerprint,$admin){
            $review=DB::table('tbl_revenue_reviews')->where('month',$month)->lockForUpdate()->first();
            if(!$review||$review->status!=='review'||!hash_equals($review->fingerprint,$fingerprint))throw new RuntimeException('The review changed. Refresh and review it again.');
            if(DB::table('tbl_earnings_settlements')->where('month',$month)->exists())throw new RuntimeException('This month is already settled.');
            // Serializes against stream credits and withdrawals before verifying the snapshot.
            DB::table('tbl_artist')->orderBy('id')->lockForUpdate()->get();
            $snapshot=$this->calculate($month);
            if(!hash_equals($snapshot['fingerprint'],$review->fingerprint))throw new RuntimeException('Revenue, eligibility or streams changed. Prepare a fresh review.');
            if($snapshot['blockers'])throw new RuntimeException(implode(' ',$snapshot['blockers']));
            [$start,$end]=$this->period($month);
            foreach($snapshot['allocations'] as $artistId=>$cents){
                $count=$snapshot['streams'][$artistId];$micros=$cents*10000;$base=intdiv($micros,$count);$remainder=$micros-$base*$count;
                $q=DB::table('tbl_artist_earnings')->where('artist_id',$artistId)->whereNull('settled_month')->whereIn('content_type',[1,3,8])->where('created_at','>=',$start)->where('created_at','<',$end);
                $changed=$q->update(['amount'=>number_format($base/1000000,6,'.',''),'settled_month'=>$month]);
                if($changed!==$count)throw new RuntimeException('Stream records changed during settlement.');
                if($remainder)DB::table('tbl_artist_earnings')->where('id',$snapshot['last_rows'][$artistId]['last_id'])->update(['amount'=>number_format(($base+$remainder)/1000000,6,'.','')]);
                DB::table('tbl_artist')->where('id',$artistId)->increment('wallet_balance',$cents/100);
                DB::table('tbl_artist_statements')->insert(['artist_id'=>$artistId,'month'=>$month,'stream_credits'=>$count,'amount_cents'=>$cents,'review_id'=>$review->id,'created_at'=>now(),'updated_at'=>now()]);
            }
            DB::table('tbl_earnings_settlements')->insert(['month'=>$month,'total_revenue'=>($snapshot['subscription_cents']+$snapshot['subscription_adjustment_cents'])/100,'platform_cut'=>$snapshot['platform_cents']/100,'pool_amount'=>$snapshot['pool_cents']/100,'total_streams'=>$snapshot['total_streams'],'rate_per_stream'=>round($snapshot['pool_cents']/100/$snapshot['total_streams'],6),'additional_revenue'=>$snapshot['ad_cents']/100,'settled_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
            DB::table('tbl_revenue_reviews')->where('id',$review->id)->update(['status'=>'settled','approved_by'=>$admin,'approved_at'=>now(),'updated_at'=>now()]);
        });
    }
}
