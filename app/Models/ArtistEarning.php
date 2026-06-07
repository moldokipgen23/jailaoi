<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistEarning extends Model
{
    use HasFactory;

    protected $table = 'tbl_artist_earnings';
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'artist_id' => 'integer',
        'user_id' => 'integer',
        'content_id' => 'integer',
        'content_type' => 'integer',
        'amount' => 'float',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }
}
