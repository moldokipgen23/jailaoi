<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Action extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_action';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'city_id' => 'integer',
        'artist_id' => 'string',
        'action' => 'integer',
        'time_spend' => 'integer',
        'content_duration' => 'integer',
        'status' => 'integer',
    ];
}
