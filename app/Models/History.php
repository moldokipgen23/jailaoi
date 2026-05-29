<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $table = 'tbl_history';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'episode_id' => 'integer',
        'stop_time' => 'integer',
        'status' => 'integer',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
