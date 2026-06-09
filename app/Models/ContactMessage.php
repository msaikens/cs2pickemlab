<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'is_spam',
        'spam_reason',
        'sent_at',
    ];

    protected $casts = [
        'is_spam' => 'boolean',
        'sent_at' => 'datetime',
    ];
}