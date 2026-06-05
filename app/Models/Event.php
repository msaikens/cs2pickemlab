<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'organizer',
        'location',
        'starts_on',
        'ends_on',
        'status',
        'has_pickem',
        'is_featured',
        'summary',
        'notes',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'has_pickem' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function stages(): HasMany
    {
        return $this->hasMany(EventStage::class)->orderBy('sort_order');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class);
    }

    public function pickemRecommendations(): HasMany
    {
        return $this->hasMany(PickemRecommendation::class);
    }
}
