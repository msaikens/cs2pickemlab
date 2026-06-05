@extends('layouts.admin', [
    'title' => 'Product Options | CS2 PickLab',
    'pageTitle' => 'Product Options',
])

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('admin.products.edit', $product) }}" class="link-accent">
            ← Back to {{ $product->name }}
        </a>
        <h2 class="mt-3 page-title">{{ $product->name }} Options</h2>
        <p class="page-subtitle">Manage customization fields, selectable choices, uploads, and add-ons.</p>
    </div>

    <a href="{{ route('admin.products.options.create', $product) }}" class="btn-primary">
        Add Option
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
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
                        <p class="font-bold text-white">{{ $option->name }}</p>
                        <p class="text-muted-xs">{{ $option->slug }}</p>

                        @if($option->help_text)
                            <p class="mt-1 text-xs text-slate-400">{{ $option->help_text }}</p>
                        @endif
                    </td>
                    <td class="text-slate-300">{{ ucfirst($option->type) }}</td>
                    <td class="text-slate-300">{{ $option->is_required ? 'Yes' : 'No' }}</td>
                    <td class="text-slate-300">
                        @if($option->values->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($option->values as $value)
                                    <div class="text-xs">
                                        {{ $value->label }}

                                        @if($value->price_delta !== 0)
                                            <span class="text-cyan-300">
                                                {{ $value->price_delta > 0 ? '+' : '-' }}${{ number_format(abs($value->price_delta) / 100, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-slate-500">None</span>
                        @endif
                    </td>
                    <td class="text-slate-300">{{ $option->sort_order }}</td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.products.options.edit', [$product, $option]) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.products.options.destroy', [$product, $option]) }}" onsubmit="return confirm('Delete this option?');">
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
                    <td colspan="6" class="empty-row">No options yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
