<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRosterPlayer extends Model
{
    protected $fillable = [
        'event_id',
        'event_stage_id',
        'team_id',
        'player_id',
        'role',
        'is_starter',
        'is_active',
        'source_payload',
        'locked_at',
    ];

    protected $casts = [
        'is_starter' => 'boolean',
        'is_active' => 'boolean',
        'source_payload' => 'array',
        'locked_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function stage()
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}