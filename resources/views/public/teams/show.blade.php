@extends('layouts.app', ['title' => $team->name . ' | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="text-5xl font-black text-white">{{ $team->name }}</h1>
    <p class="mt-3 text-slate-400">{{ $team->region }} · {{ $team->country }} · Rating {{ $team->picklab_rating }}</p>

    @if($team->summary)
        <p class="mt-6 max-w-3xl text-lg text-slate-300">{{ $team->summary }}</p>
    @endif

    <div class="mt-10 grid gap-8 md:grid-cols-2">
        <div>
            <h2 class="mb-4 text-2xl font-black text-white">Roster</h2>
            <div class="space-y-3">
                @forelse($team->players as $player)
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
                        <p class="font-bold text-white">{{ $player->handle }}</p>
                        <p class="text-sm text-slate-400">
                            {{ $player->role ?? 'player' }}
                            @if($player->rating)
                                · Rating {{ $player->rating }}
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-slate-400">No players loaded.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="mb-4 text-2xl font-black text-white">Recent matches</h2>
            <div class="space-y-3">
                @forelse($recentMatches as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-slate-800 bg-slate-900 p-4 hover:border-cyan-400">
                        <p class="font-bold text-white">{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</p>
                        <p class="text-sm text-slate-400">{{ $match->event?->name }} · {{ $match->starts_at?->format('M j, Y') ?? 'TBD' }}</p>
                    </a>
                @empty
                    <p class="text-slate-400">No matches loaded.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
