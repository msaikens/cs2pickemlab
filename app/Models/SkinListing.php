<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkinListing extends Model
{
    protected $fillable = [
        'user_id',
        'steam_asset_id',
        'market_hash_name',
        'supervisor_user_id',
        'supervisor_assigned_at',
        'supervisor_note',
        'item_name',
        'weapon_type',
        'rarity',
        'wear_name',
        'float_value',
        'image_url',
        'listing_type',
        'asking_price_cents',
        'currency',
        'status',
    ];

    protected $casts = [
        'float_value' => 'decimal:8',
        'asking_price_cents' => 'integer',
        'supervisor_assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradeRequests(): HasMany
    {
        return $this->hasMany(TradeRequest::class);
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->asking_price_cents === null) {
            return 'Trade only';
        }

        return '$' . number_format($this->asking_price_cents / 100, 2);
    }
    
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

}