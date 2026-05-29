<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live_User extends Model
{
    use HasFactory;

    protected $table = 'tbl_live_user';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'room_id' => 'string',
        'user_id' => 'integer',
        'total_view' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
