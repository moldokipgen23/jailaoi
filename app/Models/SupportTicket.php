<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'tbl_support_ticket';

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'status',
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'type' => 'string',
        'subject' => 'string',
        'message' => 'string',
        'status' => 'string',
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(SupportReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }
}
