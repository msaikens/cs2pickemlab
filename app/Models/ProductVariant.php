<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'inventory_quantity',
        'is_active',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'price' => 'integer',
        'inventory_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceDollarsAttribute(): string
    {
        return number_format($this->price / 100, 2);
    }
}
