<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $fillable = [
        'match_id',
        'predicted_winner_team_id',
        'confidence_score',
        'upset_risk',
        'best_pickem_use',
        'status',
        'is_premium',
        'headline',
        'summary',
        'reasoning',
        'published_at',
    ];

    protected $casts = [
        'match_id' => 'integer',
        'predicted_winner_team_id' => 'integer',
        'confidence_score' => 'integer',
        'is_premium' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }

    public function predictedWinner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'predicted_winner_team_id');
    }
}
