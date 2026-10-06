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

}
