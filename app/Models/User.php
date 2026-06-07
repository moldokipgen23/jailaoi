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
        'user_name' => 'string',
        'full_name' => 'string',
        'country_code' => 'string',
        'mobile_number' => 'string',
        'country_name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'image' => 'string',
        'type' => 'integer',
        'device_type' => 'integer',
        'device_token' => 'string',
        'status' => 'integer',
        'role' => 'string',
        'bio' => 'string',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'email_blast_sent_at' => 'datetime',
    ];
}
