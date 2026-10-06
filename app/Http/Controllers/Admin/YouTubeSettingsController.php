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
        return view('admin.youtube.index', ['configured'=>$import->apiKey() !== '']);
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
}
