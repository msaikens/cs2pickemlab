<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStatSnapshot extends Model
{
    protected $fillable = [
        'player_id',
        'team_id',
        'event_id',
        'event_stage_id',
        'source',
        'scope',
        'snapshot_date',
        'rating',
        'kd_ratio',
        'impact_rating',
        'adr',
        'kast',
        'kpr',
        'dpr',
        'headshot_percentage',
        'maps_played',
        'rounds_played',
        'source_payload',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'rating' => 'decimal:2',
        'kd_ratio' => 'decimal:2',
        'impact_rating' => 'decimal:2',
        'adr' => 'decimal:2',
        'kast' => 'decimal:2',
        'kpr' => 'decimal:2',
        'dpr' => 'decimal:2',
        'headshot_percentage' => 'decimal:2',
        'source_payload' => 'array',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function stage()
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }
}