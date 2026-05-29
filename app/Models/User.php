<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'tbl_user';
    protected $guarded = array();

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'string',
        'channel_name' => 'string',
        'full_name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'country_code' => 'string',
        'mobile_number' => 'string',
        'country_name' => 'string',
        'type' => 'integer',
        'image_storage_type' => 'integer',
        'image' => 'string',
        'cover_img_storage_type' => 'integer',
        'cover_img' => 'string',
        'description' => 'string',
        'device_type' => 'integer',
        'device_token' => 'string',
        'website' => 'string',
        'facebook_url' => 'string',
        'instagram_url' => 'string',
        'twitter_url' => 'string',
        'wallet_balance' => 'integer',
        'wallet_earning' => 'integer',
        'is_account_verify' => 'integer',
        'bank_name' => 'string',
        'bank_code' => 'string',
        'bank_address' => 'string',
        'ifsc_no' => 'string',
        'account_no' => 'string',
        'front_id_proof_storage_type' => 'integer',
        'front_id_proof' => 'string',
        'back_id_proof_storage_type' => 'integer',
        'back_id_proof' => 'string',
        'address' => 'string',
        'city' => 'string',
        'state' => 'string',
        'country' => 'string',
        'pincode' => 'integer',
        'user_penal_status' => 'integer',
        'reference_code' => 'string',
        'push_notification_status' => 'integer',
        'send_mail_status' => 'integer',
        'status' => 'integer',
    ];
}
