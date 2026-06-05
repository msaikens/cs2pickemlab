@extends('layouts.app', ['title' => 'Shop | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-4xl font-black text-white">Custom Gamer Awards Shop</h1>
    <p class="mt-3 max-w-3xl text-slate-400">
        Custom coins, trophies, and award packs for squads, Discord servers, LAN events, and Pick’em groups.
        Original designs only. No official Counter-Strike, Valve, Steam, tournament, or team marks.
    </p>

    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)
            <a href="{{ route('shop.show', $product) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-cyan-400">
                <div class="flex h-40 items-center justify-center rounded-lg bg-slate-950 text-slate-600">
                    Product Image
                </div>

                <h2 class="mt-5 text-xl font-black text-white">{{ $product->name }}</h2>
                <p class="mt-2 text-sm text-slate-400">{{ $product->short_description }}</p>
                <p class="mt-4 text-2xl font-black text-cyan-300">${{ $product->base_price_dollars }}</p>
            </a>
        @empty
            <p class="text-slate-400">No products available.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>
@endsection
