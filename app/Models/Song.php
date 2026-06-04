<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $table = 'tbl_song';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'city_id' => 'integer',
        'artist_id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'upload_type' => 'integer',
        'song_url' => 'string',
        'duration' => 'integer',
        'is_premium' => 'integer',
        'total_play' => 'integer',
        'status' => 'integer',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }
}
