@extends('layouts.app')

@section('title', $listing->market_hash_name)

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <section class="marketplace-card marketplace-listing-detail">
            <div class="listing-detail-image">
                @if ($listing->image_url)
                    <img src="{{ $listing->image_url }}" alt="{{ $listing->market_hash_name }}">
                @else
                    <div class="marketplace-skin-placeholder">CS2</div>
                @endif
            </div>

            <div class="listing-detail-content">
                <span class="marketplace-profile-kicker">Marketplace Listing</span>

                <h1>{{ $listing->market_hash_name }}</h1>

                <div class="listing-detail-price">
                    {{ $listing->display_price }}
                </div>

                <div class="marketplace-detail-grid listing-detail-grid">
                    <div>
                        <span>Type</span>
                        <strong>{{ ucfirst($listing->listing_type) }}</strong>
                    </div>

                    <div>
                        <span>Rarity</span>
                        <strong>{{ $listing->rarity ?? 'Unknown' }}</strong>
                    </div>

                    <div>
                        <span>Wear</span>
                        <strong>{{ $listing->wear_name ?? 'Unknown' }}</strong>
                    </div>

                    <div>
                        <span>Seller</span>
                        <strong>{{ $listing->user?->steamAccount?->persona_name ?? $listing->user?->displayName() ?? 'Seller' }}</strong>
                    </div>
                </div>

                @auth
                    @if (auth()->id() === $listing->user_id)
                        <div class="marketplace-alert marketplace-alert-success">
                            This is your listing.
                        </div>
                    @elseif (auth()->user()->canUseMarketplace())
                        <form method="POST" action="{{ route('marketplace.trade-requests.store', $listing) }}" class="marketplace-form">
                            @csrf

                            <div class="form-row">
                                <label for="message">Trade Message</label>
                                <input
                                    id="message"
                                    name="message"
                                    type="text"
                                    maxlength="1000"
                                    placeholder="Send a short trade message to the seller."
                                >
                            </div>

                            <button type="submit" class="marketplace-button primary">
                                Request Trade
                            </button>
                        </form>
                    @else
                        <a href="{{ route('profile.steam') }}" class="marketplace-button secondary">
                            Finish Marketplace Setup to Trade
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="marketplace-button secondary">
                        Sign In to Request Trade
                    </a>
                @endauth
            </div>
        </section>
    </section>
</main>
@endsection