<?php
namespace App\Console\Commands;
use App\Models\YouTubeAudioImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
class CleanYouTubeAudioImports extends Command
{
    protected $signature = 'youtube:clean-imports';
    protected $description = 'Expire stalled audio imports and clear temporary extraction files';
    public function handle(): int
    {
        YouTubeAudioImport::where(function ($query) { $query->where(fn($q)=>$q->where('status','processing')->where('updated_at','<',now()->subMinutes(15)))->orWhere(fn($q)=>$q->where('status','queued')->where('updated_at','<',now()->subHour())); })->update(['status'=>'failed','phase'=>'failed','message'=>'Audio import expired. Please try again or upload manually.']);
        YouTubeAudioImport::where('status','ready')->where('expires_at','<',now())->update(['status'=>'expired','phase'=>'expired']);
        foreach (YouTubeAudioImport::whereIn('status',['failed','expired','ready'])->where('updated_at','<',now()->subMinutes(15))->pluck('id') as $id) File::deleteDirectory(storage_path('app/private/youtube-imports/'.$id));
        return self::SUCCESS;
    }
}
