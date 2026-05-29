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
        'status' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_id');
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'artist_id');
    }

    public function followerCount()
    {
        return $this->followers()->count();
    }
}
