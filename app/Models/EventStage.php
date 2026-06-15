<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventStage extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'starts_on',
        'ends_on',
        'format',
        'has_pickem',
        'sort_order',
        'summary',
        'notes',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'has_pickem' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class);
    }

    public function pickemRecommendations(): HasMany
    {
        return $this->hasMany(PickemRecommendation::class);
    }

public function rosterPlayers()
{
    return $this->hasMany(EventRosterPlayer::class);
}

public function teamStatSnapshots()
{
    return $this->hasMany(TeamStatSnapshot::class);
}

public function playerStatSnapshots()
{
    return $this->hasMany(PlayerStatSnapshot::class);
}
}
