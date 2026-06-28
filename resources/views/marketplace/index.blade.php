@extends('layouts.public', [
    'title' => 'Marketplace | CS2 PickLab',
    'pageTitle' => 'Marketplace',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-browse.css') }}">
@endpush

@section('content')
<section class="marketplace-browse-page">
    <header class="marketplace-browse-hero">
        <div>
            <p class="marketplace-browse-kicker">CS2 PickLab Marketplace</p>
            <h1>Browse CS2 Inventory</h1>
            <p>
                View active CS2 item listings before creating an account. Sign in only when you are ready to sell, trade, or buy.
            </p>
        </div>

        <div class="marketplace-browse-hero-actions">
            @auth
                <a href="{{ route('marketplace.listings.create') }}" class="marketplace-browse-button primary">
                    Sell an Item
                </a>

                <a href="{{ route('marketplace.listings.index') }}" class="marketplace-browse-button secondary">
                    My Listings
                </a>
            @else
                <a href="{{ route('login') }}" class="marketplace-browse-button primary">
                    Sign In to Trade
                </a>

                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="marketplace-browse-button secondary">
                        Create Account
                    </a>
                @endif
            @endauth
        </div>
    </header>

    <section class="marketplace-browse-card">
        <form method="GET" action="{{ route('marketplace.index') }}" class="marketplace-browse-filters">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search skins, weapons, rarity, wear..."
            >

            <select name="type">
                <option value="">All listing types</option>
                <option value="sale" @selected(request('type') === 'sale')>For Sale</option>
                <option value="trade" @selected(request('type') === 'trade')>For Trade</option>
            </select>

            <select name="rarity">
                <option value="">All rarities</option>

                @foreach($rarities as $rarity)
                    <option value="{{ $rarity }}" @selected(request('rarity') === $rarity)>
                        {{ $rarity }}
                    </option>
                @endforeach
            </select>

            <input
                type="number"
                min="0"
                step="0.01"
                name="min_price"
                value="{{ request('min_price') }}"
                placeholder="Min $"
            >

            <input
                type="number"
                min="0"
                step="0.01"
                name="max_price"
                value="{{ request('max_price') }}"
                placeholder="Max $"
            >

            <button type="submit">
                Filter
            </button>

            @if(request()->hasAny(['q', 'type', 'rarity', 'min_price', 'max_price']))
                <a href="{{ route('marketplace.index') }}">
                    Reset
                </a>
            @endif
        </form>
    </section>

    <section class="marketplace-browse-grid">
        @forelse($listings as $listing)
            @php
                $sellerName = 'Marketplace Seller';

                if ($listing->user) {
                    if (method_exists($listing->user, 'publicDisplayName')) {
                        $sellerName = $listing->user->publicDisplayName(auth()->user());
                    } elseif ($listing->user->steamAccount?->persona_name) {
                        $sellerName = $listing->user->steamAccount->persona_name;
                    } elseif ($listing->user->profile?->display_name) {
                        $sellerName = $listing->user->profile->display_name;
                    }
                }
            @endphp

            <article class="marketplace-listing-card">
                <a href="{{ route('marketplace.listings.show', $listing) }}" class="marketplace-listing-image-wrap">
                    @if($listing->image_url)
                        <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}">
                    @else
                        <div class="marketplace-listing-image-placeholder">
                            No Image
                        </div>
                    @endif
                </a>

                <div class="marketplace-listing-body">
                    <div class="marketplace-listing-tags">
                        <span>{{ ucfirst($listing->listing_type) }}</span>

                        @if($listing->rarity)
                            <span>{{ $listing->rarity }}</span>
                        @endif
                    </div>

                    <h2>
                        <a href="{{ route('marketplace.listings.show', $listing) }}">
                            {{ $listing->item_name }}
                        </a>
                    </h2>

                    @if($listing->weapon_type)
                        <p>{{ $listing->weapon_type }}</p>
                    @endif

                    @if($listing->wear_name)
                        <p>{{ $listing->wear_name }}</p>
                    @endif

                    <div class="marketplace-listing-meta">
                        <span>Seller</span>
                        <strong>{{ $sellerName }}</strong>
                    </div>

                    <div class="marketplace-listing-price">
                        @if($listing->listing_type === 'sale' && $listing->asking_price_cents)
                            <strong>${{ number_format($listing->asking_price_cents / 100, 2) }}</strong>
                        @else
                            <strong>Trade Offers</strong>
                        @endif
                    </div>

                    <div class="marketplace-supervisor-card">
                        <span>Marketplace Supervisor</span>

                        @if($listing->supervisor)
                            <strong>
                                {{ method_exists($listing->supervisor, 'publicDisplayName')
                                    ? $listing->supervisor->publicDisplayName(auth()->user())
                                    : $listing->supervisor->displayName() }}
                            </strong>

                            @if($listing->supervisor_assigned_at)
                                <small>
                                    Assigned {{ $listing->supervisor_assigned_at->format('M j, Y') }}
                                </small>
                            @endif
                        @else
                            <strong>Pending assignment</strong>
                            <small>A site admin will be assigned automatically.</small>
                        @endif
                    </div>

                    <a href="{{ route('marketplace.listings.show', $listing) }}" class="marketplace-browse-button full">
                        View Listing
                    </a>
                </div>
            </article>
        @empty
            <section class="marketplace-browse-empty">
                <h2>No active listings found.</h2>
                <p>Try changing your filters or check back after more items are listed.</p>
            </section>
        @endforelse
    </section>

    @if($listings->hasPages())
        <div class="marketplace-browse-pagination">
            {{ $listings->links() }}
        </div>
    @endif
</section>
@endsection