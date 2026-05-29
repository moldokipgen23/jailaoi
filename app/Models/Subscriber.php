<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $table = 'tbl_subscriber';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'to_user_id' => 'integer',
        'status' => 'integer',
    ];

    public function to_user()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
