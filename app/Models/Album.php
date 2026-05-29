<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $table = 'tbl_album';
    protected $fillable = ['user_id', 'title', 'description', 'category_id', 'image', 'price', 'status'];
}