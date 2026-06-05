<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $product = new Product([
            'status' => 'draft',
            'product_type' => 'custom',
            'base_price' => 0,
            'sort_order' => 0,
        ]);

        return view('admin.products.create', compact('product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['base_price'] = $this->dollarsToCents($request->input('base_price_dollars'));
        $data['requires_customization'] = $request->boolean('requires_customization');
        $data['requires_upload'] = $request->boolean('requires_upload');
        $data['is_featured'] = $request->boolean('is_featured');

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request, $product);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['base_price'] = $this->dollarsToCents($request->input('base_price_dollars'));
        $data['requires_customization'] = $request->boolean('requires_customization');
        $data['requires_upload'] = $request->boolean('requires_upload');
        $data['is_featured'] = $request->boolean('is_featured');

        $product->update($data);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $productId = $product?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku,' . $productId],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price_dollars' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,active,archived'],
            'product_type' => ['required', 'in:physical,digital,service,bundle,custom'],
            'sort_order' => ['nullable', 'integer'],
            'primary_image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function dollarsToCents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }
}
