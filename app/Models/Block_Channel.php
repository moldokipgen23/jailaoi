<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block_Channel extends Model
{
    use HasFactory;

    protected $table = 'tbl_block_channel';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'block_user_id' => 'integer',
        'block_channel_id' => 'string',
        'status' => 'integer',
    ];
}
