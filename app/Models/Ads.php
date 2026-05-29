<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;

    protected $table = 'tbl_ads';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'title' => 'string',
        'redirect_url' => 'string',
        'type' => 'integer',
        'image_storage_type' => 'integer',
        'image' => 'string',
        'video_storage_type' => 'integer',
        'video' => 'string',
        'budget' => 'integer',
        'status' => 'integer',
        'is_hide' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
