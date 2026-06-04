<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event_Join_User extends Model
{
    use HasFactory;

    protected $table = 'tbl_event_join_user';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'live_event_id' => 'integer',
        'type' => 'integer',
        'transaction_id' => 'string',
        'price' => 'integer',
        'description' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function live_event()
    {
        return $this->belongsTo(Live_Event::class, 'live_event_id');
    }
}
