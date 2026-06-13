@extends('layouts.admin', [
    'title' => 'Edit Product Option | CS2 PickLab',
    'pageTitle' => 'Edit Product Option',
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

            <h2 class="product-admin-title">
                Edit {{ $option->name }}
            </h2>

            <p class="product-admin-subtitle">
                Update option type, help text, requirement behavior, and selectable values.
            </p>
        </div>
    </div>

    <div class="product-admin-panel">
        <form method="POST" action="{{ route('admin.products.options.update', [$product, $option]) }}" class="product-admin-form">
            @csrf
            @method('PUT')

            @include('admin.products.options.form', [
                'product' => $product,
                'option' => $option,
            ])

            <div class="product-admin-form-actions">
                <a href="{{ route('admin.products.options.index', $product) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Option
                </button>
            </div>
        </form>
    </div>
@endsection