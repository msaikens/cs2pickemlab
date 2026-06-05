@extends('layouts.admin', [
    'title' => 'Create Product Variant | CS2 PickLab',
    'pageTitle' => 'Create Product Variant',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.variants.index', $product) }}" class="link-accent">
        ← Back to Variants
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" class="space-y-6">
        @csrf

        @include('admin.products.variants.form', [
            'product' => $product,
            'variant' => $variant,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.variants.index', $product) }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Variant</button>
        </div>
    </form>
</div>
@endsection
