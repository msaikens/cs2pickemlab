<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionValue extends Model
{
    protected $fillable = [
        'product_option_id',
        'label',
        'value',
        'price_delta',
        'sort_order',
    ];

    protected $casts = [
        'product_option_id' => 'integer',
        'price_delta' => 'integer',
        'sort_order' => 'integer',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function getPriceDeltaDollarsAttribute(): string
    {
        return number_format($this->price_delta / 100, 2);
    }
}
