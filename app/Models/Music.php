<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $table = 'tbl_music';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'artist_id' => 'string',
        'album_name' => 'string',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'is_premium' => 'integer',
        'duration' => 'integer',
        'upload_type' => 'integer',
        'music' => 'string',
        'description' => 'string',
        'portrait_img' => 'string',
        'landscape_img' => 'string',
        'ogtag_img' => 'string',
        'total_play' => 'integer',
        'status' => 'integer',
        'release_year' => 'integer',
        'release_date' => 'date',
        'tags' => 'string',
    ];

}
