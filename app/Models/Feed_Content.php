<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed_Content extends Model
{
    use HasFactory;

    protected $table = 'tbl_feed_content';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'feed_id' => 'integer',
        'content_type' => 'integer',
        'image_storage_type' => 'integer',
        'image' => 'string',
        'video_storage_type' => 'integer',
        'video' => 'string',
        'status' => 'integer',
    ];
}
