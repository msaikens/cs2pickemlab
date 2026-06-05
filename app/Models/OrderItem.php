<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'sku',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer',
        'product_variant_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'line_total' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function customizations(): HasMany
    {
        return $this->hasMany(OrderItemCustomization::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(OrderUpload::class);
    }

    public function getUnitPriceDollarsAttribute(): string
    {
        return number_format($this->unit_price / 100, 2);
    }

    public function getLineTotalDollarsAttribute(): string
    {
        return number_format($this->line_total / 100, 2);
    }
}
