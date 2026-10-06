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
}
