<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed_Comment extends Model
{
    use HasFactory;

    protected $table = 'tbl_feed_comment';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'comment__id' => 'integer',
        'user_id' => 'integer',
        'post_id' => 'integer',
        'comment' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }
}
