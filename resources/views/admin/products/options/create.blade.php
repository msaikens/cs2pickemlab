@extends('layouts.admin', [
    'title' => 'Create Product Option | CS2 PickLab',
    'pageTitle' => 'Create Product Option',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.options.index', $product) }}" class="link-accent">
        ← Back to Options
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.products.options.store', $product) }}" class="space-y-6">
        @csrf

        @include('admin.products.options.form', [
            'product' => $product,
            'option' => $option,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.products.options.index', $product) }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Option</button>
        </div>
    </form>
</div>
@endsection
