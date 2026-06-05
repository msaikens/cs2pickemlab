@extends('layouts.admin', [
    'title' => 'Edit Product Variant | CS2 PickLab',
    'pageTitle' => 'Edit Product Variant',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="link-accent">
        ← Back to Variants
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.products.variants.form', [
            'product' => $product,
            'variant' => $variant,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Variant</button>
        </div>
    </form>
</div>
@endsection
