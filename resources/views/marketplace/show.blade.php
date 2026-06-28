@extends('layouts.public', [
    'title' => $listing->item_name . ' | CS2 PickLab Marketplace',
    'pageTitle' => 'Marketplace Listing',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-browse.css') }}">
@endpush

@section('content')
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

<section class="marketplace-browse-page">
    <header class="marketplace-browse-hero">
        <div>
            <p class="marketplace-browse-kicker">Marketplace Listing</p>
            <h1>{{ $listing->item_name }}</h1>
            <p>
                {{ ucfirst($listing->listing_type) }} listing from {{ $sellerName }}.
            </p>
        </div>

        <a href="{{ route('marketplace.index') }}" class="marketplace-browse-button secondary">
            Back to Marketplace
        </a>
    </header>

    <section class="marketplace-detail-grid">
        <article class="marketplace-detail-image-card">
            @if($listing->image_url)
                <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}">
            @else
                <div class="marketplace-listing-image-placeholder large">
                    No Image
                </div>
            @endif
        </article>

        <article class="marketplace-browse-card marketplace-detail-card">
            <div class="marketplace-listing-tags">
                <span>{{ ucfirst($listing->listing_type) }}</span>

                @if($listing->rarity)
                    <span>{{ $listing->rarity }}</span>
                @endif

                @if($listing->status)
                    <span>{{ ucfirst($listing->status) }}</span>
                @endif
            </div>

            <h2>{{ $listing->market_hash_name ?: $listing->item_name }}</h2>

            <div class="marketplace-detail-rows">
                <div>
                    <span>Seller</span>
                    <strong>{{ $sellerName }}</strong>
                </div>

                @if($listing->weapon_type)
                    <div>
                        <span>Type</span>
                        <strong>{{ $listing->weapon_type }}</strong>
                    </div>
                @endif

                @if($listing->wear_name)
                    <div>
                        <span>Wear</span>
                        <strong>{{ $listing->wear_name }}</strong>
                    </div>
                @endif

                @if(! is_null($listing->float_value))
                    <div>
                        <span>Float</span>
                        <strong>{{ $listing->float_value }}</strong>
                    </div>
                @endif

                <div>
                    <span>Listing Type</span>
                    <strong>{{ ucfirst($listing->listing_type) }}</strong>
                </div>

                <div>
                    <span>Price / Terms</span>

                    @if($listing->listing_type === 'sale' && $listing->asking_price_cents)
                        <strong>${{ number_format($listing->asking_price_cents / 100, 2) }}</strong>
                    @else
                        <strong>Trade Offers</strong>
                    @endif
                </div>
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

            <div class="marketplace-detail-actions">
                @guest
                    <a href="{{ route('login') }}" class="marketplace-browse-button primary">
                        Sign In to Trade or Buy
                    </a>

                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="marketplace-browse-button secondary">
                            Create Account
                        </a>
                    @endif
                @else
                    @if(auth()->id() === $listing->user_id)
                        <a href="{{ route('marketplace.listings.index') }}" class="marketplace-browse-button secondary">
                            Manage My Listings
                        </a>
                    @else
                        <button class="marketplace-browse-button primary" type="button" disabled>
                            Trade / Purchase Flow Coming Next
                        </button>

                        <p class="marketplace-detail-note">
                            Your account can browse listings now. The next step is wiring offer, trade, and purchase requests.
                        </p>
                    @endif
                @endguest
            </div>
        </article>
    </section>
</section>
@endsection