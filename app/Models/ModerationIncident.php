<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModerationIncident extends Model
{
    public const ACTION_WARNING = 'warning';
    public const ACTION_SUSPENSION = 'suspension';
    public const ACTION_BAN = 'ban';
    public const ACTION_LISTINGS_REMOVED = 'listings_removed';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REVERSED = 'reversed';
    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'incident_number',
        'subject_user_id',
        'admin_user_id',
        'action_type',
        'status',
        'title',
        'user_message',
        'admin_note',
        'starts_at',
        'ends_at',
        'resolved_at',
        'listings_removed_count',
        'reversed_at',
        'reversed_by_user_id',
        'reversal_reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'resolved_at' => 'datetime',
        'reversed_at' => 'datetime'
    ];

    public function subjectUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public static function generateIncidentNumber(): string
    {
        do {
            $number = 'MOD-' . now()->format('Ymd') . '-' . strtoupper(str()->random(8));
        } while (self::where('incident_number', $number)->exists());

        return $number;
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(ModerationAppeal::class);
    }

    public function reversedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by_user_id');
    }

    public function isReversed(): bool
    {
        return $this->status === self::STATUS_REVERSED || ! is_null($this->reversed_at);
    }

    public function canBeAppealed(): bool
    {
        return ! $this->isReversed()
            && in_array($this->action_type, [
                self::ACTION_WARNING,
                self::ACTION_SUSPENSION,
                self::ACTION_BAN,
                self::ACTION_LISTINGS_REMOVED,
            ], true);
    }
}