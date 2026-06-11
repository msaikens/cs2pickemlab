<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteamAccount extends Model
{
    protected $fillable = [
        'user_id',
        'steam_id_64',
        'persona_name',
        'profile_url',
        'avatar_url',
        'profile_visibility',
        'linked_at',
        'last_verified_at',
    ];

    protected $casts = [
        'linked_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}