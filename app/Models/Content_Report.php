<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content_Report extends Model
{
    use HasFactory;

    protected $table = 'tbl_content_report';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'report_user_id' => 'integer',
        'content_type' => 'integer',
        'content_id' => 'integer',
        'episode_id' => 'integer',
        'message' => 'string',
        'status' => 'integer',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function report_user()
    {
        return $this->belongsTo(User::class, 'report_user_id');
    }
    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
