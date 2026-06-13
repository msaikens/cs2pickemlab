@extends('layouts.app', ['title' => 'Teams | CS2 PickLab'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teams-index.css') }}">
@endpush

@section('content')
<section class="teams-page">
    <header class="teams-hero">
        <p class="teams-kicker">Team Database</p>
        <h1>Teams</h1>
        <p>Team profiles, ratings, and PickLab notes.</p>
    </header>

    @if($teams->count() === 0)
        <section class="teams-empty">
            <div class="teams-empty-icon">CS2</div>
            <h2>No teams available.</h2>
            <p>Add teams from the admin panel to populate the public team database.</p>
        </section>
    @else
        <section class="teams-grid">
            @foreach($teams as $team)
                <a href="{{ route('teams.show', $team) }}" class="team-card">
                    <div class="team-card-top">
                        <div>
                            <p class="team-region">{{ $team->region ?: 'Unknown region' }}</p>
                            <h2>{{ $team->name }}</h2>
                        </div>

                        <span class="team-rating">
                            {{ $team->picklab_rating }}
                        </span>
                    </div>

                    <div class="team-meta">
                        <span>{{ $team->players_count }} player(s)</span>
                        <span>PickLab Rating</span>
                    </div>

                    <p class="team-summary">
                        {{ $team->summary ?: 'No team summary has been added yet.' }}
                    </p>

                    <div class="team-card-footer">
                        <span>View profile</span>
                        <strong>→</strong>
                    </div>
                </a>
            @endforeach
        </section>

        <div class="teams-pagination">
            {{ $teams->links() }}
        </div>
    @endif
</section>
@endsection