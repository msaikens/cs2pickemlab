@extends('layouts.admin', [
    'title' => 'Admin Dashboard | CS2 PickLab',
    'pageTitle' => 'Dashboard',
])

@section('content')
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('admin.teams.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Teams</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['teams'] }}</p>
    </a>

    <a href="{{ route('admin.players.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Players</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['players'] }}</p>
    </a>

    <a href="{{ route('admin.events.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Events</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['events'] }}</p>
    </a>

    <a href="{{ route('admin.matches.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Matches</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['matches'] }}</p>
    </a>

    <a href="{{ route('admin.predictions.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Predictions</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['predictions'] }}</p>
    </a>

    <a href="{{ route('admin.pickem.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Pick’em</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['pickem'] }}</p>
    </a>

    <a href="{{ route('admin.products.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Products</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['products'] }}</p>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Orders</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['orders'] }}</p>
    </a>

    <a href="{{ route('admin.marketplace.listings') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Marketplace Listings</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['marketplace_listings_total'] }}</p>
        <p class="mt-2 text-sm text-slate-400">
            {{ $stats['marketplace_listings_active'] }} active ·
            {{ $stats['marketplace_listings_pending'] }} pending
        </p>
    </a>

    <a href="{{ route('admin.marketplace.trade-requests') }}" class="card hover:border-cyan-400">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Trade Requests</p>
        <p class="mt-2 text-4xl font-black text-white">{{ $stats['trade_requests_total'] }}</p>
        <p class="mt-2 text-sm text-slate-400">
            {{ $stats['trade_requests_pending'] }} pending ·
            {{ $stats['trade_requests_accepted'] }} accepted
        </p>
    </a>
</div>

<div class="mt-10 grid gap-8 lg:grid-cols-3">
    <section class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Latest Matches</h2>
            <a href="{{ route('admin.matches.index') }}" class="link-accent">Manage</a>
        </div>

        <div class="space-y-3">
            @forelse($latestMatches as $match)
                <div class="rounded-lg bg-slate-950 p-3">
                    <p class="font-bold text-white">
                        {{ $match['team_one_name'] ?? 'TBD' }} vs {{ $match['team_two_name'] ?? 'TBD' }}
                    </p>

                    <p class="text-muted-sm">
                        {{ $match['event_name'] ?? 'No event' }}

                        @if(! empty($match['stage_name']))
                            · {{ $match['stage_name'] }}
                        @endif

                        · {{ ucfirst($match['status'] ?? 'scheduled') }}
                    </p>

                    <p class="text-muted-xs">
                        {{ strtoupper($match['format'] ?? 'bo3') }}

                        @if(! empty($match['starts_at']))
                            · {{ \Illuminate\Support\Carbon::parse($match['starts_at'])->format('M j, Y g:i A') }}
                        @else
                            · TBD
                        @endif
                    </p>
                </div>
            @empty
                <p class="text-muted">No matches yet.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Latest Products</h2>
            <a href="{{ route('admin.products.index') }}" class="link-accent">Manage</a>
        </div>

        <div class="space-y-3">
            @forelse($latestProducts as $product)
                <div class="rounded-lg bg-slate-950 p-3">
                    <p class="font-bold text-white">{{ $product['name'] }}</p>

                    <p class="text-muted-sm">
                        {{ ucfirst($product['status'] ?? 'draft') }}
                        · ${{ number_format(($product['base_price'] ?? 0) / 100, 2) }}
                    </p>
                </div>
            @empty
                <p class="text-muted">No products yet.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Latest Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="link-accent">Manage</a>
        </div>

        <div class="space-y-3">
            @forelse($latestOrders as $order)
                <div class="rounded-lg bg-slate-950 p-3">
                    <p class="font-bold text-white">{{ $order['order_number'] }}</p>

                    <p class="text-muted-sm">
                        {{ $order['customer_email'] }}
                        · ${{ number_format(($order['total'] ?? 0) / 100, 2) }}
                    </p>

                    <p class="text-muted-xs">
                        {{ str_replace('_', ' ', ucfirst($order['status'] ?? 'draft')) }}
                        · {{ ucfirst($order['payment_status'] ?? 'unpaid') }}
                    </p>
                </div>
            @empty
                <p class="text-muted">No orders yet.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="mt-10 grid gap-8 lg:grid-cols-2">
    <section class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Latest Marketplace Listings</h2>
            <a href="{{ route('admin.marketplace.listings') }}" class="link-accent">Manage</a>
        </div>

        <div class="space-y-3">
            @forelse($latestListings as $listing)
                <div class="rounded-lg bg-slate-950 p-3">
                    <p class="font-bold text-white">{{ $listing->market_hash_name }}</p>

                    <p class="text-muted-sm">
                        {{ $listing->user?->displayName() ?? 'Unknown seller' }}
                        · {{ ucfirst($listing->status ?? 'unknown') }}
                        · {{ $listing->display_price }}
                    </p>

                    <p class="text-muted-xs">
                        Asset: {{ $listing->steam_asset_id ?? '—' }}
                    </p>
                </div>
            @empty
                <p class="text-muted">No marketplace listings yet.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Latest Trade Requests</h2>
            <a href="{{ route('admin.marketplace.trade-requests') }}" class="link-accent">Manage</a>
        </div>

        <div class="space-y-3">
            @forelse($latestTradeRequests as $tradeRequest)
                <div class="rounded-lg bg-slate-950 p-3">
                    <p class="font-bold text-white">
                        {{ $tradeRequest->listing?->market_hash_name ?? 'Removed Listing' }}
                    </p>

                    <p class="text-muted-sm">
                        Buyer: {{ $tradeRequest->buyer?->displayName() ?? 'Unknown' }}
                        · Seller: {{ $tradeRequest->seller?->displayName() ?? 'Unknown' }}
                    </p>

                    <p class="text-muted-xs">
                        {{ ucfirst($tradeRequest->status ?? 'unknown') }}
                        · {{ $tradeRequest->created_at?->diffForHumans() }}
                    </p>
                </div>
            @empty
                <p class="text-muted">No trade requests yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection