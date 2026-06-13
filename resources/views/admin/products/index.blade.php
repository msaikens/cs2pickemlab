@extends('layouts.admin', [
    'title' => 'Products | CS2 PickLab',
    'pageTitle' => 'Products',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <h2 class="product-admin-title">Products</h2>
            <p class="product-admin-subtitle">
                Manage shop products. Options and variants are managed per product.
            </p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            Add Product
        </a>
    </div>

    <div class="product-admin-table-wrap">
        <table class="product-admin-table">
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
                            <p class="product-admin-row-title">{{ $product->name }}</p>
                            <p class="product-admin-muted">
                                {{ $product->sku ?? 'No SKU' }} · /shop/{{ $product->slug }}
                            </p>
                        </td>

                        <td>
                            <span class="product-type product-type-{{ $product->product_type }}">
                                {{ ucfirst($product->product_type) }}
                            </span>
                        </td>

                        <td>
                            <span class="product-status product-status-{{ $product->status }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>

                        <td>
                            <span class="product-price">
                                ${{ $product->base_price_dollars }}
                            </span>
                        </td>

                        <td>
                            @if($product->is_featured)
                                <span class="product-flag product-flag-featured">Yes</span>
                            @else
                                <span class="product-flag">No</span>
                            @endif
                        </td>

                        <td class="text-right">
                            <div class="product-admin-actions">
                                <a href="{{ route('shop.show', $product) }}" class="btn-small-secondary">
                                    View
                                </a>

                                <a href="{{ route('admin.products.options.index', $product) }}" class="btn-small-accent">
                                    Options
                                </a>

                                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-small-accent">
                                    Variants
                                </a>

                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.products.destroy', $product) }}"
                                    onsubmit="return confirm('Delete this product?');"
                                >
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
                        <td colspan="6" class="product-admin-empty">
                            No products yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="product-admin-pagination">
            {{ $products->links() }}
        </div>
    @endif
@endsection