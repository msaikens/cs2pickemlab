<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeRequestEvent extends Model
{
    protected $fillable = [
        'trade_request_id',
        'actor_user_id',
        'event_type',
        'old_status',
        'new_status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function tradeRequest(): BelongsTo
    {
        return $this->belongsTo(TradeRequest::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}