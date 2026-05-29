<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Notification_Tracking extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_notification_tracking';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'notification_id' => 'integer',
    ];
}
