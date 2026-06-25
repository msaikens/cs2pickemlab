<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInboxMessage extends Model
{
    public const TYPE_MODERATION = 'moderation';
    public const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'user_id',
        'moderation_incident_id',
        'type',
        'title',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderationIncident(): BelongsTo
    {
        return $this->belongsTo(ModerationIncident::class);
    }

    public function markRead(): void
    {
        if (! $this->read_at) {
            $this->forceFill([
                'read_at' => now(),
            ])->save();
        }
    }
}