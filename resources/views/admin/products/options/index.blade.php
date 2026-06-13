@extends('layouts.admin', [
    'title' => 'Product Options | CS2 PickLab',
    'pageTitle' => 'Product Options',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-products.css') }}">
@endpush

@section('content')
    <div class="product-admin-header">
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="link-accent">
                ← Back to {{ $product->name }}
            </a>

            <h2 class="product-admin-title">{{ $product->name }} Options</h2>
            <p class="product-admin-subtitle">
                Manage customization fields, selectable choices, uploads, and add-ons.
            </p>
        </div>

        <a href="{{ route('admin.products.options.create', $product) }}" class="btn-primary">
            Add Option
        </a>
    </div>

    <div class="product-admin-table-wrap">
        <table class="product-admin-table">
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th>Values</th>
                    <th>Sort</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($product->options as $option)
                    <tr>
                        <td>
                            <p class="product-admin-row-title">{{ $option->name }}</p>
                            <p class="product-admin-muted">{{ $option->slug }}</p>

                            @if($option->help_text)
                                <p class="product-admin-help-text">{{ $option->help_text }}</p>
                            @endif
                        </td>

                        <td>
                            <span class="product-option-type product-option-type-{{ $option->type }}">
                                {{ ucfirst($option->type) }}
                            </span>
                        </td>

                        <td>
                            @if($option->is_required)
                                <span class="product-flag product-flag-featured">Yes</span>
                            @else
                                <span class="product-flag">No</span>
                            @endif
                        </td>

                        <td>
                            @if($option->values->isNotEmpty())
                                <div class="product-admin-option-values">
                                    @foreach($option->values as $value)
                                        <div>
                                            <span>{{ $value->label }}</span>

                                            @if($value->price_delta !== 0)
                                                <strong>
                                                    {{ $value->price_delta > 0 ? '+' : '-' }}${{ number_format(abs($value->price_delta) / 100, 2) }}
                                                </strong>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="product-admin-muted">None</span>
                            @endif
                        </td>

                        <td>
                            {{ $option->sort_order }}
                        </td>

                        <td class="text-right">
                            <div class="product-admin-actions">
                                <a href="{{ route('admin.products.options.edit', [$product, $option]) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.products.options.destroy', [$product, $option]) }}"
                                    onsubmit="return confirm('Delete this option?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn-small-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="product-admin-empty">
                            No options yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection