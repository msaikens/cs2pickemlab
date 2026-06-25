@extends('layouts.app', ['title' => 'CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<section class="home-hero">
    <div class="home-hero-shell">
        <div class="home-hero-copy">
            <p class="home-kicker">CS2 picks, match reads, and custom gamer awards</p>

            <h1>Smarter CS2 picks before every match.</h1>

            <p>
                Track team form, upset risk, Pick&#8217;em recommendations, and order custom esports awards for your squad, LAN, or Discord tournament.
            </p>

            <div class="home-hero-actions">
                <a href="{{ route('pickem.index') }}" class="home-button primary">
                    Open Pick&#8217;em Assistant
                </a>

                <a href="{{ route('shop.index') }}" class="home-button secondary">
                    Shop Custom Awards
                </a>
            </div>
        </div>

        <aside class="home-match-board">
            <div class="home-card-heading">
                <p class="home-kicker">Live Board</p>
                <h2>Today&#8217;s match board</h2>
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
                        <p>Check back later for matches!</p>
                    </div>
                @endforelse
            </div>
        </aside>
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
                    <p>Create predictions from the admin panel to show them here.</p>
                </div>
            @endforelse
        </div>
    </div>

    <aside class="home-shop-column">
        <div class="home-section-header">
            <p class="home-kicker">Awards</p>
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
                    <p>Add featured products to populate this section.</p>
                </div>
            @endforelse
        </div>
    </aside>
</section>
@endsection