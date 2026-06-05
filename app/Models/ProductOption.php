<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'type',
        'is_required',
        'sort_order',
        'help_text',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }

    public function customizations(): HasMany
    {
        return $this->hasMany(OrderItemCustomization::class);
    }
}
