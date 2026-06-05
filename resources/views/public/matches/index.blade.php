@extends('layouts.app', ['title' => 'Matches | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-4xl font-black text-white">Matches</h1>
    <p class="mt-3 text-slate-400">Upcoming and live CS2 match reads.</p>

    <div class="mt-8 grid gap-4">
        @forelse($matches as $match)
            <a href="{{ route('matches.show', $match) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-cyan-400">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</h2>
                        <p class="text-sm text-slate-400">{{ $match->event?->name }} · {{ $match->stage?->name }} · {{ strtoupper($match->format) }}</p>
                    </div>
                    <div class="text-right text-sm">
                        <p class="font-bold text-cyan-300">{{ ucfirst($match->status) }}</p>
                        <p class="text-slate-400">{{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}</p>
                    </div>
                </div>

                @if($match->prediction)
                    <div class="mt-4 rounded-lg bg-slate-950 p-4">
                        <p class="font-bold text-white">{{ $match->prediction->headline }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ $match->prediction->summary }}</p>
                    </div>
                @endif
            </a>
        @empty
            <p class="text-slate-400">No matches available.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $matches->links() }}
    </div>
</section>
@endsection
