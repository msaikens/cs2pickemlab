<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamStatSnapshot extends Model
{
    protected $fillable = [
        'team_id',
        'event_id',
        'event_stage_id',
        'source',
        'scope',
        'snapshot_date',
        'matches_played',
        'maps_played',
        'match_win_rate',
        'map_win_rate',
        'round_win_rate',
        'ct_round_win_rate',
        't_round_win_rate',
        'pistol_win_rate',
        'average_player_rating',
        'average_adr',
        'form_score',
        'map_pool',
        'source_payload',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'match_win_rate' => 'decimal:2',
        'map_win_rate' => 'decimal:2',
        'round_win_rate' => 'decimal:2',
        'ct_round_win_rate' => 'decimal:2',
        't_round_win_rate' => 'decimal:2',
        'pistol_win_rate' => 'decimal:2',
        'average_player_rating' => 'decimal:2',
        'average_adr' => 'decimal:2',
        'form_score' => 'decimal:2',
        'map_pool' => 'array',
        'source_payload' => 'array',
    ];

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