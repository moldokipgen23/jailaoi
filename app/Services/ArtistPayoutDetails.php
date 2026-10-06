<?php
namespace App\Services;
use Illuminate\Support\Facades\Validator;
class ArtistPayoutDetails
{
    public function validate(string $method, $details): array
    {
        if (is_string($details)) $details = json_decode($details, true);
        $details = is_array($details) ? $details : [];
        $rules = match ($method) {
            'bank' => ['bank_name'=>'required|string|max:100', 'account_name'=>'required|string|max:100', 'account_number'=>'required|regex:/^[0-9]{6,34}$/', 'swift'=>'nullable|string|max:100'],
            'upi' => ['upi_id'=>'required|regex:/^[a-zA-Z0-9._-]{2,256}@[a-zA-Z0-9.-]{2,64}$/', 'account_name'=>'required|string|max:100', 'bank_name'=>'nullable|string|max:100'],
            default => ['unsupported_payment_method'=>'required'],
        };
        return Validator::make($details, $rules)->validate();
    }
}
