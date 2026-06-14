@extends('layouts.admin', [
    'title' => 'Admin Dashboard | CS2 PickLab',
    'pageTitle' => 'Dashboard',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-resource.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')
<section class="admin-dashboard-page">
    <div class="admin-stat-grid">
        <a href="{{ route('admin.teams.index') }}" class="admin-stat-card">
            <span>Teams</span>
            <strong>{{ $stats['teams'] }}</strong>
        </a>

        <a href="{{ route('admin.players.index') }}" class="admin-stat-card">
            <span>Players</span>
            <strong>{{ $stats['players'] }}</strong>
        </a>

        <a href="{{ route('admin.events.index') }}" class="admin-stat-card">
            <span>Events</span>
            <strong>{{ $stats['events'] }}</strong>
        </a>

        <a href="{{ route('admin.matches.index') }}" class="admin-stat-card">
            <span>Matches</span>
            <strong>{{ $stats['matches'] }}</strong>
        </a>

        <a href="{{ route('admin.predictions.index') }}" class="admin-stat-card">
            <span>Predictions</span>
            <strong>{{ $stats['predictions'] }}</strong>
        </a>

        <a href="{{ route('admin.pickem.index') }}" class="admin-stat-card">
            <span>Pick&#8217;em</span>
            <strong>{{ $stats['pickem'] }}</strong>
        </a>

        <a href="{{ route('admin.products.index') }}" class="admin-stat-card">
            <span>Products</span>
            <strong>{{ $stats['products'] }}</strong>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="admin-stat-card">
            <span>Orders</span>
            <strong>{{ $stats['orders'] }}</strong>
        </a>

        <a href="{{ route('admin.marketplace.listings') }}" class="admin-stat-card wide">
            <span>Marketplace Listings</span>
            <strong>{{ $stats['marketplace_listings_total'] }}</strong>
            <p>
                {{ $stats['marketplace_listings_active'] }} active &middot;
                {{ $stats['marketplace_listings_pending'] }} pending
            </p>
        </a>

        <a href="{{ route('admin.marketplace.trade-requests') }}" class="admin-stat-card wide">
            <span>Trade Requests</span>
            <strong>{{ $stats['trade_requests_total'] }}</strong>
            <p>
                {{ $stats['trade_requests_pending'] }} pending &middot;
                {{ $stats['trade_requests_accepted'] }} accepted
            </p>
        </a>
    </div>

    <div class="admin-dashboard-grid three">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Latest Matches</h2>
                <a href="{{ route('admin.matches.index') }}">Manage</a>
            </div>

            <div class="admin-feed-list">
                @forelse($latestMatches as $match)
                    <article class="admin-feed-item">
                        <strong>
                            {{ $match['team_one_name'] ?? 'TBD' }} vs {{ $match['team_two_name'] ?? 'TBD' }}
                        </strong>

                        <p>
                            {{ $match['event_name'] ?? 'No event' }}

                            @if(! empty($match['stage_name']))
                                &middot; {{ $match['stage_name'] }}
                            @endif

                            &middot; {{ ucfirst($match['status'] ?? 'scheduled') }}
                        </p>

                        <span>
                            {{ strtoupper($match['format'] ?? 'bo3') }}

                            @if(! empty($match['starts_at']))
                                &middot; {{ \Illuminate\Support\Carbon::parse($match['starts_at'])->format('M j, Y g:i A') }}
                            @else
                                &middot; TBD
                            @endif
                        </span>
                    </article>
                @empty
                    <p class="admin-empty-text">No matches yet.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Latest Products</h2>
                <a href="{{ route('admin.products.index') }}">Manage</a>
            </div>

            <div class="admin-feed-list">
                @forelse($latestProducts as $product)
                    <article class="admin-feed-item">
                        <strong>{{ $product['name'] }}</strong>

                        <p>
                            {{ ucfirst($product['status'] ?? 'draft') }}
                            &middot; ${{ number_format(($product['base_price'] ?? 0) / 100, 2) }}
                        </p>
                    </article>
                @empty
                    <p class="admin-empty-text">No products yet.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Latest Orders</h2>
                <a href="{{ route('admin.orders.index') }}">Manage</a>
            </div>

            <div class="admin-feed-list">
                @forelse($latestOrders as $order)
                    <article class="admin-feed-item">
                        <strong>{{ $order['order_number'] }}</strong>

                        <p>
                            {{ $order['customer_email'] }}
                            &middot; ${{ number_format(($order['total'] ?? 0) / 100, 2) }}
                        </p>

                        <span>
                            {{ str_replace('_', ' ', ucfirst($order['status'] ?? 'draft')) }}
                            &middot; {{ ucfirst($order['payment_status'] ?? 'unpaid') }}
                        </span>
                    </article>
                @empty
                    <p class="admin-empty-text">No orders yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="admin-dashboard-grid two">
        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Latest Marketplace Listings</h2>
                <a href="{{ route('admin.marketplace.listings') }}">Manage</a>
            </div>

            <div class="admin-feed-list">
                @forelse($latestListings as $listing)
                    <article class="admin-feed-item">
                        <strong>{{ $listing->market_hash_name }}</strong>

                        <p>
                            {{ $listing->user?->displayName() ?? 'Unknown seller' }}
                            &middot; {{ ucfirst($listing->status ?? 'unknown') }}
                            &middot; {{ $listing->display_price }}
                        </p>

                        <span>
                            Asset: {{ $listing->steam_asset_id ?? '—' }}
                        </span>
                    </article>
                @empty
                    <p class="admin-empty-text">No marketplace listings yet.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-header">
                <h2>Latest Trade Requests</h2>
                <a href="{{ route('admin.marketplace.trade-requests') }}">Manage</a>
            </div>

            <div class="admin-feed-list">
<!-- TEST TXN BUTTON -->
            <form method="POST" action="{{ route('wallet.topup.create') }}">
    @csrf

    <label class="form-label" for="amount_dollars">Wallet Top-Up Amount</label>

    <input
        id="amount_dollars"
        name="amount_dollars"
        type="number"
        min="5"
        max="500"
        step="0.01"
        value="5.00"
        class="form-input"
    >

    <button type="submit" class="btn-primary">
        Add Test Funds
    </button>
</form>
<!-- END TEST TXN BUTTON -->
                @forelse($latestTradeRequests as $tradeRequest)
                    <article class="admin-feed-item">
                        <strong>
                            {{ $tradeRequest->listing?->market_hash_name ?? 'Removed Listing' }}
                        </strong>

                        <p>
                            Buyer: {{ $tradeRequest->buyer?->displayName() ?? 'Unknown' }}
                            &middot; Seller: {{ $tradeRequest->seller?->displayName() ?? 'Unknown' }}
                        </p>

                        <span>
                            {{ ucfirst($tradeRequest->status ?? 'unknown') }}
                            &middot; {{ $tradeRequest->created_at?->diffForHumans() }}
                        </span>
                    </article>
                @empty
                    <p class="admin-empty-text">No trade requests yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection