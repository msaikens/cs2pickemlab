@extends('layouts.app', ['title' => $match->teamOne->name . ' vs ' . $match->teamTwo->name . ' | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/matches.css') }}">
@endpush

@section('content')
<section class="matches-page match-detail-page">
    <header class="match-detail-hero">
        <p class="matches-kicker">
            {{ $match->event?->name ?: 'Unknown Event' }}
            @if($match->stage)
                · {{ $match->stage->name }}
            @endif
        </p>

        <h1>
            {{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}
        </h1>
    </header>

    <section class="match-stat-grid">
        <div class="match-stat-card">
            <span>Status</span>
            <strong>{{ ucfirst($match->status) }}</strong>
        </div>

        <div class="match-stat-card">
            <span>Format</span>
            <strong>{{ strtoupper($match->format) }}</strong>
        </div>

        <div class="match-stat-card">
            <span>Start</span>
            <strong>{{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}</strong>
        </div>
    </section>

    @if($match->prediction)
        <section class="match-prediction-panel">
            <p class="matches-kicker">Prediction</p>

            <h2>{{ $match->prediction->headline }}</h2>

            <div class="match-prediction-grid">
                <div>
                    <span>Predicted Winner</span>
                    <strong>{{ $match->prediction->predictedWinner?->name ?? 'TBD' }}</strong>
                </div>

                <div>
                    <span>Confidence</span>
                    <strong>{{ $match->prediction->confidence_score }}%</strong>
                </div>

                <div>
                    <span>Upset Risk</span>
                    <strong>{{ ucfirst($match->prediction->upset_risk) }}</strong>
                </div>
            </div>

            <p class="match-prediction-summary">
                {{ $match->prediction->summary }}
            </p>

            <p class="match-prediction-reasoning">
                {{ $match->prediction->reasoning }}
            </p>
        </section>
    @endif

    <section class="match-team-grid">
        @foreach([$match->teamOne, $match->teamTwo] as $team)
            <article class="match-team-card">
                <a href="{{ route('teams.show', $team) }}">
                    {{ $team->name }}
                </a>

                <p>
                    {{ $team->summary ?: 'No team summary has been added yet.' }}
                </p>

                <div class="match-roster">
                    <h3>Roster</h3>

                    @if($team->players->count())
                        <ul>
                            @foreach($team->players as $player)
                                <li>
                                    <span>{{ $player->handle }}</span>

                                    @if($player->role)
                                        <strong>{{ $player->role }}</strong>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="match-roster-empty">
                            No players loaded.
                        </p>
                    @endif
                </div>
            </article>
        @endforeach
    </section>
</section>
@endsection