@extends('layouts.admin', [
    'title' => 'Create Product Option | CS2 PickLab',
    'pageTitle' => 'Create Product Option',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <a href="{{ route('admin.products.options.index', $product) }}" class="link-accent">
                ← Back to Options
            </a>

            <h2 class="product-admin-title">Create Product Option</h2>
            <p class="product-admin-subtitle">
                Add a customization field, selectable choice group, upload field, or add-on for {{ $product->name }}.
            </p>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.options.store', $product) }}" class="product-admin-form">
            @csrf

            @include('admin.products.options.form', [
                'product' => $product,
                'option' => $option,
            ])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.options.index', $product) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Option
                </button>
            </div>
        </form>
    </div>
@endsection