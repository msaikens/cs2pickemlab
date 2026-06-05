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
                    <p class="font-bold text-white">{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</p>
                    <p class="text-muted-sm">
                        {{ $match->event?->name ?? 'No event' }} · {{ ucfirst($match->status) }}
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
                    <p class="font-bold text-white">{{ $product->name }}</p>
                    <p class="text-muted-sm">
                        {{ ucfirst($product->status) }} · ${{ $product->base_price_dollars }}
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
                    <p class="font-bold text-white">{{ $order->order_number }}</p>
                    <p class="text-muted-sm">
                        {{ $order->customer_email }} · ${{ $order->total_dollars }}
                    </p>
                </div>
            @empty
                <p class="text-muted">No orders yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
