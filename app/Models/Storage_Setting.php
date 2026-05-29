<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage_Setting extends Model
{
    use HasFactory;

    protected $table = 'tbl_storage_setting';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'storage_type' => 'string',
        's3_access_key' => 'string',
        's3_secret_key' => 'string',
        's3_region' => 'string',
        's3_bucket_name' => 'string',
        's3_endpoint' => 'string',
        'status' => 'integer',
    ];
}
