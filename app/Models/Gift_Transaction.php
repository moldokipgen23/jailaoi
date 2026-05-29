<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift_Transaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_gift_transaction';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'gift_id' => 'integer',
        'coin' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function gift()
    {
        return $this->belongsTo(Gift::class, 'gift_id');
    }
}
