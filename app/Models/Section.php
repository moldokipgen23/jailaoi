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
        'user_id' => 'integer',
        'section_type' => 'integer',
        'title' => 'string',
        'sub_title' => 'string',
        'type' => 'integer',
        'artist_id' => 'integer',
        'category_id' => 'integer',
        'language_id' => 'integer',
        'city_id' => 'integer',
        'screen_layout' => 'string',
        'is_premium' => 'integer',
        'order_by_upload' => 'integer',
        'order_by_play' => 'integer',
        'is_paid' => 'integer',
        'is_title' => 'integer',
        'is_category' => 'integer',
        'is_artist_name' => 'integer',
        'no_of_content' => 'integer',
        'view_all' => 'integer',
        'sortable' => 'integer',
        'status' => 'integer',
        'is_pinned' => 'integer',
    ];
}
