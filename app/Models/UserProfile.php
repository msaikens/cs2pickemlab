<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'first_name',
        'last_name',
        'bio',
        'avatar_url',
        'steam_name',
        'steam_id',
        'faceit_name',
        'discord_name',
        'twitch_name',
        'youtube_name',
        'country',
        'timezone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
