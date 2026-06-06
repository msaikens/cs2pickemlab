<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentGate extends Model
{
    protected $fillable = [
        'gate_key',
        'label',
        'description',
        'is_enabled',
        'requires_login',
        'requires_subscription',
        'locked_message',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'requires_login' => 'boolean',
        'requires_subscription' => 'boolean',
    ];
}
