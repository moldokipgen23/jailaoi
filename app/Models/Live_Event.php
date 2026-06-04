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

    public function join_users()
    {
        return $this->hasMany(Event_Join_User::class, 'live_event_id');
    }
}
