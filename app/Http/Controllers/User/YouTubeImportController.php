<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Services\YouTubeMusicImport;
use Illuminate\Http\Request;
class YouTubeImportController extends Controller
{
    public function preview(Request $request, YouTubeMusicImport $import)
    {
        $input = $request->validate(['youtube_url'=>'required|string|max:2048']);
        try { return response()->json(['status'=>200, 'data'=>$import->preview($input['youtube_url'])]); }
        catch (\Illuminate\Validation\ValidationException $e) { return response()->json(['status'=>422, 'message'=>$e->validator->errors()->first()], 422); }
        catch (\Throwable $e) { return response()->json(['status'=>503, 'message'=>'YouTube is unavailable right now. Try again later or add your music manually.'], 503); }
    }
    public function artwork(Request $request, YouTubeMusicImport $import)
    {
        $input=$request->validate(['youtube_url'=>'required|string|max:2048','rights_confirmed'=>'required|accepted']);
        $id=$import->videoId($input['youtube_url']);
        try {
            $response=\Illuminate\Support\Facades\Http::connectTimeout(5)->timeout(12)->withOptions(['allow_redirects'=>false])->get('https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg');
            if (!$response->successful()) abort(422, 'Thumbnail is unavailable. Upload your artwork instead.');
            $bytes=$response->body();
            $image=@getimagesizefromstring($bytes);
            if (strlen($bytes)>5*1024*1024 || !$image || ($image['mime'] ?? '') !== 'image/jpeg') abort(422, 'Thumbnail is unavailable. Upload your artwork instead.');
            return response($bytes,200,['Content-Type'=>'image/jpeg','Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { throw $e; }
        catch (\Throwable $e) { return response()->json(['message'=>'Could not retrieve artwork. Upload your artwork instead.'],503); }
    }

    public function startAudio(Request $request, \App\Services\YouTubeAudioAccess $access, \App\Services\YouTubeAudioExtractor $extractor, YouTubeMusicImport $metadata)
    {
        $userId = (int) auth()->guard('user')->id();
        abort_unless($access->allows($userId), 403, 'YouTube audio import is not enabled for your artist account.');
        $input = $request->validate(['youtube_url'=>'required|string|max:2048', 'rights_confirmed'=>'required|accepted']);
        $videoId = $metadata->videoId($input['youtube_url']);
        abort_unless($extractor->available(), 503, 'Audio import is temporarily unavailable. Use manual upload.');
        $lock = \Illuminate\Support\Facades\Cache::lock('youtube-audio-start:'.$userId, 10);
        if (!$lock->get()) return response()->json(['message'=>'An import request is already being processed.'], 429);
        try {
            $existing = \App\Models\YouTubeAudioImport::where('user_id', $userId)->whereIn('status', ['queued','processing'])->where('updated_at','>',now()->subHour())->first();
            if ($existing) return response()->json(['data'=>$this->audioResult($existing)], 202);
            $query = \App\Models\YouTubeAudioImport::where('user_id', $userId);
            if ((clone $query)->where('created_at','>=',now()->startOfDay())->count() >= config('youtube_audio.daily_attempts') || (clone $query)->where('created_at','>=',now()->startOfMonth())->count() >= config('youtube_audio.monthly_attempts')) return response()->json(['message'=>'Your audio import attempt limit has been reached. Use manual upload.'], 429);
            if (\App\Models\YouTubeAudioImport::whereIn('status',['queued','processing'])->count() >= 5) return response()->json(['message'=>'Audio import is busy. Try later or upload manually.'], 503);
            $import = \App\Models\YouTubeAudioImport::create(['id'=>(string)\Illuminate\Support\Str::uuid(), 'user_id'=>$userId, 'video_id'=>$videoId, 'status'=>'queued', 'phase'=>'queued', 'rights_confirmed_at'=>now(), 'expires_at'=>now()->addDay()]);
            try { \App\Jobs\ImportYouTubeAudio::dispatch($import->id); }
            catch (\Throwable $e) { $import->update(['status'=>'failed','phase'=>'failed','message'=>'The import worker is unavailable. Please upload manually.']); return response()->json(['message'=>$import->message], 503); }
            return response()->json(['data'=>$this->audioResult($import)], 202);
        } finally { $lock->release(); }
    }
    public function audioStatus(string $id)
    {
        $import = \App\Models\YouTubeAudioImport::where('user_id',auth()->guard('user')->id())->findOrFail($id);
        return response()->json(['data'=>$this->audioResult($import)])->header('Cache-Control','private, no-store');
    }
    private function audioResult(\App\Models\YouTubeAudioImport $import): array
    {
        $expired = $import->expires_at->isPast() && $import->status === 'ready';
        $status = $import->content_id ? 'published' : ($expired ? 'expired' : $import->status);
        return ['id'=>$import->id, 'status'=>$status, 'phase'=>$expired ? 'expired' : $import->phase, 'title'=>$import->title, 'message'=>$status === 'published' ? 'This import has already been published. Start another import for a new release.' : ($expired ? 'Imported audio expired. Import again or upload manually.' : $import->message),
            'filename'=>$status === 'ready' ? $import->filename : null,
            'duration'=>$status === 'ready' ? sprintf('%02d:%02d:%02d', intdiv($import->duration_seconds,3600), intdiv($import->duration_seconds%3600,60), $import->duration_seconds%60) : null];
    }

}
