<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin_Transaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_coin_transaction';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'package_id' => 'integer',
        'transaction_id' => 'string',
        'price' => 'integer',
        'coin' => 'integer',
        'description' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function package()
    {
        return $this->belongsTo(Coin_Package::class, 'package_id');
    }
}
