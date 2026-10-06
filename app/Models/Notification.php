<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'tbl_notification';
    protected $guarded = array();
    protected $appends = ['description'];
    protected $attributes = ['type' => 0, 'image' => '', 'user_id' => 0, 'from_user_id' => 0, 'content_id' => 0, 'storage_type' => 0, 'status' => 1];

    public function getDescriptionAttribute(): string
    {
        return (string) ($this->attributes['message'] ?? '');
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['message'] = $value;
    }


    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'image' => 'string',
    ];
}
