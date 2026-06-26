<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

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

    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'CPL-' . now()->format('Ymd') . '-' . strtoupper(str()->random(8));
        } while (static::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

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