@extends('layouts.public', [
    'title' => $event->name . ' Pick’em | CS2 PickLab',
    'pageTitle' => $event->name,
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pickem.css') }}">
@endpush

@section('content')
<section class="pickem-page">
    <header class="pickem-hero">
        <div>
            <p class="pickem-kicker">Pick’em Tracker</p>

            <h1>{{ $event->name }}</h1>

            <p>
                {{ ucfirst($event->status ?? 'upcoming') }}

                @if(! empty($event->starts_on))
                    · {{ \Illuminate\Support\Carbon::parse($event->starts_on)->format('M j, Y') }}
                @endif

                @if(! empty($event->ends_on))
                    – {{ \Illuminate\Support\Carbon::parse($event->ends_on)->format('M j, Y') }}
                @endif
            </p>
        </div>

        <a href="{{ route('pickem.index') }}" class="pickem-button secondary">
            ← Back to Pick’em Hub
        </a>
    </header>

    <div class="pickem-tracker-grid">
        <main class="pickem-main-column">
            @forelse($swissStageBoards as $board)
                @include('public.pickem.partials.swiss-stage', [
                    'board' => $board,
                ])
            @empty
                <section class="pickem-card">
                    <h2>Swiss Stages</h2>
                    <p class="pickem-card-subtitle">
                        No Stage 1, Stage 2, or Stage 3 Swiss matches have been added yet.
                    </p>
                </section>
            @endforelse

            <section class="pickem-card">
                <div class="pickem-card-topline">
                    <div>
                        <p class="pickem-kicker">Playoffs</p>
                        <h2>Playoff Bracket</h2>
                    </div>

                    <span>Quarterfinals → Semifinals → Grand Final</span>
                </div>

                @include('public.pickem.partials.bracket', [
                    'playoffBracket' => $playoffBracket ?? collect(),
                ])
            </section>
        </main>

        <aside class="pickem-sidebar">
            <section class="pickem-card">
                <h2>3-0 Picks</h2>
                <p class="pickem-card-subtitle">Safe and risky perfect-record calls.</p>

                <div class="pickem-rec-list">
                    @forelse($recommendationBuckets['three_zero'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="pickem-empty-text">No 3-0 recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="pickem-card">
                <h2>Advance Picks</h2>
                <p class="pickem-card-subtitle">Teams expected to make it through.</p>

                <div class="pickem-rec-list">
                    @forelse($recommendationBuckets['advance'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="pickem-empty-text">No advancement recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="pickem-card">
                <h2>0-3 Picks</h2>
                <p class="pickem-card-subtitle">Likely elimination candidates.</p>

                <div class="pickem-rec-list">
                    @forelse($recommendationBuckets['zero_three'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="pickem-empty-text">No 0-3 recommendations published.</p>
                    @endforelse
                </div>
            </section>

            <section class="pickem-card">
                <h2>Upset / Avoid</h2>
                <p class="pickem-card-subtitle">Volatile teams and traps.</p>

                <div class="pickem-rec-list">
                    @forelse($recommendationBuckets['watch_avoid'] ?? collect() as $rec)
                        @include('public.pickem.partials.recommendation-card', ['rec' => $rec])
                    @empty
                        <p class="pickem-empty-text">No upset or avoid notes published.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</section>
@endsection