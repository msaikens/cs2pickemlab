@extends('layouts.app', [
    'title' => 'My Listings | CS2 PickLab',
])

@section('title', 'My Listings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-my-listings.css') }}">
@endpush

@section('content')
<main class="my-marketplace-page">
    <section class="my-marketplace-shell">
        <header class="my-marketplace-hero">
            <div class="my-marketplace-hero-copy">
                <p class="my-marketplace-kicker">Marketplace</p>
                <h1>My Listings</h1>
                <p>Manage your active, pending, completed, and cancelled skin listings.</p>
            </div>

            <div class="my-marketplace-actions">
                <a href="{{ route('marketplace.listings.create') }}" class="marketplace-button primary">
                    Create Listing
                </a>

                <a href="{{ route('marketplace.trade-requests.index') }}" class="marketplace-button secondary">
                    Trade Requests
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="my-marketplace-alert success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="my-marketplace-alert danger">
                {{ session('error') }}
            </div>
        @endif

        <section class="my-marketplace-card my-marketplace-filter-card">
            <div>
                <p class="my-marketplace-card-label">Listing Status</p>
                <h2>Filter Listings</h2>
            </div>

            <form method="GET" action="{{ route('marketplace.listings.index') }}" class="my-listings-filter">
                <select name="status" aria-label="Filter listings by status">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>

                <button type="submit" class="marketplace-button primary">
                    Filter
                </button>

                <a href="{{ route('marketplace.listings.index') }}" class="marketplace-button secondary">
                    Reset
                </a>
            </form>
        </section>

        @if ($listings->count() === 0)
            <section class="my-marketplace-card">
                <div class="my-marketplace-empty">
                    <div class="my-marketplace-empty-icon">CS2</div>
                    <strong>No listings found.</strong>
                    <p>Create a listing from your synced Steam inventory.</p>

                    <a href="{{ route('marketplace.listings.create') }}" class="marketplace-button primary">
                        Create Listing
                    </a>
                </div>
            </section>
        @else
            <section class="my-listings-list">
                @foreach ($listings as $listing)
                    @php
                        $statusClass = strtolower((string) $listing->status);
                        $rarity = strtolower((string) ($listing->rarity ?? 'unknown'));

                        $rarityClass = match (true) {
                            str_contains($rarity, 'consumer') => 'rarity-consumer',
                            str_contains($rarity, 'industrial') => 'rarity-industrial',
                            str_contains($rarity, 'mil-spec'), str_contains($rarity, 'milspec') => 'rarity-milspec',
                            str_contains($rarity, 'restricted') => 'rarity-restricted',
                            str_contains($rarity, 'classified') => 'rarity-classified',
                            str_contains($rarity, 'covert') => 'rarity-covert',
                            str_contains($rarity, 'contraband') => 'rarity-contraband',
                            default => 'rarity-default',
                        };
                    @endphp

                    <article class="my-listing-card {{ $rarityClass }}">
                        <div class="my-listing-image">
                            @if ($listing->image_url)
                                <img src="{{ $listing->image_url }}" alt="{{ $listing->market_hash_name }}">
                            @else
                                <div class="my-listing-placeholder">CS2</div>
                            @endif
                        </div>

                        <div class="my-listing-main">
                            <div class="my-listing-heading">
                                <div class="my-listing-title-group">
                                    <p class="my-listing-type">
                                        {{ ucfirst($listing->listing_type) }}
                                        <span>·</span>
                                        {{ $listing->display_price }}
                                    </p>

                                    <h2>
                                        <a href="{{ route('marketplace.listings.show', $listing) }}">
                                            {{ $listing->market_hash_name }}
                                        </a>
                                    </h2>

                                    <p class="my-listing-date">
                                        Created {{ $listing->created_at?->diffForHumans() }}
                                    </p>
                                </div>

                                <span class="my-listing-status {{ $statusClass }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </div>

                            <div class="my-listing-meta">
                                <span>{{ $listing->rarity ?? 'Unknown rarity' }}</span>
                                <span>{{ $listing->wear_name ?? 'Unknown wear' }}</span>
                                <span>{{ $listing->tradeRequests->count() }} request(s)</span>
                            </div>

                            @if ($listing->tradeRequests->count() > 0)
                                <div class="my-listing-requests">
                                    <div class="my-listing-requests-title">
                                        Recent Requests
                                    </div>

                                    @foreach ($listing->tradeRequests->take(3) as $tradeRequest)
                                        <div class="my-listing-request-row">
                                            <span>
                                                {{ $tradeRequest->buyer?->steamAccount?->persona_name ?? $tradeRequest->buyer?->displayName() ?? 'Buyer' }}
                                            </span>

                                            <strong>{{ ucfirst($tradeRequest->status) }}</strong>
                                        </div>
                                    @endforeach

                                    @if ($listing->tradeRequests->count() > 3)
                                        <div class="my-listing-request-row muted">
                                            <span>More requests available in Trade Requests.</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="my-listing-actions">
                                <a href="{{ route('marketplace.listings.show', $listing) }}" class="marketplace-button secondary">
                                    View
                                </a>

                                @if (in_array($listing->status, ['draft', 'active', 'pending'], true))
                                    <form
                                        method="POST"
                                        action="{{ route('marketplace.listings.cancel', $listing) }}"
                                        onsubmit="return confirm('Cancel this listing? Open trade requests for it will also be cancelled.');"
                                    >
                                        @csrf

                                        <button type="submit" class="marketplace-button danger">
                                            Cancel Listing
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="my-marketplace-pagination">
                {{ $listings->links() }}
            </div>
        @endif
    </section>
</main>
@endsection