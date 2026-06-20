<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $table = 'tbl_album';
    protected $guarded = [];

    protected $casts = [
        'id'                       => 'integer',
        'user_id'                  => 'integer',
        'channel_id'               => 'string',
        'name'                     => 'string',
        'description'              => 'string',
        'cover_image'              => 'string',
        'cover_image_storage_type' => 'integer',
        'status'                   => 'integer',
    ];
}
