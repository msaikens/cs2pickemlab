@extends('layouts.admin', [
    'title' => 'Products | CS2 PickLab',
    'pageTitle' => 'Products',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Products</h2>
        <p class="page-subtitle">Manage shop products. Options and variants are managed per product.</p>
    </div>

    <a href="{{ route('admin.products.create') }}" class="btn-primary">
        Add Product
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Type</th>
                <th>Status</th>
                <th>Price</th>
                <th>Featured</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $product->name }}</p>
                        <p class="text-muted-xs">{{ $product->sku ?? 'No SKU' }} · /shop/{{ $product->slug }}</p>
                    </td>
                    <td class="text-slate-300">{{ ucfirst($product->product_type) }}</td>
                    <td>
                        <span class="status-pill">{{ ucfirst($product->status) }}</span>
                    </td>
                    <td class="price-text">${{ $product->base_price_dollars }}</td>
                    <td class="text-slate-300">{{ $product->is_featured ? 'Yes' : 'No' }}</td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('shop.show', $product) }}" class="btn-small-secondary">View</a>
                            <a href="{{ route('admin.products.options.index', $product) }}" class="btn-small-accent">Options</a>
                            <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-small-accent">Variants</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-small-primary">Edit</a>

                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-small-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">No products yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
