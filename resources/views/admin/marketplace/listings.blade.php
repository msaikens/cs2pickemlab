@extends('layouts.admin', [
    'title' => 'Marketplace Listings | CS2 PickLab',
    'pageTitle' => 'Marketplace Listings',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-marketplace.css') }}">
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Marketplace Listings</h2>
            <p class="page-subtitle">
                Review, search, and moderate user skin listings.
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.marketplace.listings') }}" class="admin-filter-panel">
        <input
            name="search"
            type="search"
            value="{{ request('search') }}"
            placeholder="Search listing, asset ID, user..."
            class="form-input admin-filter-search"
        >

        <select name="status" class="form-input admin-filter-select">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>

        <button type="submit" class="btn-primary">
            Filter
        </button>

        <a href="{{ route('admin.marketplace.listings') }}" class="btn-secondary">
            Reset
        </a>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Seller</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Requests</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($listings as $listing)
                    <tr>
                        <td>
                            <div class="marketplace-admin-item">
                                @if($listing->image_url)
                                    <img
                                        src="{{ $listing->image_url }}"
                                        alt="{{ $listing->market_hash_name }}"
                                        class="marketplace-admin-thumb"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="marketplace-admin-thumb placeholder">
                                        CS2
                                    </div>
                                @endif

                                <div class="marketplace-admin-item-main">
                                    <p class="table-primary-text">
                                        {{ $listing->market_hash_name }}
                                    </p>

                                    <p class="text-muted-xs">
                                        Asset: {{ $listing->steam_asset_id ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td>
                            @include('components.user-identity', [
                                'user' => $listing->user,
                                'size' => 'sm',
                                'showEmail' => true,
                                'showAccountType' => true,
                                'showAccountName' => false,
                            ])
                        </td>

                        <td>
                            <span class="status-pill status-pill-{{ $listing->status }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>

                        <td>
                            <span class="price-text">
                                {{ $listing->display_price }}
                            </span>
                        </td>

                        <td>
                            {{ $listing->tradeRequests->count() }}
                        </td>

                        <td class="text-right">
                            <div class="table-actions">
                                <a href="{{ route('marketplace.listings.show', $listing) }}" class="btn-small-secondary">
                                    View
                                </a>

                                @if(in_array($listing->status, ['draft', 'active', 'pending'], true))
                                    <form
                                        method="POST"
                                        action="{{ route('admin.marketplace.listings.cancel', $listing) }}"
                                        onsubmit="return confirm('Cancel this listing and open requests?');"
                                    >
                                        @csrf

                                        <button type="submit" class="btn-small-danger">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">
                            No marketplace listings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($listings->hasPages())
        <div class="pagination-wrap">
            {{ $listings->links() }}
        </div>
    @endif
@endsection