@extends('layouts.public', [
    'title' => $event->name . ' Pick’em | CS2 PickLab',
    'pageTitle' => $event->name,
])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-10">
    <div class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs font-black uppercase tracking-[0.3em] text-cyan-400">Pick’em Tracker</p>

        <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white">{{ $event->name }}</h1>

                <p class="mt-3 text-slate-400">
                    {{ ucfirst($event->status ?? 'upcoming') }}

                    @if(! empty($event->starts_on))
                        · {{ \Illuminate\Support\Carbon::parse($event->starts_on)->format('M j, Y') }}
                    @endif

                    @if(! empty($event->ends_on))
                        – {{ \Illuminate\Support\Carbon::parse($event->ends_on)->format('M j, Y') }}
                    @endif
                </p>
            </div>

            <a href="{{ route('pickem.index') }}" class="link-accent">
                ← Back to Pick’em Hub
            </a>
        </div>
    </div>

    <div class="grid gap-8 xl:grid-cols-3">
        <div class="space-y-8 xl:col-span-2">
            @forelse($swissStageBoards as $board)
                @include('public.pickem.partials.swiss-stage', [
                    'board' => $board,
                ])
            @empty
                <section class="card">
                    <h2 class="text-2xl font-black text-white">Swiss Stages</h2>
                    <p class="mt-3 text-slate-400">
                        No Stage 1, Stage 2, or Stage 3 Swiss matches have been added yet.
                    </p>
                </section>
            @endforelse

            <section class="card">
                <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-2xl font-black text-white">Playoff Bracket</h2>
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">
                        Quarterfinals → Semifinals → Grand Final
                    </span>
                </div>

                @include('public.pickem.partials.bracket', [
                    'playoffBracket' => $playoffBracket ?? collect(),
                ])
            </section>
        </div>

        <aside class="space-y-6">
            <section class="card">
                <h2 class="text-xl font-black text-white">3-0 Picks</h2>
                <p class="mt-1 text-sm text-slate-500">Safe and risky perfect-record calls.</p>

                <div class="mt-4 space-y-3">
                    @forelse($recommendationBuckets['three_zero'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="text-sm text-slate-400">No 3-0 recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <h2 class="text-xl font-black text-white">Advance Picks</h2>
                <p class="mt-1 text-sm text-slate-500">Teams expected to make it through.</p>

                <div class="mt-4 space-y-3">
                    @forelse($recommendationBuckets['advance'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="text-sm text-slate-400">No advancement recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <h2 class="text-xl font-black text-white">0-3 Picks</h2>
                <p class="mt-1 text-sm text-slate-500">Likely elimination candidates.</p>

                <div class="mt-4 space-y-3">
                    @forelse($recommendationBuckets['zero_three'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="text-sm text-slate-400">No 0-3 recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <h2 class="text-xl font-black text-white">Upset / Avoid</h2>
                <p class="mt-1 text-sm text-slate-500">Volatile teams and traps.</p>

                <div class="mt-4 space-y-3">
                    @forelse($recommendationBuckets['watch_avoid'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="text-sm text-slate-400">No upset or avoid notes published.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</section>
@endsection
