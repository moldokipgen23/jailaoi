<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interests extends Model
{
    use HasFactory;

    protected $table = 'tbl_interests';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'category_ids' => 'string',
        'hashtag_ids' => 'string',
    ];
}
