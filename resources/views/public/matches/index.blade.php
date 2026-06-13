@extends('layouts.app', ['title' => 'Matches | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/matches.css') }}">
@endpush

@section('content')
<section class="matches-page">
    <header class="matches-hero">
        <p class="matches-kicker">Match Board</p>
        <h1>Matches</h1>
        <p>Upcoming and live CS2 match reads.</p>
    </header>

    @if($matches->count() === 0)
        <section class="matches-empty">
            <div class="matches-empty-icon">VS</div>
            <h2>No matches available.</h2>
            <p>Add matches from the admin panel to populate the public match board.</p>
        </section>
    @else
        <section class="matches-list">
            @foreach($matches as $match)
                <a href="{{ route('matches.show', $match) }}" class="match-list-card">
                    <div class="match-list-main">
                        <div>
                            <p class="matches-kicker">
                                {{ $match->event?->name ?: 'Unknown Event' }}
                                @if($match->stage)
                                    · {{ $match->stage->name }}
                                @endif
                            </p>

                            <h2>
                                {{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}
                            </h2>

                            <div class="match-meta-row">
                                <span>{{ strtoupper($match->format) }}</span>
                                <span>{{ ucfirst($match->status) }}</span>
                                <span>{{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}</span>
                            </div>
                        </div>

                        <div class="match-status-box">
                            <strong>{{ ucfirst($match->status) }}</strong>
                            <span>{{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}</span>
                        </div>
                    </div>

                    @if($match->prediction)
                        <div class="match-card-prediction">
                            <strong>{{ $match->prediction->headline }}</strong>
                            <p>{{ $match->prediction->summary }}</p>
                        </div>
                    @endif
                </a>
            @endforeach
        </section>

        <div class="matches-pagination">
            {{ $matches->links() }}
        </div>
    @endif
</section>
@endsection