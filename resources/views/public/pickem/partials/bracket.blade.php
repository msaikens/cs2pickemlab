@php
    $rounds = [
        'Quarterfinals' => [
            'label' => 'Quarterfinals',
            'class' => '',
        ],
        'Semifinals' => [
            'label' => 'Semifinals',
            'class' => 'offset-md',
        ],
        'Grand Final' => [
            'label' => 'Grand Final',
            'class' => 'offset-lg',
        ],
    ];

    $final = ($playoffBracket ?? collect())->get('Grand Final', collect())->sortBy('bracket_position')->first();
    $champion = $final?->winner;
@endphp

<div class="pickem-bracket-scroll">
    <div class="pickem-bracket">
        <div class="pickem-bracket-grid">
            @foreach($rounds as $roundKey => $round)
                @php
                    $roundMatches = ($playoffBracket ?? collect())
                        ->get($roundKey, collect())
                        ->sortBy('bracket_position');
                @endphp

                <section class="pickem-bracket-round">
                    <h3>{{ $round['label'] }}</h3>

                    <div class="pickem-bracket-match-list {{ $round['class'] }}">
                        @forelse($roundMatches as $match)
                            <article class="pickem-bracket-match">
                                <div class="pickem-bracket-match-top">
                                    <span>Slot {{ $match->bracket_position ?: $loop->iteration }}</span>
                                    <span>{{ strtoupper($match->format ?? 'bo3') }}</span>
                                </div>

                                <div class="pickem-bracket-teams">
                                    <div class="pickem-bracket-team {{ (int) $match->winner_team_id === (int) $match->team_one_id ? 'winner' : '' }}">
                                        <span>{{ $match->teamOne?->name ?? 'TBD' }}</span>

                                        <strong>
                                            @if($match->status === 'completed')
                                                {{ $match->team_one_score ?? 0 }}
                                            @else
                                                —
                                            @endif
                                        </strong>
                                    </div>

                                    <div class="pickem-bracket-team {{ (int) $match->winner_team_id === (int) $match->team_two_id ? 'winner' : '' }}">
                                        <span>{{ $match->teamTwo?->name ?? 'TBD' }}</span>

                                        <strong>
                                            @if($match->status === 'completed')
                                                {{ $match->team_two_score ?? 0 }}
                                            @else
                                                —
                                            @endif
                                        </strong>
                                    </div>
                                </div>

                                <div class="pickem-bracket-match-footer">
                                    <span>{{ ucfirst($match->status ?? 'scheduled') }}</span>

                                    <span>
                                        @if(! empty($match->starts_at))
                                            {{ \Illuminate\Support\Carbon::parse($match->starts_at)->format('M j, g:i A') }}
                                        @else
                                            Time TBD
                                        @endif
                                    </span>
                                </div>
                            </article>
                        @empty
                            <div class="pickem-bracket-empty">
                                No {{ strtolower($round['label']) }} matches added.
                            </div>
                        @endforelse
                    </div>
                </section>
            @endforeach

            <section class="pickem-bracket-round">
                <h3>Champion</h3>

                <div class="pickem-champion-wrap">
                    <div class="pickem-champion-card">
                        <p>Winner</p>

                        <strong>{{ $champion?->name ?? 'TBD' }}</strong>

                        @if($final)
                            <span>
                                Grand Final

                                @if($final->status === 'completed')
                                    · {{ $final->team_one_score ?? 0 }}-{{ $final->team_two_score ?? 0 }}
                                @endif
                            </span>
                        @else
                            <span>Awaiting final result</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>