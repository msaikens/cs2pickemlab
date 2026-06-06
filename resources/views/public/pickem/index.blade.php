@extends('layouts.public', [
    'title' => 'Pick’em | CS2 PickLab',
    'pageTitle' => 'Pick’em',
])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-10">
    <div class="mb-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs font-black uppercase tracking-[0.3em] text-cyan-400">CS2 PickLab</p>

        <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-4xl font-black text-white">Pick’em Hub</h1>
                <p class="mt-3 max-w-3xl text-slate-400">
                    View Pick’em recommendations, event progress, match results, team records, and playoff brackets.
                </p>
            </div>

            @if($event)
                <a href="{{ route('pickem.show', $event) }}" class="inline-flex items-center justify-center rounded-lg bg-cyan-400 px-5 py-3 font-black text-slate-950 hover:bg-cyan-300">
                    View Full Tracker
                </a>
            @endif
        </div>
    </div>

    @if($event)
        <div class="mb-8 grid gap-6 lg:grid-cols-3">
            <section class="card lg:col-span-2">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                    Featured Event
                </p>

                <h2 class="mt-2 text-3xl font-black text-white">
                    {{ $event->name }}
                </h2>

                <p class="mt-3 text-slate-400">
                    {{ ucfirst($event->status ?? 'upcoming') }}

                    @if(! empty($event->starts_on))
                        · Starts {{ \Illuminate\Support\Carbon::parse($event->starts_on)->format('M j, Y') }}
                    @endif

                    @if(! empty($event->ends_on))
                        · Ends {{ \Illuminate\Support\Carbon::parse($event->ends_on)->format('M j, Y') }}
                    @endif
                </p>

                @if(! empty($event->summary))
                    <p class="mt-4 text-slate-300">
                        {{ $event->summary }}
                    </p>
                @endif

                <div class="mt-5">
                    <a href="{{ route('pickem.show', $event) }}" class="link-accent">
                        Open event tracker →
                    </a>
                </div>
            </section>

            <section class="card">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                    Tracker Includes
                </p>

                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <p>• 3-0, advance, and 0-3 recommendations</p>
                    <p>• Team records and advancement status</p>
                    <p>• Completed match scores</p>
                    <p>• Standard playoff bracket display</p>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-4">
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
        </div>

        @if(isset($events) && $events->count() > 1)
            <section class="mt-10">
                <h2 class="mb-4 text-2xl font-black text-white">Other Pick’em Events</h2>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($events as $listedEvent)
                        @continue($listedEvent->id === $event->id)

                        <a href="{{ route('pickem.show', $listedEvent) }}" class="card block hover:border-cyan-400">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                {{ ucfirst($listedEvent->status ?? 'upcoming') }}
                            </p>

                            <h3 class="mt-2 text-xl font-black text-white">
                                {{ $listedEvent->name }}
                            </h3>

                            <p class="mt-3 text-sm text-slate-400">
                                @if(! empty($listedEvent->starts_on))
                                    {{ \Illuminate\Support\Carbon::parse($listedEvent->starts_on)->format('M j, Y') }}
                                @else
                                    Date TBD
                                @endif
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @else
        <section class="card">
            <h2 class="text-2xl font-black text-white">No active Pick’em event yet</h2>
            <p class="mt-3 text-slate-400">
                Once an event is marked as Pick’em-enabled and published recommendations are added, it will appear here.
            </p>
        </section>
    @endif
</section>
@endsection
