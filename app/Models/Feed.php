<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $table = 'tbl_feed';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'string',
        'hashtag_id' => 'string',
        'description' => 'string',
        'is_like' => 'integer',
        'is_comment' => 'integer',
        'total_like' => 'integer',
        'status' => 'integer',
    ];

    public function channel()
    {
        return $this->belongsTo(User::class, 'channel_id', 'channel_id');
    }
}
