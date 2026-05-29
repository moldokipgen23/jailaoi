<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent_Section extends Model
{
    use HasFactory;

    protected $table = 'tbl_rent_section';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'category_id' => 'integer',
        'no_of_content' => 'integer',
        'view_all' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
