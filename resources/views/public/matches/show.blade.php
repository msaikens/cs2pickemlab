@extends('layouts.app', ['title' => $match->teamOne->name . ' vs ' . $match->teamTwo->name . ' | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12">
    <p class="text-sm font-bold uppercase tracking-widest text-cyan-400">
        {{ $match->event?->name }} @if($match->stage) · {{ $match->stage->name }} @endif
    </p>

    <h1 class="mt-3 text-4xl font-black text-white">
        {{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}
    </h1>

    <div class="mt-5 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Status</p>
            <p class="text-xl font-bold text-white">{{ ucfirst($match->status) }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Format</p>
            <p class="text-xl font-bold text-white">{{ strtoupper($match->format) }}</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-sm text-slate-400">Start</p>
            <p class="text-xl font-bold text-white">{{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}</p>
        </div>
    </div>

    @if($match->prediction)
        <div class="mt-8 rounded-2xl border border-cyan-500/40 bg-slate-900 p-6">
            <p class="text-sm font-bold uppercase tracking-widest text-cyan-400">Prediction</p>
            <h2 class="mt-2 text-2xl font-black text-white">{{ $match->prediction->headline }}</h2>

            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <div>
                    <p class="text-sm text-slate-400">Predicted winner</p>
                    <p class="text-lg font-bold text-white">{{ $match->prediction->predictedWinner?->name ?? 'TBD' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Confidence</p>
                    <p class="text-lg font-bold text-white">{{ $match->prediction->confidence_score }}%</p>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Upset risk</p>
                    <p class="text-lg font-bold text-white">{{ ucfirst($match->prediction->upset_risk) }}</p>
                </div>
            </div>

            <p class="mt-5 text-slate-300">{{ $match->prediction->summary }}</p>
            <p class="mt-4 text-slate-400">{{ $match->prediction->reasoning }}</p>
        </div>
    @endif

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        @foreach([$match->teamOne, $match->teamTwo] as $team)
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-5">
                <a href="{{ route('teams.show', $team) }}" class="text-2xl font-black text-white hover:text-cyan-300">{{ $team->name }}</a>
                <p class="mt-2 text-slate-400">{{ $team->summary }}</p>

                <h3 class="mt-5 font-bold text-white">Roster</h3>
                <ul class="mt-2 space-y-1 text-sm text-slate-300">
                    @foreach($team->players as $player)
                        <li>{{ $player->handle }} @if($player->role) · {{ $player->role }} @endif</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>
@endsection
