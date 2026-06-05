@extends('layouts.admin', [
    'title' => 'Product Variants | CS2 PickLab',
    'pageTitle' => 'Product Variants',
])

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('admin.products.edit', $product) }}" class="link-accent">
            ← Back to {{ $product->name }}
        </a>
        <h2 class="mt-3 page-title">{{ $product->name }} Variants</h2>
        <p class="page-subtitle">Manage sellable versions, SKU-level pricing, and optional inventory.</p>
    </div>

    <a href="{{ route('admin.products.variants.create', $product) }}" class="btn-primary">
        Add Variant
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Variant</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Inventory</th>
                <th>Active</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($product->variants as $variant)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $variant->name }}</p>
                    </td>
                    <td class="text-slate-300">{{ $variant->sku ?? 'No SKU' }}</td>
                    <td class="price-text">${{ $variant->price_dollars }}</td>
                    <td class="text-slate-300">
                        {{ $variant->inventory_quantity === null ? 'Unlimited / made to order' : $variant->inventory_quantity }}
                    </td>
                    <td class="text-slate-300">{{ $variant->is_active ? 'Yes' : 'No' }}</td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirm('Delete this variant?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-small-danger">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">No variants yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
