@extends('layouts.app')

@section('title', 'Skin Marketplace')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/marketplace-browse.css') }}">
@endpush

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <header class="marketplace-profile-hero">
            <div class="marketplace-profile-kicker">CS2 Marketplace</div>
            <h1>Skin Marketplace</h1>
            <p>Browse real listings created from synced Steam inventories.</p>

            @auth
                @if (auth()->user()->canUseMarketplace() && Route::has('marketplace.listings.create'))
                    <a href="{{ route('marketplace.listings.create') }}" class="marketplace-button primary">
                        Sell Skins
                    </a>
                @elseif (Route::has('profile.steam'))
                    <a href="{{ route('profile.steam') }}" class="marketplace-button secondary">
                        Finish Marketplace Setup
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="marketplace-button secondary">
                    Sign In to Trade
                </a>
            @endauth
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
            <form method="GET" action="{{ route('marketplace.index') }}" class="marketplace-filter-bar">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search AK-47, Doppler, Gloves, Factory New..."
                >

                <select name="listing_type">
                    <option value="">All Types</option>
                    <option value="trade" @selected(request('listing_type') === 'trade')>Trade</option>
                    <option value="sale" @selected(request('listing_type') === 'sale')>Sale</option>
                </select>

                <select name="rarity">
                    <option value="">All Rarities</option>
                    @foreach ($rarities as $rarity)
                        <option value="{{ $rarity }}" @selected(request('rarity') === $rarity)>
                            {{ $rarity }}
                        </option>
                    @endforeach
                </select>

                <select name="wear_name">
                    <option value="">All Wear</option>
                    @foreach ($wears as $wear)
                        <option value="{{ $wear }}" @selected(request('wear_name') === $wear)>
                            {{ $wear }}
                        </option>
                    @endforeach
                </select>

                <select name="sort">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Price Low</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Price High</option>
                </select>

                <button type="submit" class="marketplace-button primary">
                    Filter
                </button>
            </form>
        </section>

        @if ($listings->count() === 0)
            <section class="marketplace-card">
                <div class="marketplace-empty-state">
                    <strong>No listings found.</strong>
                    <p>Once users sync inventory and list skins, active listings will appear here.</p>
                </div>
            </section>
        @else
            <section class="marketplace-listing-grid">
                @foreach ($listings as $listing)
                    <a href="{{ route('marketplace.listings.show', $listing) }}" class="marketplace-skin-card">
                        <div class="marketplace-skin-image-wrap">
                            @if ($listing->image_url)
                                <img src="{{ $listing->image_url }}" alt="{{ $listing->market_hash_name }}">
                            @else
                                <div class="marketplace-skin-placeholder">CS2</div>
                            @endif
                        </div>

                        <div class="marketplace-skin-body">
                            <h2>{{ $listing->market_hash_name }}</h2>

                            <div class="marketplace-skin-meta">
                                <span>{{ $listing->rarity ?? 'Unknown rarity' }}</span>
                                <span>{{ $listing->wear_name ?? 'Unknown wear' }}</span>
                            </div>

                            <div class="marketplace-skin-footer">
                                <strong>{{ $listing->display_price }}</strong>

                                <span>
                                    {{ $listing->user?->steamAccount?->persona_name ?? $listing->user?->displayName() ?? 'Seller' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </section>

            <div class="marketplace-pagination">
                {{ $listings->links() }}
            </div>
        @endif
    </section>
</main>
@endsection