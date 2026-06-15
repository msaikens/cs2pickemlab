<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'direction',
        'amount_cents',
        'currency',
        'balance_bucket',
        'available_balance_after_cents',
        'reserved_balance_after_cents',
        'pending_balance_after_cents',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'reference_type',
        'reference_id',
        'status',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'available_balance_after_cents' => 'integer',
        'reserved_balance_after_cents' => 'integer',
        'pending_balance_after_cents' => 'integer',
        'metadata' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}