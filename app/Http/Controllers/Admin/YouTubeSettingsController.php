<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\YouTubeMusicImport;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
class YouTubeSettingsController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->guard('admin')->check() && (auth()->guard('admin')->user()->role ?? 'super_admin') === 'super_admin', 403);
    }
    public function index(YouTubeMusicImport $import)
    {
        $this->authorizeAdmin();
        return view('admin.youtube.index', ['configured'=>$import->apiKey() !== '', 'audioSettings'=>app(\App\Services\YouTubeAudioAccess::class)->settings(), 'artists'=>\App\Models\Artist::where('is_suspended',0)->whereNotNull('user_id')->orderBy('name')->get(['user_id','name']), 'audioImports'=>\App\Models\YouTubeAudioImport::orderByDesc('created_at')->limit(25)->get()]);
    }
    public function save(Request $request)
    {
        $this->authorizeAdmin();
        $validator=\Illuminate\Support\Facades\Validator::make($request->only('api_key'), ['api_key'=>'required|string|min:20|max:256|regex:/^[A-Za-z0-9_-]+$/']);
        if ($validator->fails()) return back()->withErrors($validator);
        $input=$validator->validated();
        General_Setting::updateOrCreate(['key'=>'youtube_api_key_encrypted'], ['value'=>Crypt::encryptString($input['api_key'])]);
        return redirect()->route('admin.youtube.index')->with('success','YouTube API key saved securely. Test a video import to verify the configuration.');
    }
    public function saveAudio(Request $request)
    {
        $this->authorizeAdmin();
        $input = $request->validate(['enabled'=>'nullable|boolean', 'user_ids'=>'nullable|array|max:100', 'user_ids.*'=>'integer|distinct|exists:tbl_artist,user_id']);
        General_Setting::updateOrCreate(['key'=>'youtube_audio_access'], ['value'=>json_encode(['enabled'=>(bool)($input['enabled'] ?? false), 'user_ids'=>array_map('intval',$input['user_ids'] ?? [])])]);
        return redirect()->route('admin.youtube.index')->with('success','Experimental audio import access updated. No subscription charges are enabled.');
    }

}
