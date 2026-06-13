@extends('layouts.admin', [
    'title' => 'Edit Product Variant | CS2 PickLab',
    'pageTitle' => 'Edit Product Variant',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <a href="{{ route('admin.products.variants.index', $product) }}" class="link-accent">
                ← Back to Variants
            </a>

            <h2 class="product-admin-title">
                Edit {{ $variant->name }}
            </h2>

            <p class="product-admin-subtitle">
                Update SKU, price, inventory, and active status for this product variant.
            </p>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" class="product-admin-form">
            @csrf
            @method('PUT')

            @include('admin.products.variants.form', [
                'product' => $product,
                'variant' => $variant,
            ])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Variant
                </button>
            </div>
        </form>
    </div>
@endsection