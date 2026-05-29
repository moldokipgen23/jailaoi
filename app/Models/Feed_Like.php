<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed_Like extends Model
{
    use HasFactory;

    protected $table = 'tbl_feed_like';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'feed_id' => 'integer',
        'status' => 'integer',
    ];
}
