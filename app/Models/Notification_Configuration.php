<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification_Configuration extends Model
{
    use HasFactory;

    protected $table = 'tbl_notification_configuration';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'type' => 'string',
        'send_notification' => 'integer',
        'send_mail' => 'integer',
        'status' => 'integer',
    ];
}
