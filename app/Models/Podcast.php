<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasFactory;

    protected $table = 'tbl_podcast';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'category_id' => 'integer',
        'language_id' => 'string',
        'portrait_img' => 'string',
        'landscape_img' => 'string',
        'description' => 'string',
        'is_premium' => 'integer',
        'total_play' => 'integer',
        'status' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
