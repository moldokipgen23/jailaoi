<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live_Event extends Model
{
    use HasFactory;

    protected $table = 'tbl_live_event';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'portrait_img' => 'string',
        'landscape_img' => 'string',
        'date' => 'string',
        'start_time' => 'string',
        'end_time' => 'string',
        'is_paid' => 'integer',
        'price' => 'integer',
        'type' => 'integer',
        'link' => 'string',
        'description' => 'string',
        'status' => 'integer',
    ];
}
