<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $fillable = [
        'match_id',
        'source',
        'model_name',
        'model_version',
        'predicted_winner_team_id',
        'confidence_score',
        'team_one_win_probability',
        'team_two_win_probability',
        'upset_risk',
        'prediction_label',
        'best_pickem_use',
        'status',
        'is_premium',
        'headline',
        'summary',
        'reasoning',
        'factors',
        'input_snapshot',
        'published_at',
        'generated_at',
        'stale_at',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'factors' => 'array',
        'input_snapshot' => 'array',
        'published_at' => 'datetime',
        'generated_at' => 'datetime',
        'stale_at' => 'datetime',
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
