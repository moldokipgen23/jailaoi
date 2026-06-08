<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $table = 'tbl_withdrawal_requests';
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'artist_id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'float',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
