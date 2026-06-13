@php
    $stage = $board['stage'];
    $buckets = $board['buckets'];
    $rounds = $board['rounds'];
@endphp

<section class="pickem-card pickem-swiss-stage">
    <div class="pickem-stage-header">
        <div>
            <p class="pickem-kicker">Swiss Stage</p>

            <h2>{{ $stage->name }}</h2>

            @if(! empty($stage->summary))
                <p>{{ $stage->summary }}</p>
            @endif
        </div>

        <div class="pickem-stage-dates">
            @if(! empty($stage->starts_on))
                <p>{{ \Illuminate\Support\Carbon::parse($stage->starts_on)->format('M j, Y') }}</p>
            @endif

            @if(! empty($stage->ends_on))
                <p>to {{ \Illuminate\Support\Carbon::parse($stage->ends_on)->format('M j, Y') }}</p>
            @endif
        </div>
    </div>

    <div class="pickem-stage-buckets">
        <section class="pickem-bucket advanced">
            <h3>Advanced</h3>
            <p>Teams reaching 3 wins.</p>

            <div class="pickem-record-groups">
                @foreach(['3-0', '3-1', '3-2'] as $record)
                    <div>
                        <h4>{{ $record }}</h4>

                        <div class="pickem-team-list">
                            @forelse($buckets['advanced'][$record] ?? [] as $team)
                                <div class="pickem-team-row success">
                                    <span>{{ $team['name'] }}</span>
                                    <strong>{{ $team['record'] }}</strong>
                                </div>
                            @empty
                                <p class="pickem-empty-text">No teams yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="pickem-bucket alive">
            <h3>Still Alive</h3>
            <p>Teams still playing in the stage.</p>

            <div class="pickem-alive-grid">
                @foreach($buckets['alive'] ?? [] as $record => $teams)
                    <div>
                        <h4>{{ $record }}</h4>

                        <div class="pickem-team-list">
                            @forelse($teams as $team)
                                <div class="pickem-team-row info">
                                    <span>{{ $team['name'] }}</span>
                                    <strong>{{ $team['record'] }}</strong>
                                </div>
                            @empty
                                <p class="pickem-empty-text">—</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="pickem-bucket eliminated">
            <h3>Eliminated</h3>
            <p>Teams reaching 3 losses.</p>

            <div class="pickem-record-groups">
                @foreach(['2-3', '1-3', '0-3'] as $record)
                    <div>
                        <h4>{{ $record }}</h4>

                        <div class="pickem-team-list">
                            @forelse($buckets['eliminated'][$record] ?? [] as $team)
                                <div class="pickem-team-row danger">
                                    <span>{{ $team['name'] }}</span>
                                    <strong>{{ $team['record'] }}</strong>
                                </div>
                            @empty
                                <p class="pickem-empty-text">No teams yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="pickem-stage-matches">
        <h3>{{ $stage->name }} Matches</h3>

        <div class="pickem-round-list">
            @forelse($rounds as $roundLabel => $roundMatches)
                <section class="pickem-round">
                    <h4>{{ $roundLabel }}</h4>

                    <div class="pickem-match-grid">
                        @forelse($roundMatches as $match)
                            <article class="pickem-match-card">
                                <div class="pickem-match-teams">
                                    <div class="pickem-match-team left">
                                        <span class="{{ (int) $match->winner_team_id === (int) $match->team_one_id ? 'is-winner' : '' }}">
                                            {{ $match->teamOne?->name ?? 'TBD' }}
                                        </span>
                                    </div>

                                    <div class="pickem-match-score">
                                        @if($match->status === 'completed')
                                            {{ $match->team_one_score ?? 0 }} - {{ $match->team_two_score ?? 0 }}
                                        @else
                                            vs
                                        @endif
                                    </div>

                                    <div class="pickem-match-team">
                                        <span class="{{ (int) $match->winner_team_id === (int) $match->team_two_id ? 'is-winner' : '' }}">
                                            {{ $match->teamTwo?->name ?? 'TBD' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="pickem-match-meta">
                                    <span>{{ strtoupper($match->format ?? 'bo3') }} · {{ ucfirst($match->status ?? 'scheduled') }}</span>

                                    <span>
                                        @if(! empty($match->starts_at))
                                            {{ \Illuminate\Support\Carbon::parse($match->starts_at)->format('M j, g:i A') }}
                                        @else
                                            Time TBD
                                        @endif
                                    </span>
                                </div>

                                @if(! empty($match->summary))
                                    <p class="pickem-match-summary">
                                        {{ $match->summary }}
                                    </p>
                                @endif
                            </article>
                        @empty
                            <p class="pickem-empty-box">
                                No matches added for {{ $roundLabel }} yet.
                            </p>
                        @endforelse
                    </div>
                </section>
            @empty
                <p class="pickem-empty-box">
                    No matches have been added for this stage yet.
                </p>
            @endforelse
        </div>
    </section>
</section>