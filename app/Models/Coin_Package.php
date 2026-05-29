<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin_Package extends Model
{
    use HasFactory;

    protected $table = 'tbl_coin_package';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'storage_type' => 'integer',
        'image' => 'string',
        'price' => 'integer',
        'coin' => 'integer',
        'android_product_package' => 'string',
        'ios_product_package' => 'string',
        'web_product_package' => 'string',
        'status' => 'integer',
    ];
}
