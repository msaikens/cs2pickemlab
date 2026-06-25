<?php

// app/Models/TradeRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TradeRequestEvent;

class TradeRequest extends Model
{
    protected $fillable = [
        'skin_listing_id',
        'buyer_user_id',
        'seller_user_id',
        'message',
        'status',
        'accepted_at',
        'declined_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(SkinListing::class, 'skin_listing_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function events(): HasMany
    {
    return $this->hasMany(TradeRequestEvent::class)->latest();
    }
}