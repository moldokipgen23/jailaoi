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
        'type' => 'integer',
        'user_id' => 'integer',
        'content_id' => 'integer',
        'episode_id' => 'integer',
        'comment' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function song()
    {
        return $this->belongsTo(Song::class, 'content_id');
    }
    public function podcasts()
    {
        return $this->belongsTo(Podcast::class, 'content_id');
    }
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
