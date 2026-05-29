<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'tbl_language';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'storage_type' => 'integer',
        'image' => 'string',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];
}
