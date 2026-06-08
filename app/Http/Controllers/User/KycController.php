<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistKyc;
use App\Models\Common;
use App\Models\General_Setting;
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

    private function getSettings()
    {
        $rows = General_Setting::whereIn('key', [
            'allowed_payment_methods',
            'allowed_id_types',
            'kyc_required_for_withdrawal',
        ])->pluck('value', 'key');

        return [
            'allowed_payment_methods' => array_filter(explode(',', $rows['allowed_payment_methods'] ?? 'bank,upi')),
            'allowed_id_types'        => array_filter(explode(',', $rows['allowed_id_types'] ?? 'passport,national_id,drivers_license')),
            'kyc_required_for_withdrawal' => ($rows['kyc_required_for_withdrawal'] ?? '1') === '1',
        ];
    }

    public function index()
    {
        try {
            $user = Auth::guard('user')->user();
            if (!$user) return redirect()->route('user.login');

            $artist = Artist::where('user_id', $user->id)->first();
            if (!$artist) return redirect()->route('user.dashboard');

            $kyc = ArtistKyc::where('user_id', $user->id)->latest()->first();
            $settings = $this->getSettings();

            return view('user.kyc.index', [
                'kyc'      => $kyc,
                'artist'   => $artist,
                'user'     => $user,
                'settings' => $settings,
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

            // Block re-submission while under review — allow if approved (payment update)
            $existing = ArtistKyc::where('user_id', $user->id)->whereIn('status', ['submitted', 'under_review'])->first();
            if ($existing) return response()->json(['status' => 400, 'errors' => 'Your KYC is currently under review. Please wait for the result before resubmitting.']);

            // Check if updating an approved KYC (payment details change)
            $approvedKyc = ArtistKyc::where('user_id', $user->id)->where('status', 'approved')->latest()->first();

            $settings = $this->getSettings();
            $allowedMethods = implode(',', $settings['allowed_payment_methods']);
            $allowedIdTypes = implode(',', $settings['allowed_id_types']);

            $rules = [
                'legal_first_name' => 'required|string|max:100',
                'legal_last_name'  => 'required|string|max:100',
                'date_of_birth'    => 'required|date',
                'nationality'      => 'required|string|max:100',
                'id_type'          => 'required|in:' . $allowedIdTypes,
                'id_number'        => 'required|string|max:100',
                'id_front_img'     => ($request->input('_keep_existing_images') && $approvedKyc) ? 'nullable' : 'required|image|mimes:jpeg,png,jpg|max:5120',
                'id_back_img'      => ($request->input('_keep_existing_images') && $approvedKyc) ? 'nullable' : 'required|image|mimes:jpeg,png,jpg|max:5120',
                'address'          => 'required|string|max:500',
                'city'             => 'required|string|max:100',
                'country'          => 'required|string|max:100',
                'payment_method'   => 'required|in:' . $allowedMethods,
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

            // JAILAOI: keep existing images when artist updates payment details after approval
            if ($request->input('_keep_existing_images') && $approvedKyc) {
                $id_front = $approvedKyc->id_front_img;
                $id_back  = $approvedKyc->id_back_img;
            } else {
                $id_front = $this->common->saveImage($request->file('id_front_img'), $this->folder_kyc, 'kyc_front_');
                $id_back  = $this->common->saveImage($request->file('id_back_img'), $this->folder_kyc, 'kyc_back_');
            }

            $paymentDetails = $request->payment_details;
            $decoded = json_decode($paymentDetails, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = ['raw' => $paymentDetails];
            }

            $kycData = [
                'artist_id'        => $artist->id,
                'user_id'          => $user->id,
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
                'admin_note'       => null,
                'reviewed_at'      => null,
            ];

            if ($approvedKyc) {
                // JAILAOI: artist updating payment details after approval — reset for re-review
                $approvedKyc->fill($kycData)->save();
                $msg = 'Payment details updated. Your KYC will be re-reviewed within 3-5 business days. Withdrawals are paused until re-approved.';
            } else {
                ArtistKyc::create($kycData);
                $msg = 'KYC submitted successfully. We will review it within 3-5 business days.';
            }

            return response()->json(['status' => 200, 'success' => $msg]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
