<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Send_Gift extends Model
{
    use HasFactory;

    protected $table = 'tbl_send_gift';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'gift_id' => 'integer',
        'user_id' => 'integer',
        'channel_id' => 'integer',
        'content_id' => 'integer',
        'price' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function channel()
    {
        return $this->belongsTo(User::class, 'channel_id');
    }
    public function gift()
    {
        return $this->belongsTo(Gift::class, 'gift_id');
    }
    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
