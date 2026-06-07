<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayError extends Model
{
    use HasFactory;

    protected $table = 'tbl_play_error';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'content_id' => 'integer',
        'content_type' => 'integer',
        'http_status' => 'integer',
    ];
}
