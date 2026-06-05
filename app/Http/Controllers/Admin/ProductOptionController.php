<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductOptionController extends Controller
{
    public function index(Product $product): View
    {
        $product->load(['options.values']);

        return view('admin.products.options.index', compact('product'));
    }

    public function create(Product $product): View
    {
        $option = new ProductOption([
            'product_id' => $product->id,
            'type' => 'select',
            'is_required' => false,
            'sort_order' => $product->options()->count() + 1,
        ]);

        return view('admin.products.options.create', compact('product', 'option'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedOptionData($request, $product);

        $data['product_id'] = $product->id;
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_required'] = $request->boolean('is_required');

        $option = ProductOption::create($data);

        $this->syncValues($request, $option);

        return redirect()
            ->route('admin.products.options.index', $product)
            ->with('success', 'Product option created.');
    }

    public function edit(Product $product, ProductOption $option): View
    {
        $this->ensureOptionBelongsToProduct($product, $option);

        $option->load('values');

        return view('admin.products.options.edit', compact('product', 'option'));
    }

    public function update(Request $request, Product $product, ProductOption $option): RedirectResponse
    {
        $this->ensureOptionBelongsToProduct($product, $option);

        $data = $this->validatedOptionData($request, $product, $option);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_required'] = $request->boolean('is_required');

        $option->update($data);

        $this->syncValues($request, $option);

        return redirect()
            ->route('admin.products.options.edit', [$product, $option])
            ->with('success', 'Product option updated.');
    }

    public function destroy(Product $product, ProductOption $option): RedirectResponse
    {
        $this->ensureOptionBelongsToProduct($product, $option);

        $option->delete();

        return redirect()
            ->route('admin.products.options.index', $product)
            ->with('success', 'Product option deleted.');
    }

    private function validatedOptionData(Request $request, Product $product, ?ProductOption $option = null): array
    {
        $optionId = $option?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:product_options,slug,' . $optionId . ',id,product_id,' . $product->id,
            ],
            'type' => ['required', 'in:select,radio,checkbox,text,textarea,file,number,date'],
            'sort_order' => ['nullable', 'integer'],
            'help_text' => ['nullable', 'string', 'max:255'],

            'values' => ['nullable', 'array'],
            'values.*.id' => ['nullable', 'integer', 'exists:product_option_values,id'],
            'values.*.label' => ['nullable', 'string', 'max:255'],
            'values.*.value' => ['nullable', 'string', 'max:255'],
            'values.*.price_delta_dollars' => ['nullable', 'numeric'],
            'values.*.sort_order' => ['nullable', 'integer'],
            'delete_values' => ['nullable', 'array'],
            'delete_values.*' => ['integer', 'exists:product_option_values,id'],
        ]);
    }

    private function syncValues(Request $request, ProductOption $option): void
    {
        $deleteIds = collect($request->input('delete_values', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! empty($deleteIds)) {
            ProductOptionValue::query()
                ->where('product_option_id', $option->id)
                ->whereIn('id', $deleteIds)
                ->delete();
        }

        $values = $request->input('values', []);

        foreach ($values as $valueData) {
            $label = trim((string) ($valueData['label'] ?? ''));
            $value = trim((string) ($valueData['value'] ?? ''));

            if ($label === '' && $value === '') {
                continue;
            }

            if ($value === '') {
                $value = Str::slug($label);
            }

            $payload = [
                'label' => $label,
                'value' => $value,
                'price_delta' => $this->dollarsToCents($valueData['price_delta_dollars'] ?? 0),
                'sort_order' => (int) ($valueData['sort_order'] ?? 0),
            ];

            $existingId = $valueData['id'] ?? null;

            if ($existingId) {
                ProductOptionValue::query()
                    ->where('product_option_id', $option->id)
                    ->where('id', $existingId)
                    ->update($payload);
            } else {
                $option->values()->create($payload);
            }
        }
    }

    private function dollarsToCents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }

    private function ensureOptionBelongsToProduct(Product $product, ProductOption $option): void
    {
        abort_unless((int) $option->product_id === (int) $product->id, 404);
    }
}
