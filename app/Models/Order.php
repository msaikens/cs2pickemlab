<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'payment_status',
        'subtotal',
        'shipping_amount',
        'tax_amount',
        'discount_amount',
        'total',
        'currency',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'subtotal' => 'integer',
        'shipping_amount' => 'integer',
        'tax_amount' => 'integer',
        'discount_amount' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(OrderUpload::class);
    }

    public function getTotalDollarsAttribute(): string
    {
        return number_format($this->total / 100, 2);
    }

    public function getSubtotalDollarsAttribute(): string
    {
        return number_format($this->subtotal / 100, 2);
    }
}
