@extends('layouts.admin', [
    'title' => 'Product Variants | CS2 PickLab',
    'pageTitle' => 'Product Variants',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="link-accent">
                ← Back to {{ $product->name }}
            </a>

            <h2 class="product-admin-title">{{ $product->name }} Variants</h2>
            <p class="product-admin-subtitle">
                Manage sellable versions, SKU-level pricing, and optional inventory.
            </p>
        </div>

        <a href="{{ route('admin.products.variants.create', $product) }}" class="btn-primary">
            Add Variant
        </a>
    </div>

    <div class="product-admin-table-wrap">
        <table class="product-admin-table">
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
                            <p class="product-admin-row-title">{{ $variant->name }}</p>
                        </td>

                        <td>
                            {{ $variant->sku ?? 'No SKU' }}
                        </td>

                        <td>
                            <span class="product-price">
                                ${{ $variant->price_dollars }}
                            </span>
                        </td>

                        <td>
                            @if($variant->inventory_quantity === null)
                                <span class="product-flag">
                                    Made to order
                                </span>
                            @else
                                {{ $variant->inventory_quantity }}
                            @endif
                        </td>

                        <td>
                            @if($variant->is_active)
                                <span class="product-flag product-flag-featured">Yes</span>
                            @else
                                <span class="product-flag">No</span>
                            @endif
                        </td>

                        <td class="text-right">
                            <div class="product-admin-actions">
                                <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                                    onsubmit="return confirm('Delete this variant?');"
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
                            No variants yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection