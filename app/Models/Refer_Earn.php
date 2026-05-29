<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refer_Earn extends Model
{
    use HasFactory;

    protected $table = 'tbl_refer_earn';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'parent_user_id' => 'integer',
        'reference_code' => 'string',
        'child_user_id' => 'integer',
        'parent_earn' => 'integer',
        'child_earn' => 'integer',
        'status' => 'integer',
    ];

    public function parent_user()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
    public function child_user()
    {
        return $this->belongsTo(User::class, 'child_user_id');
    }
}
