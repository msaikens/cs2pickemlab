@extends('layouts.app', ['title' => $team->name . ' | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teams-show.css') }}">
@endpush

@section('content')
<section class="team-show-page">
    <header class="team-show-hero">
        <div>
            <p class="team-show-kicker">Team Profile</p>

            <h1>{{ $team->name }}</h1>

            <div class="team-show-meta">
                <span>{{ $team->region ?: 'Unknown region' }}</span>
                <span>{{ $team->country ?: 'Unknown country' }}</span>
                <strong>Rating {{ $team->picklab_rating }}</strong>
            </div>
        </div>

        <div class="team-show-rating-card">
            <span>PickLab Rating</span>
            <strong>{{ $team->picklab_rating }}</strong>
        </div>
    </header>

    @if($team->summary)
        <section class="team-summary-card">
            <p>{{ $team->summary }}</p>
        </section>
    @endif

    <div class="team-show-grid">
        <section class="team-panel">
            <div class="team-panel-heading">
                <p class="team-show-kicker">Players</p>
                <h2>Roster</h2>
            </div>

            <div class="team-roster-list">
                @forelse($team->players as $player)
                    <article class="team-player-card">
                        <div class="team-player-avatar">
                            {{ strtoupper(mb_substr($player->handle, 0, 1)) }}
                        </div>

                        <div class="team-player-main">
                            <strong>{{ $player->handle }}</strong>

                            <p>
                                {{ $player->role ?? 'Player' }}

                                @if($player->rating)
                                    <span>· Rating {{ $player->rating }}</span>
                                @endif
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="team-empty-card">
                        <strong>No players loaded.</strong>
                        <p>Add roster data from the admin panel.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="team-panel">
            <div class="team-panel-heading">
                <p class="team-show-kicker">Schedule</p>
                <h2>Recent Matches</h2>
            </div>

            <div class="team-match-list">
                @forelse($recentMatches as $match)
                    <a href="{{ route('matches.show', $match) }}" class="team-match-card">
                        <div>
                            <strong>{{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}</strong>
                            <p>{{ $match->event?->name ?: 'Unknown event' }}</p>
                        </div>

                        <span>{{ $match->starts_at?->format('M j, Y') ?? 'TBD' }}</span>
                    </a>
                @empty
                    <div class="team-empty-card">
                        <strong>No matches loaded.</strong>
                        <p>Recent matches will appear here once imported.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection