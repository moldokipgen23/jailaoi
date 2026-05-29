<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $table = 'tbl_report_reason';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'reason' => 'string',
        'status' => 'integer',
    ];
}
