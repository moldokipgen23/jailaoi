<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'tbl_content';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'content_type' => 'integer',
        'channel_id' => 'string',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'album_id' => 'integer',
        'hashtag_id' => 'string',
        'title' => 'string',
        'description' => 'string',
        'lyrics' => 'string',
        'portrait_img_storage_type' => 'integer',
        'portrait_img' => 'string',
        'landscape_img_storage_type' => 'integer',
        'landscape_img' => 'string',
        'content_storage_type' => 'integer',
        'content_upload_type' => 'string',
        'content' => 'string',
        'waveform_data' => 'string',
        'content_duration' => 'integer',
        'is_rent' => 'integer',
        'rent_price' => 'integer',
        'rent_day' => 'integer',
        'is_comment' => 'integer',
        'is_download' => 'integer',
        'is_like' => 'integer',
        'total_view' => 'integer',
        'total_like' => 'integer',
        'total_dislike' => 'integer',
        'playlist_type' => 'integer',
        'status' => 'integer',
    ];

    public function channel()
    {
        return $this->belongsTo(User::class, 'channel_id', 'channel_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}
