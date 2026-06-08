<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistKyc extends Model
{
    use HasFactory;

    protected $table = 'tbl_artist_kyc';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'artist_id' => 'integer',
        'user_id' => 'integer',
        'date_of_birth' => 'date',
        'payment_details' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
