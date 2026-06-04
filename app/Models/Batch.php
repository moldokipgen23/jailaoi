<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'tbl_batch';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'input_file_id' => 'string',
        'batch_id' => 'string',
        'output_file_id' => 'string',
        'error_file_id' => 'string',
        'status' => 'string',
    ];
}
