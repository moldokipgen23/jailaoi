<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badges_Bonus extends Model
{
    use HasFactory;

    protected $table = 'tbl_badges_bonus';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'type' => 'integer',
        'name' => 'string',
        'storage_type' => 'integer',
        'image' => 'string',
        'description' => 'string',
        'bonus_coin' => 'integer',
        'condition_type' => 'string',
        'x_number' => 'integer',
        'x_content' => 'integer',
        'status' => 'integer',
    ];
}
