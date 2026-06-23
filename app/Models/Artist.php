<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $table = 'tbl_artist';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'bio' => 'string',
        'type' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer',
        'user_id' => 'integer',
        'wallet_balance' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // JAILAOI: Link to the original registration request
    public function artistRequest()
    {
        return $this->hasOne(\App\Models\ArtistRequest::class, 'user_id', 'user_id');
    }
}
