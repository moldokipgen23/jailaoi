<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Badges_Bonus extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_badges_bonus';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'badges_bonus_id' => 'integer',
        'reward_coin' => 'integer',
        'status' => 'integer',
    ];

    public function badges_bonus()
    {
        return $this->belongsTo(Badges_Bonus::class, 'badges_bonus_id');
    }
}
