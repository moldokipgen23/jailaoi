<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $table = 'tbl_gift';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'storage_type' => 'integer',
        'image' => 'string',
        'price' => 'integer',
        'status' => 'integer',
    ];
}
