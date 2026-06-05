@extends('layouts.app', ['title' => 'CS2 PickLab'])

@section('content')
<section class="bg-gradient-to-b from-slate-900 to-slate-950">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="mb-3 text-sm font-bold uppercase tracking-widest text-cyan-400">CS2 picks, match reads, and custom gamer awards</p>
            <h1 class="text-4xl font-black tracking-tight text-white md:text-6xl">
                Smarter CS2 picks before every match.
            </h1>
            <p class="mt-5 max-w-xl text-lg text-slate-300">
                Track team form, upset risk, Pick’em recommendations, and order custom esports awards for your squad, LAN, or Discord tournament.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('pickem.index') }}" class="rounded-lg bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Open Pick’em Assistant</a>
                <a href="{{ route('shop.index') }}" class="rounded-lg border border-slate-700 px-5 py-3 font-bold text-white hover:bg-slate-900">Shop Custom Awards</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
            <h2 class="text-xl font-bold text-white">Today’s match board</h2>
            <div class="mt-5 space-y-4">
                @forelse($upcomingMatches as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-slate-800 bg-slate-950 p-4 hover:border-cyan-400">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-white">{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</p>
                                <p class="text-sm text-slate-400">{{ $match->event?->name }} · {{ strtoupper($match->format) }}</p>
                            </div>
                            @if($match->prediction)
                                <span class="rounded-full bg-slate-800 px-3 py-1 text-sm text-cyan-300">
                                    {{ $match->prediction->confidence_score }}%
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="text-slate-400">No matches loaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <h2 class="mb-5 text-2xl font-black text-white">Latest predictions</h2>
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($latestPredictions as $prediction)
                <a href="{{ route('matches.show', $prediction->match) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-cyan-400">
                    <p class="text-sm text-cyan-400">{{ $prediction->match->teamOne->name }} vs {{ $prediction->match->teamTwo->name }}</p>
                    <h3 class="mt-2 text-lg font-bold text-white">{{ $prediction->headline }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ $prediction->summary }}</p>
                </a>
            @empty
                <p class="text-slate-400">No predictions yet.</p>
            @endforelse
        </div>
    </div>

    <div>
        <h2 class="mb-5 text-2xl font-black text-white">Featured shop</h2>
        <div class="space-y-4">
            @forelse($featuredProducts as $product)
                <a href="{{ route('shop.show', $product) }}" class="block rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-cyan-400">
                    <h3 class="font-bold text-white">{{ $product->name }}</h3>
                    <p class="mt-1 text-sm text-slate-400">{{ $product->short_description }}</p>
                    <p class="mt-3 font-black text-cyan-300">${{ $product->base_price_dollars }}</p>
                </a>
            @empty
                <p class="text-slate-400">No products yet.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
