@extends('layouts.admin', [
    'title' => 'Edit Product | CS2 PickLab',
    'pageTitle' => 'Edit Product',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <a href="{{ route('admin.products.index') }}" class="link-accent">
                ← Back to Products
            </a>

            <h2 class="product-admin-title">
                Edit {{ $product->name }}
            </h2>

            <p class="product-admin-subtitle">
                Update product details, pricing, customization rules, and shop visibility.
            </p>
        </div>

        <div class="product-admin-header-actions">
            <a href="{{ route('admin.products.options.index', $product) }}" class="btn-accent">
                Manage Options
            </a>

            <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-accent">
                Manage Variants
            </a>

            <a href="{{ route('shop.show', $product) }}" class="btn-secondary">
                View Product
            </a>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="product-admin-form">
            @csrf
            @method('PUT')

            @include('admin.products.form', ['product' => $product])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Product
                </button>
            </div>
        </form>
    </div>
@endsection