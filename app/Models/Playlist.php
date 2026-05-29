<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $table = 'tbl_playlist';
    protected $fillable = ['user_id', 'name', 'privacy', 'image', 'plays', 'status'];
}