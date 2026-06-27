<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketplaceRating extends Model
{
    protected $fillable = [
        'rater_user_id',
        'rated_user_id',
        'rateable_type',
        'rateable_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rater_user_id' => 'integer',
        'rated_user_id' => 'integer',
        'rating' => 'integer',
    ];

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_user_id');
    }

    public function ratedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }
}