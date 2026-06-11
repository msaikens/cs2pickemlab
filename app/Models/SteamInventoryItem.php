<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteamInventoryItem extends Model
{
    protected $fillable = [
        'user_id',
        'steam_id_64',
        'asset_id',
        'class_id',
        'instance_id',
        'app_id',
        'context_id',
        'market_hash_name',
        'name',
        'type',
        'rarity',
        'exterior',
        'icon_url',
        'image_url',
        'tradable',
        'marketable',
        'commodity',
        'raw_asset',
        'raw_description',
        'last_seen_at',
    ];

    protected $casts = [
        'tradable' => 'boolean',
        'marketable' => 'boolean',
        'commodity' => 'boolean',
        'raw_asset' => 'array',
        'raw_description' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayImageAttribute(): ?string
    {
        return $this->image_url;
    }
}