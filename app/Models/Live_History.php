<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live_History extends Model
{
    use HasFactory;

    protected $table = 'tbl_live_history';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'room_id' => 'string',
        'user_id' => 'integer',
        'total_gift' => 'integer',
        'total_join_user' => 'integer',
        'total_live_chat' => 'integer',
        'start_time' => 'string',
        'end_time' => 'string',
        'duration' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
