<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'tbl_section';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'short_title' => 'string',
        'is_home_screen' => 'integer',
        'content_type' => 'integer',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'order_by_view' => 'integer',
        'order_by_like' => 'integer',
        'order_by_upload' => 'integer',
        'screen_layout' => 'string',
        'no_of_content' => 'integer',
        'view_all' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer',
        'is_fixed' => 'integer',
    ];
}
