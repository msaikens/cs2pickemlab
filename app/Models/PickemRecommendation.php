<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickemRecommendation extends Model
{
    protected $fillable = [
        'event_id',
        'event_stage_id',
        'team_id',
        'slot_type',
        'risk_level',
        'confidence_score',
        'status',
        'is_premium',
        'sort_order',
        'headline',
        'summary',
        'reasoning',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'event_stage_id' => 'integer',
        'team_id' => 'integer',
        'confidence_score' => 'integer',
        'is_premium' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(EventStage::class, 'event_stage_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
