@extends('layouts.admin', [
    'title' => 'Edit Product | CS2 PickLab',
    'pageTitle' => 'Edit Product',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.products.index') }}" class="link-accent">
        ← Back to Products
    </a>

    <div class="flex flex-wrap gap-2">
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

<div class="panel">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.products.form', ['product' => $product])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Product</button>
        </div>
    </form>
</div>
@endsection
