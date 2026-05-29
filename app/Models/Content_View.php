<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content_View extends Model
{
    use HasFactory;

    protected $table = 'tbl_content_view';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'episode_id' => 'integer',
        'status' => 'integer',
    ];
}
