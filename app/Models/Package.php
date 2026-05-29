<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'tbl_package';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'price' => 'string',
        'time' => 'string',
        'type' => 'string',
        'android_product_package' => 'string',
        'ios_product_package' => 'string',
        'web_product_package' => 'string',
        'status' => 'integer',
    ];
}
