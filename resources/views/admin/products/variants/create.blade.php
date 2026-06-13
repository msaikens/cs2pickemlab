@extends('layouts.admin', [
    'title' => 'Create Product Variant | CS2 PickLab',
    'pageTitle' => 'Create Product Variant',
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

            <h2 class="product-admin-title">Create Product Variant</h2>
            <p class="product-admin-subtitle">
                Add a sellable version of {{ $product->name }} with SKU-level pricing and inventory.
            </p>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="product-admin-form">
            @csrf

            @include('admin.products.variants.form', [
                'product' => $product,
                'variant' => $variant,
            ])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Variant
                </button>
            </div>
        </form>
    </div>
@endsection