<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemCustomization extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_option_id',
        'label',
        'value',
        'price_delta',
    ];

    protected $casts = [
        'order_item_id' => 'integer',
        'product_option_id' => 'integer',
        'price_delta' => 'integer',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function getPriceDeltaDollarsAttribute(): string
    {
        return number_format($this->price_delta / 100, 2);
    }
}
