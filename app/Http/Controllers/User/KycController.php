<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistKyc;
use App\Models\Common;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    private $folder_kyc = "kyc";
    public $common;

    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return redirect()->route('user.login');

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) return redirect()->route('user.dashboard');

            $kyc = ArtistKyc::where('user_id', $user->id)->latest()->first();

            return view('user.kyc.index', [
                'kyc'    => $kyc,
                'artist' => $artist,
                'user'   => $user,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return response()->json(['status' => 400, 'errors' => 'Not authenticated']);

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) return response()->json(['status' => 400, 'errors' => 'Artist profile not found']);

            $existing = ArtistKyc::where('user_id', $user->id)->whereIn('status', ['submitted', 'under_review', 'approved'])->first();
            if ($existing) return response()->json(['status' => 400, 'errors' => 'KYC already submitted']);

            $rules = [
                'legal_first_name' => 'required|string|max:100',
                'legal_last_name'  => 'required|string|max:100',
                'date_of_birth'    => 'required|date',
                'nationality'      => 'required|string|max:100',
                'id_type'          => 'required|in:passport,national_id,drivers_license',
                'id_number'        => 'required|string|max:100',
                'id_front_img'     => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'id_back_img'      => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'address'          => 'required|string|max:500',
                'city'             => 'required|string|max:100',
                'country'          => 'required|string|max:100',
                'payment_method'   => 'required|in:paypal,bank,mobile_money',
                'payment_details'  => 'required|string|max:2000',
                'agree_accurate'   => 'required|accepted',
                'agree_terms'      => 'required|accepted',
            ];

            $validator = Validator::make($request->all(), $rules, [
                'agree_accurate.required' => 'Please confirm the information is accurate.',
                'agree_accurate.accepted' => 'Please confirm the information is accurate.',
                'agree_terms.required'    => 'Please agree to the monetization terms.',
                'agree_terms.accepted'    => 'Please agree to the monetization terms.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->all()]);
            }

            // Upload ID images
            $id_front = $this->common->saveImage($request->file('id_front_img'), $this->folder_kyc, 'kyc_front_');
            $id_back  = $this->common->saveImage($request->file('id_back_img'), $this->folder_kyc, 'kyc_back_');

            // Parse payment_details as JSON
            $paymentDetails = $request->payment_details;
            $decoded = json_decode($paymentDetails, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = ['raw' => $paymentDetails];
            }

            ArtistKyc::create([
                'artist_id'       => $artist->id,
                'user_id'         => $user->id,
                'legal_first_name' => $request->legal_first_name,
                'legal_last_name'  => $request->legal_last_name,
                'date_of_birth'    => $request->date_of_birth,
                'nationality'      => $request->nationality,
                'id_type'          => $request->id_type,
                'id_number'        => $request->id_number,
                'id_front_img'     => $id_front,
                'id_back_img'      => $id_back,
                'address'          => $request->address,
                'city'             => $request->city,
                'country'          => $request->country,
                'payment_method'   => $request->payment_method,
                'payment_details'  => json_encode($decoded),
                'status'           => 'submitted',
            ]);

            return response()->json(['status' => 200, 'success' => 'KYC submitted successfully. We will review it within 3-5 business days.']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
