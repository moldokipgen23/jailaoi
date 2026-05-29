<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'tbl_page';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'storage_type' => 'integer',
        'icon' => 'string',
        'status' => 'integer',
    ];
}
