@extends('layouts.app', ['title' => 'CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<section class="home-hero">
    <div class="home-hero-shell">
        <div class="home-hero-copy">
            <p class="home-kicker">
                CS2 Picks, Skins, Pins & Marketplace Tools
            </p>

            <h1>
                Pick smarter. Trade sharper. Build your CS2 edge.
            </h1>

            <p class="home-hero-lede">
                CS2 PickLab combines pick’em predictions, match analysis, and a supervised marketplace for
                CS2 skins, pins, cases, capsules, stickers, graffiti, and other tradeable inventory.
            </p>

            <div class="home-hero-actions">
                <a href="{{ route('marketplace.index') }}" class="home-button primary">
                    Browse Marketplace
                </a>

                <a href="{{ route('pickem.index') }}" class="home-button secondary">
                    View Pick’ems
                </a>

                @auth
                    <a href="{{ route('marketplace.listings.create') }}" class="home-button ghost">
                        Sell or Trade Items
                    </a>
                @else
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="home-button ghost">
                            Create Account
                        </a>
                    @endif
                @endauth
            </div>

            <div class="home-hero-stats" aria-label="CS2 PickLab platform features">
                <div>
                    <strong>Skins</strong>
                    <span>Browse tradeable CS2 inventory</span>
                </div>

                <div>
                    <strong>Pick’ems</strong>
                    <span>Follow match predictions</span>
                </div>

                <div>
                    <strong>Supervised</strong>
                    <span>Marketplace activity with admin oversight</span>
                </div>
            </div>
        </div>

        <aside class="home-match-board">
            <div class="home-card-heading">
                <p class="home-kicker">Live Board</p>
                <h2>Today’s match board</h2>
            </div>

            <div class="home-match-list">
                @forelse($upcomingMatches as $match)
                    <a href="{{ route('matches.show', $match) }}" class="home-match-card">
                        <div class="home-match-main">
                            <strong>{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</strong>
                            <span>{{ $match->event?->name }} · {{ strtoupper($match->format) }}</span>
                        </div>

                        @if($match->prediction)
                            <span class="home-confidence-pill">
                                {{ $match->prediction->confidence_score }}%
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="home-empty-card">
                        <strong>No matches loaded yet.</strong>
                        <p>Check back later for matches.</p>
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</section>

<section class="home-marketplace-feature">
    <div class="home-marketplace-copy">
        <p class="home-kicker">Tradeable CS2 Inventory</p>

        <h2>
            A marketplace for skins, pins, stickers, cases, capsules, graffiti, and more.
        </h2>

        <p>
            Browse active listings before creating an account. When you are ready, connect your Steam profile,
            complete marketplace setup, and list items for sale or trade.
        </p>

        <div class="home-marketplace-actions">
            <a href="{{ route('marketplace.index') }}" class="home-button primary">
                Browse Active Listings
            </a>

            @auth
                <a href="{{ route('marketplace.listings.create') }}" class="home-button secondary">
                    List an Item
                </a>
            @else
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="home-button secondary">
                        Join to Sell or Trade
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="home-marketplace-grid" aria-label="Marketplace categories">
        <article>
            <span>Skins</span>
            <strong>Rifles, pistols, knives, gloves, and weapon finishes</strong>
        </article>

        <article>
            <span>Pins</span>
            <strong>Collectible CS2 pins and display inventory</strong>
        </article>

        <article>
            <span>Stickers</span>
            <strong>Team, event, capsule, and player stickers</strong>
        </article>

        <article>
            <span>Cases & Capsules</span>
            <strong>Tradeable containers, capsules, and collectible drops</strong>
        </article>

        <article>
            <span>Graffiti</span>
            <strong>Sealed graffiti and cosmetic extras</strong>
        </article>

        <article>
            <span>Supervisor Assigned</span>
            <strong>Every marketplace listing and trade receives admin oversight to prevent fraud and scamming.</strong>
        </article>
    </div>
</section>

<section class="home-content">
    <div class="home-main-column">
        <div class="home-section-header">
            <p class="home-kicker">Analysis</p>
            <h2>Latest predictions</h2>
        </div>

        <div class="home-prediction-grid">
            @forelse($latestPredictions as $prediction)
                <a href="{{ route('matches.show', $prediction->match) }}" class="home-prediction-card">
                    <p>
                        {{ $prediction->match->teamOne->name }} vs {{ $prediction->match->teamTwo->name }}
                    </p>

                    <h3>{{ $prediction->headline }}</h3>

                    <span>{{ $prediction->summary }}</span>
                </a>
            @empty
                <div class="home-empty-card">
                    <strong>No predictions yet.</strong>
                    <p>Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>

    <aside class="home-shop-column">
        <div class="home-section-header">
            <p class="home-kicker">Awards & Gear</p>
            <h2>Featured shop</h2>
        </div>

        <div class="home-shop-list">
            @forelse($featuredProducts as $product)
                <a href="{{ route('shop.show', $product) }}" class="home-shop-card">
                    <div>
                        <h3>{{ $product->name }}</h3>
                        <p>{{ $product->short_description }}</p>
                    </div>

                    <strong>${{ $product->base_price_dollars }}</strong>
                </a>
            @empty
                <div class="home-empty-card">
                    <strong>No products yet.</strong>
                    <p>Check back soon!</p>
                </div>
            @endforelse
        </div>
    </aside>
</section>
@endsection