<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $table = 'tbl_album';
    protected $guarded = array();

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function songs()
    {
        return $this->hasMany(Content::class, 'album_id', 'id')->where('content_type', 2)->where('status', 1);
    }
}
