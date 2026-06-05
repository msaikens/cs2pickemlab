<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $product->load(['variants' => fn ($query) => $query->orderBy('name')]);

        return view('admin.products.variants.index', compact('product'));
    }

    public function create(Product $product): View
    {
        $variant = new ProductVariant([
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->base_price,
            'inventory_quantity' => null,
            'is_active' => true,
        ]);

        return view('admin.products.variants.create', compact('product', 'variant'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['product_id'] = $product->id;
        $data['price'] = $this->dollarsToCents($request->input('price_dollars'));
        $data['is_active'] = $request->boolean('is_active');

        ProductVariant::create($data);

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Product variant created.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $data = $this->validatedData($request, $variant);

        $data['price'] = $this->dollarsToCents($request->input('price_dollars'));
        $data['is_active'] = $request->boolean('is_active');

        $variant->update($data);

        return redirect()
            ->route('admin.products.variants.edit', [$product, $variant])
            ->with('success', 'Product variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->delete();

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Product variant deleted.');
    }

    private function validatedData(Request $request, ?ProductVariant $variant = null): array
    {
        $variantId = $variant?->id ?? 'NULL';

        return $request->validate([
            'sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku,' . $variantId],
            'name' => ['required', 'string', 'max:255'],
            'price_dollars' => ['required', 'numeric', 'min:0'],
            'inventory_quantity' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function dollarsToCents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }

    private function ensureVariantBelongsToProduct(Product $product, ProductVariant $variant): void
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);
    }
}
