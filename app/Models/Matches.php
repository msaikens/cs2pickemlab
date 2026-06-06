<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Matches extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'event_id',
        'event_stage_id',
        'team_one_id',
        'team_two_id',
        'winner_team_id',
        'starts_at',
        'status',
        'format',
        'bracket_group',
        'round_label',
        'bracket_position',
        'team_one_score',
        'team_two_score',
        'summary',
        'notes',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'event_stage_id' => 'integer',
        'team_one_id' => 'integer',
        'team_two_id' => 'integer',
        'winner_team_id' => 'integer',
        'starts_at' => 'datetime',
        'bracket_position' => 'integer',
        'team_one_score' => 'integer',
        'team_two_score' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }

    public function teamOne(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_one_id');
    }

    public function teamTwo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_two_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function prediction(): HasOne
    {
        return $this->hasOne(Prediction::class, 'match_id');
    }
}
