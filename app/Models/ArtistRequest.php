<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistRequest extends Model
{
    use HasFactory;

    protected $table = 'tbl_artist_requests';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'artist_name' => 'string',
        'bio' => 'string',
        'status' => 'string',
        'admin_note' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
