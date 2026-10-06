<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\General_Setting;

class OperationsHealth
{
    public function inspect(): array
    {
        $checks=[];
        try { DB::select('SELECT 1');$checks['database']=['ok'=>true,'message'=>'Database is reachable.']; }
        catch(\Throwable $e){$checks['database']=['ok'=>false,'message'=>'Database connection failed.'];}
        $total=disk_total_space(base_path());$free=disk_free_space(base_path());
        $checks['disk']=['ok'=>$total>0 && $free/$total>0.15,'message'=>'Keep at least 15% of disk space free.'];
        $file=storage_path('app/private/backups/latest.json');$backup=is_file($file)?json_decode(file_get_contents($file),true):null;
        $fresh=$backup && isset($backup['created_at']) && strtotime($backup['created_at'])>time()-26*3600;
        foreach($backup['files']??[] as $entry){$path=dirname($file).'/'.basename($entry['name']);$fresh=$fresh && is_file($path) && filesize($path)===$entry['bytes'];}
        $checks['backup']=['ok'=>(bool)$fresh,'message'=>$fresh?'Recent encrypted database and local-media backup is present.':'No verified backup within the last 26 hours.'];
        $checks['offsite_backup']=['ok'=>false,'message'=>'Automatic off-server backup destination has not been configured.'];
        $checks['remote_media_backup']=['ok'=>false,'message'=>'Bunny-only media is not included in local backups; restore storage access first.'];
        try{
            $settings=General_Setting::pluck('value','key');
            $checks['push']=['ok'=>!empty($settings['onesignal_apid'])&&!empty($settings['onesignal_rest_key']),'message'=>'OneSignal App ID and server key must both be configured and device delivery tested.'];
            $checks['startio']=['ok'=>($settings['startio_enabled']??'0')!=='1'||!empty($settings['startio_app_id_android']),'message'=>'Enabled Start.io requires an App ID matching the installed Android build.'];
            $checks['meta']=['ok'=>($settings['meta_status']??'0')!=='1'||(!empty($settings['meta_placement_id_banner'])&&!empty($settings['meta_placement_id_interstitial'])),'message'=>'Configured placements still require a real-device delivery test.'];
            $cdn=trim($settings['bunny_cdn_url']??'');
            if($cdn && preg_match('#^https://[a-z0-9.-]+(?:/[^?#]*)?$#i',$cdn)){
                try{$response=Http::timeout(8)->head(rtrim($cdn,'/').'/');$code=$response->status();$checks['media_delivery']=['ok'=>!in_array($code,[401,403])&&$code<500,'message'=>in_array($code,[401,403])?'CDN access is denied; media delivery requires attention.':'CDN endpoint reachable; individual tracks still need playback tests.'];}
                catch(\Throwable $e){$checks['media_delivery']=['ok'=>false,'message'=>'CDN cannot be reached.'];}
            }
            $duplicateGroups=DB::table('tbl_artist_earnings')->select('artist_id','user_id','content_id','content_type')->selectRaw('DATE(created_at) as day')->groupBy('artist_id','user_id','content_id','content_type','day')->havingRaw('COUNT(*) > 1')->get()->count();
            $negative=DB::table('tbl_artist')->where('wallet_balance','<',0)->count();
            $checks['finance']=['ok'=>$duplicateGroups===0&&$negative===0,'message'=>$duplicateGroups.' duplicate daily earning groups; '.$negative.' negative balances. Reconcile before affected payouts.'];
        }catch(\Throwable $e){$checks['configuration']=['ok'=>false,'message'=>'Configuration or financial checks could not complete.'];}
        $identity=(bool)config('services.revenue.identity_enforced',false);
        $checks['api_identity']=['ok'=>$identity,'message'=>$identity?'Mandatory verified API sessions are active. Released-app sign-in still needs device verification.':'Mandatory API session verification is pending released-app compatibility verification.'];
        return ['checked_at'=>gmdate('c'),'checks'=>$checks,'issue_count'=>count(array_filter($checks,fn($c)=>!$c['ok']))];
    }
    public function save(array $report): void
    {
        $dir=storage_path('app/private/operations');if(!is_dir($dir))mkdir($dir,0700,true);
        file_put_contents($dir.'/health.json.tmp',json_encode($report));chmod($dir.'/health.json.tmp',0600);rename($dir.'/health.json.tmp',$dir.'/health.json');
    }
}
