<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast_Section extends Model
{
    use HasFactory;


    protected $table = 'tbl_podcast_section';
    
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'sub_title' => 'string',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'screen_layout' => 'string',
        'is_premium' => 'integer',
        'order_by_upload' => 'integer',
        'order_by_play' => 'integer',
        'no_of_content' => 'integer',
        'view_all' => 'integer',
        'sortable' => 'integer',
        'status' => 'integer',
    ];
}
