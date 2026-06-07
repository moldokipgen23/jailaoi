<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ArtistRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index()
    {
        return view('user.register.index');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:100',
                'artist_name' => 'required|string|max:100',
                'email' => 'required|email|unique:tbl_user,email',
                'mobile_number' => 'nullable|string|max:20',
                'country_code' => 'nullable|string|max:6',
                'password' => 'required|string|min:6|confirmed',
                'bio' => 'required|string|min:20|max:2000',
                'artist_types' => 'required|array|min:1',
                'artist_types.*' => 'in:music,podcast',
            ], [
                'artist_types.required' => 'Please select at least one content type (Music or Podcast).',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                ]);
            }

            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number ?? '',
                'country_code' => $request->country_code ?? '',
                'password' => Hash::make($request->password),
                'role' => 'user',
                'status' => 1,
                'type' => 1,
                'bio' => $request->bio,
            ]);

            ArtistRequest::create([
                'user_id' => $user->id,
                'artist_name' => $request->artist_name,
                'bio' => $request->bio,
                'artist_types' => implode(',', $request->artist_types),
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => 200,
                'success' => 'Application submitted. We will review and notify you by email.',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
