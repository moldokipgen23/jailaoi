<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Summary extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_summary';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'score_json' => 'string',
        'status' => 'integer',
    ];
}
