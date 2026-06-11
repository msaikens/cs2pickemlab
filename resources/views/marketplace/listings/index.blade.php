@extends('layouts.app')

@section('title', 'My Listings')

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <header class="marketplace-profile-hero">
            <div class="marketplace-profile-kicker">Marketplace</div>
            <h1>My Listings</h1>
            <p>Manage your active, pending, completed, and cancelled skin listings.</p>

            <div class="marketplace-account-actions">
                <a href="{{ route('marketplace.listings.create') }}" class="marketplace-button primary">
                    Create Listing
                </a>

                <a href="{{ route('marketplace.trade-requests.index') }}" class="marketplace-button secondary">
                    Trade Requests
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="marketplace-alert marketplace-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="marketplace-alert marketplace-alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <section class="marketplace-card">
            <form method="GET" action="{{ route('marketplace.listings.index') }}" class="marketplace-filter-bar my-listings-filter">
                <select name="status">
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
            <section class="marketplace-card">
                <div class="marketplace-empty-state">
                    <strong>No listings found.</strong>
                    <p>Create a listing from your synced Steam inventory.</p>
                </div>
            </section>
        @else
            <section class="my-listings-list">
                @foreach ($listings as $listing)
                    <article class="my-listing-card">
                        <div class="my-listing-image">
                            @if ($listing->image_url)
                                <img src="{{ $listing->image_url }}" alt="{{ $listing->market_hash_name }}">
                            @else
                                <div class="marketplace-skin-placeholder">CS2</div>
                            @endif
                        </div>

                        <div class="my-listing-main">
                            <div class="my-listing-heading">
                                <div>
                                    <h2>
                                        <a href="{{ route('marketplace.listings.show', $listing) }}">
                                            {{ $listing->market_hash_name }}
                                        </a>
                                    </h2>

                                    <p>
                                        {{ ucfirst($listing->listing_type) }}
                                        ·
                                        {{ $listing->display_price }}
                                        ·
                                        {{ $listing->created_at?->diffForHumans() }}
                                    </p>
                                </div>

                                <span class="listing-status {{ $listing->status }}">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </div>

                            <div class="marketplace-skin-meta">
                                <span>{{ $listing->rarity ?? 'Unknown rarity' }}</span>
                                <span>{{ $listing->wear_name ?? 'Unknown wear' }}</span>
                                <span>{{ $listing->tradeRequests->count() }} request(s)</span>
                            </div>

                            @if ($listing->tradeRequests->count() > 0)
                                <div class="my-listing-requests">
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

            <div class="marketplace-pagination">
                {{ $listings->links() }}
            </div>
        @endif
    </section>
</main>
@endsection