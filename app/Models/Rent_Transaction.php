<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent_Transaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_rent_transaction';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'content_id' => 'integer',
        'transaction_id' => 'string',
        'price' => 'string',
        'admin_commission' => 'string',
        'user_wallet_amount' => 'string',
        'description' => 'string',
        'expiry_date' => 'string',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
