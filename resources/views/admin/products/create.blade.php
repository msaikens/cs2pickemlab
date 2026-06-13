@extends('layouts.admin', [
    'title' => 'Create Product | CS2 PickLab',
    'pageTitle' => 'Create Product',
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

            <h2 class="product-admin-title">Create Product</h2>
            <p class="product-admin-subtitle">
                Add a shop product with pricing, type, customization rules, and display settings.
            </p>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.store') }}" class="product-admin-form">
            @csrf

            @include('admin.products.form', ['product' => $product])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Product
                </button>
            </div>
        </form>
    </div>
@endsection