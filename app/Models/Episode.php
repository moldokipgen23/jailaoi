<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $table = 'tbl_episode';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'podcasts_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'portrait_img' => 'string',
        'landscape_img' => 'string',
        'episode_upload_type' => 'string',
        'episode_audio' => 'string',
        'duration' => 'integer',
        'total_play' => 'integer',
        'sortable' => 'integer',
        'status' => 'integer',
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class, 'podcasts_id', 'id');
    }
}
