<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'tbl_blog';
    protected $fillable = ['title', 'content', 'description', 'image', 'category', 'view', 'tags', 'created_by', 'status'];
}