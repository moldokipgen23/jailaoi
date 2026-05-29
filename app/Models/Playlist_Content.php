<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist_Content extends Model
{
    use HasFactory;

    protected $table = 'tbl_playlist_content';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'string',
        'playlist_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];

    public function Content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
