<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'region',
        'country',
        'logo_path',
        'picklab_rating',
        'status',
        'summary',
        'notes',
    ];

    protected $casts = [
        'picklab_rating' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function matchesAsTeamOne(): HasMany
    {
        return $this->hasMany(Matches::class, 'team_one_id');
    }

    public function matchesAsTeamTwo(): HasMany
    {
        return $this->hasMany(Matches::class, 'team_two_id');
    }

    public function wonMatches(): HasMany
    {
        return $this->hasMany(Matches::class, 'winner_team_id');
    }

    public function pickemRecommendations(): HasMany
    {
        return $this->hasMany(PickemRecommendation::class);
    }

public function eventRosterPlayers()
{
    return $this->hasMany(EventRosterPlayer::class);
}

public function playerStatSnapshots()
{
    return $this->hasMany(PlayerStatSnapshot::class);
}

public function statSnapshots()
{
    return $this->hasMany(TeamStatSnapshot::class);
}

public function latestStatSnapshot()
{
    return $this->hasOne(TeamStatSnapshot::class)->latestOfMany('snapshot_date');
}
}
