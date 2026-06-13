@extends('layouts.public', [
    'title' => 'Pick’em | CS2 PickLab',
    'pageTitle' => 'Pick’em',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pickem.css') }}">
@endpush

@section('content')
<section class="pickem-page">
    <header class="pickem-hero">
        <div>
            <p class="pickem-kicker">CS2 PickLab</p>

            <h1>Pick’em Hub</h1>

            <p>
                View Pick’em recommendations, event progress, match results, team records, and playoff brackets.
            </p>
        </div>

        @if($event)
            <a href="{{ route('pickem.show', $event) }}" class="pickem-button primary">
                View Full Tracker
            </a>
        @endif
    </header>

    @if($event)
        <div class="pickem-feature-grid">
            <section class="pickem-card pickem-feature-card">
                <p class="pickem-muted-label">Featured Event</p>

                <h2>{{ $event->name }}</h2>

                <p class="pickem-event-date">
                    {{ ucfirst($event->status ?? 'upcoming') }}

                    @if(! empty($event->starts_on))
                        · Starts {{ \Illuminate\Support\Carbon::parse($event->starts_on)->format('M j, Y') }}
                    @endif

                    @if(! empty($event->ends_on))
                        · Ends {{ \Illuminate\Support\Carbon::parse($event->ends_on)->format('M j, Y') }}
                    @endif
                </p>

                @if(! empty($event->summary))
                    <p class="pickem-summary">
                        {{ $event->summary }}
                    </p>
                @endif

                <a href="{{ route('pickem.show', $event) }}" class="pickem-link">
                    Open event tracker →
                </a>
            </section>

            <section class="pickem-card">
                <p class="pickem-muted-label">Tracker Includes</p>

                <div class="pickem-includes-list">
                    <p>3-0, advance, and 0-3 recommendations</p>
                    <p>Team records and advancement status</p>
                    <p>Completed match scores</p>
                    <p>Standard playoff bracket display</p>
                </div>
            </section>
        </div>

        <div class="pickem-rec-grid">
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
        </div>

        @if(isset($events) && $events->count() > 1)
            <section class="pickem-other-events">
                <div class="pickem-section-heading">
                    <p class="pickem-kicker">Archive</p>
                    <h2>Other Pick’em Events</h2>
                </div>

                <div class="pickem-event-grid">
                    @foreach($events as $listedEvent)
                        @continue($listedEvent->id === $event->id)

                        <a href="{{ route('pickem.show', $listedEvent) }}" class="pickem-event-card">
                            <p class="pickem-muted-label">
                                {{ ucfirst($listedEvent->status ?? 'upcoming') }}
                            </p>

                            <h3>{{ $listedEvent->name }}</h3>

                            <p>
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
        <section class="pickem-card pickem-empty-state">
            <h2>No active Pick’em event yet</h2>

            <p>
                Once an event is marked as Pick’em-enabled and published recommendations are added, it will appear here.
            </p>
        </section>
    @endif
</section>
@endsection