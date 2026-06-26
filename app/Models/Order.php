<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_DRAFT = 'draft';

    // Legacy only. Do not use for production status going forward.
    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_RECEIVED = 'received';
    public const STATUS_DESIGN_NEEDED = 'design_needed';
    public const STATUS_DESIGN_READY = 'design_ready';
    public const STATUS_PRINTING = 'printing';
    public const STATUS_QUALITY_CHECK = 'quality_check';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

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

        'shipping_name',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_instructions',
        'shipping_carrier',
        'tracking_number',

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
        'shipped_at',
        'completed_at',
        'cancelled_at',
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
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_RECEIVED,
            self::STATUS_DESIGN_NEEDED,
            self::STATUS_DESIGN_READY,
            self::STATUS_PRINTING,
            self::STATUS_QUALITY_CHECK,
            self::STATUS_SHIPPED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_UNPAID,
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_PAID,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_REFUNDED,
        ];
    }

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

    public function getShippingDollarsAttribute(): string
    {
        return number_format($this->shipping_amount / 100, 2);
    }

    public function getTaxDollarsAttribute(): string
    {
        return number_format($this->tax_amount / 100, 2);
    }

    public function getDiscountDollarsAttribute(): string
    {
        return number_format($this->discount_amount / 100, 2);
    }

    public function statusLabel(): string
    {
        if ($this->status === self::STATUS_PENDING_PAYMENT) {
            return 'Received';
        }

        return str($this->status)->replace('_', ' ')->title()->toString();
    }

    public function paymentStatusLabel(): string
    {
        return str($this->payment_status)->replace('_', ' ')->title()->toString();
    }

    public function shippingAddressLines(): array
    {
        return array_values(array_filter([
            $this->shipping_name,
            $this->shipping_address_line_1,
            $this->shipping_address_line_2,
            trim(collect([
                $this->shipping_city,
                $this->shipping_state,
                $this->shipping_postal_code,
            ])->filter()->implode(', ')),
            $this->shipping_country,
        ]));
    }
}