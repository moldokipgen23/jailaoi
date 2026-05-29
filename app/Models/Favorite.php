<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $table = 'tbl_favorite';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'type' => 'integer',
        'content_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class, 'content_id');
    }
    public function podcast()
    {
        return $this->belongsTo(Podcast::class, 'content_id');
    }
}
