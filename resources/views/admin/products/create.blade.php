@extends('layouts.admin', [
    'title' => 'Create Product | CS2 PickLab',
    'pageTitle' => 'Create Product',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="link-accent">
        ← Back to Products
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
        @csrf

        @include('admin.products.form', ['product' => $product])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Product</button>
        </div>
    </form>
</div>
@endsection
