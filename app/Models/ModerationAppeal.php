<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationAppeal extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DENIED = 'denied';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'moderation_incident_id',
        'user_id',
        'status',
        'message',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(ModerationIncident::class, 'moderation_incident_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}