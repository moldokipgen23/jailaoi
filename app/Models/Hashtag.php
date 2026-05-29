<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $table = 'tbl_hashtag';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'total_used' => 'integer',
        'status' => 'integer',
    ];
}
