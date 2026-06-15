<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'available_balance_cents',
        'reserved_balance_cents',
        'pending_balance_cents',
    ];

    protected $casts = [
        'available_balance_cents' => 'integer',
        'reserved_balance_cents' => 'integer',
        'pending_balance_cents' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getAvailableBalanceDollarsAttribute(): string
    {
        return number_format($this->available_balance_cents / 100, 2);
    }

    public function getReservedBalanceDollarsAttribute(): string
    {
        return number_format($this->reserved_balance_cents / 100, 2);
    }

    public function getPendingBalanceDollarsAttribute(): string
    {
        return number_format($this->pending_balance_cents / 100, 2);
    }
}