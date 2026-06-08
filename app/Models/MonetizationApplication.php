<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonetizationApplication extends Model
{
    use HasFactory;

    protected $table = 'tbl_monetization_applications';
    protected $guarded = [];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'snapshot_plays' => 'integer',
        'snapshot_followers' => 'integer',
        'snapshot_monthly_plays' => 'integer',
        'snapshot_tracks' => 'integer',
        'snapshot_earnings' => 'decimal:4',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
