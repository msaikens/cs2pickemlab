<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ShopCartService
{
    private const SESSION_KEY = 'shop.cart.items';

    public function items(): Collection
    {
        $rawItems = collect(session(self::SESSION_KEY, []));

        if ($rawItems->isEmpty()) {
            return collect();
        }

        $productIds = $rawItems
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $products = Product::query()
            ->with(['options.values', 'activeVariants'])
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $cleaned = [];

        $items = $rawItems
            ->map(function (array $item, string|int $key) use ($products, &$cleaned) {
                $product = $products->get($item['product_id'] ?? null);

                if (! $product) {
                    return null;
                }

                $quantity = max(1, min(99, (int) ($item['quantity'] ?? 1)));
                $selectedOptionsInput = $item['selected_options_input'] ?? [];
                $variantId = $item['variant_id'] ?? null;

                $variant = $this->resolveVariant(
                    product: $product,
                    variantId: $variantId,
                    enforceRequired: false,
                );

                $selectedOptions = $this->normalizeSelectedOptions(
                    product: $product,
                    selectedOptionsInput: $selectedOptionsInput,
                    enforceRequired: false,
                );

                $baseUnitPrice = $variant
                    ? (int) $variant->price
                    : (int) $product->base_price;

                $optionDeltaTotal = collect($selectedOptions)->sum('price_delta');

                $unitPrice = $baseUnitPrice + $optionDeltaTotal;
                $lineTotal = $unitPrice * $quantity;

                $cartItem = [
                    'key' => (string) $key,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sku' => $variant?->sku ?: $product->sku,
                    'variant_name' => $variant?->name,
                    'base_price' => (int) $product->base_price,
                    'variant_price' => $variant ? (int) $variant->price : null,
                    'option_delta_total' => $optionDeltaTotal,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                    'primary_image_path' => $product->primary_image_path,
                    'requires_customization' => (bool) $product->requires_customization,
                    'requires_upload' => (bool) $product->requires_upload,
                    'customization_notes' => $item['customization_notes'] ?? null,
                    'selected_options' => $selectedOptions,
                    'selected_options_input' => $selectedOptionsInput,
                ];

                $cleaned[(string) $key] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $quantity,
                    'customization_notes' => $cartItem['customization_notes'],
                    'selected_options_input' => $selectedOptionsInput,
                ];

                return $cartItem;
            })
            ->filter()
            ->values();

        session([self::SESSION_KEY => $cleaned]);

        return $items;
    }

    public function add(
        Product $product,
        int $quantity = 1,
        ?string $customizationNotes = null,
        array $selectedOptionsInput = [],
        ?int $variantId = null,
    ): void {
        if ($product->status !== 'active') {
            throw ValidationException::withMessages([
                'product' => 'This product is not currently available.',
            ]);
        }

        $product->loadMissing(['options.values', 'activeVariants']);

        $variant = $this->resolveVariant(
            product: $product,
            variantId: $variantId,
            enforceRequired: true,
        );

        $quantity = max(1, min(99, $quantity));

        $this->normalizeSelectedOptions(
            product: $product,
            selectedOptionsInput: $selectedOptionsInput,
            enforceRequired: true,
        );

        $items = session(self::SESSION_KEY, []);

        $key = $this->makeKey(
            product: $product,
            selectedOptionsInput: $selectedOptionsInput,
            customizationNotes: $customizationNotes,
            variantId: $variant?->id,
        );

        $existingQuantity = (int) ($items[$key]['quantity'] ?? 0);

        $items[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'quantity' => min(99, $existingQuantity + $quantity),
            'customization_notes' => $customizationNotes,
            'selected_options_input' => $selectedOptionsInput,
        ];

        session([self::SESSION_KEY => $items]);
    }

    public function update(string $key, int $quantity, ?string $customizationNotes = null): void
    {
        $items = session(self::SESSION_KEY, []);

        if (! array_key_exists($key, $items)) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($key);
            return;
        }

        $items[$key]['quantity'] = max(1, min(99, $quantity));
        $items[$key]['customization_notes'] = $customizationNotes;

        session([self::SESSION_KEY => $items]);
    }

    public function remove(string $key): void
    {
        $items = session(self::SESSION_KEY, []);

        unset($items[$key]);

        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function subtotal(): int
    {
        return $this->items()->sum('line_total');
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function hasItems(): bool
    {
        return $this->items()->isNotEmpty();
    }

    public function subtotalDollars(): string
    {
        return number_format($this->subtotal() / 100, 2);
    }

    private function resolveVariant(
        Product $product,
        ?int $variantId,
        bool $enforceRequired = true,
    ): ?ProductVariant {
        if ($product->activeVariants->isEmpty()) {
            return null;
        }

        if (! $variantId && $enforceRequired) {
            throw ValidationException::withMessages([
                'variant_id' => 'Please choose a product option.',
            ]);
        }

        if (! $variantId) {
            return null;
        }

        $variant = $product->activeVariants->firstWhere('id', $variantId);

        if (! $variant) {
            throw ValidationException::withMessages([
                'variant_id' => 'The selected product option is not available.',
            ]);
        }

        if ($variant->inventory_quantity !== null && $variant->inventory_quantity <= 0) {
            throw ValidationException::withMessages([
                'variant_id' => 'The selected product option is out of stock.',
            ]);
        }

        return $variant;
    }

    private function normalizeSelectedOptions(
        Product $product,
        array $selectedOptionsInput,
        bool $enforceRequired = true,
    ): array {
        $selected = [];

        foreach ($product->options as $option) {
            $optionId = (string) $option->id;
            $rawValue = $selectedOptionsInput[$optionId] ?? $selectedOptionsInput[$option->id] ?? null;

            if (is_string($rawValue)) {
                $rawValue = trim($rawValue);
            }

            if ($option->type === 'file') {
                continue;
            }

            if ($option->is_required && $enforceRequired && blank($rawValue)) {
                throw ValidationException::withMessages([
                    "options.{$option->id}" => "{$option->name} is required.",
                ]);
            }

            if (blank($rawValue)) {
                continue;
            }

            if (in_array($option->type, ['select', 'radio'], true)) {
                $value = $option->values
                    ->firstWhere('id', (int) $rawValue);

                if (! $value) {
                    throw ValidationException::withMessages([
                        "options.{$option->id}" => "Invalid selection for {$option->name}.",
                    ]);
                }

                $selected[] = [
                    'option_id' => $option->id,
                    'option_name' => $option->name,
                    'type' => $option->type,
                    'value_id' => $value->id,
                    'value_label' => $value->label,
                    'value_text' => null,
                    'price_delta' => (int) $value->price_delta,
                ];

                continue;
            }

            $selected[] = [
                'option_id' => $option->id,
                'option_name' => $option->name,
                'type' => $option->type,
                'value_id' => null,
                'value_label' => null,
                'value_text' => (string) $rawValue,
                'price_delta' => 0,
            ];
        }

        return $selected;
    }

    private function makeKey(
        Product $product,
        array $selectedOptionsInput = [],
        ?string $customizationNotes = null,
        ?int $variantId = null,
    ): string {
        ksort($selectedOptionsInput);

        return 'product_' . $product->id . '_' . md5(json_encode([
            'variant_id' => $variantId,
            'options' => $selectedOptionsInput,
            'customization_notes' => $customizationNotes,
        ]));
    }
}