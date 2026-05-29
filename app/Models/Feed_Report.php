<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed_Report extends Model
{
    use HasFactory;

    protected $table = 'tbl_feed_report';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'report_user_id' => 'integer',
        'feed_id' => 'integer',
        'message' => 'string',
        'status' => 'integer',
    ];

    public function report_user()
    {
        return $this->belongsTo(User::class, 'report_user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }
}
