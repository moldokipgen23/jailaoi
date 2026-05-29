<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'tbl_comment';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'comment_id' => 'integer',
        'user_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'episode_id' => 'integer',
        'comment' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
