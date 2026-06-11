<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteamTradeProfile extends Model
{
    protected $fillable = [
        'user_id',
        'steam_trade_url',
        'trade_partner_id',
        'trade_token',
        'inventory_public',
        'last_inventory_sync_at',
        'trade_hold_warning_acknowledged_at',
    ];

    protected $casts = [
        'inventory_public' => 'boolean',
        'last_inventory_sync_at' => 'datetime',
        'trade_hold_warning_acknowledged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}