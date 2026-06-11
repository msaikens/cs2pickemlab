<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceFee extends Model
{
    protected $fillable = [
        'skin_listing_id',
        'trade_request_id',
        'fee_type',
        'rate_basis_points',
        'fixed_fee_cents',
        'calculated_fee_cents',
        'currency',
    ];

    protected $casts = [
        'rate_basis_points' => 'integer',
        'fixed_fee_cents' => 'integer',
        'calculated_fee_cents' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(SkinListing::class, 'skin_listing_id');
    }

    public function tradeRequest(): BelongsTo
    {
        return $this->belongsTo(TradeRequest::class);
    }
}