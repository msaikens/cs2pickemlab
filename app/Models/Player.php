<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{
    protected $fillable = [
        'team_id',
        'handle',
        'slug',
        'real_name',
        'country',
        'role',
        'photo_path',
        'rating',
        'kd_ratio',
        'impact_rating',
        'status',
        'summary',
        'notes',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'rating' => 'decimal:2',
        'kd_ratio' => 'decimal:2',
        'impact_rating' => 'decimal:2',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

public function eventRosters()
{
    return $this->hasMany(EventRosterPlayer::class);
}

public function statSnapshots()
{
    return $this->hasMany(PlayerStatSnapshot::class);
}

public function latestStatSnapshot()
{
    return $this->hasOne(PlayerStatSnapshot::class)->latestOfMany('snapshot_date');
}
}