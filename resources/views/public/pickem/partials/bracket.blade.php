@php
    $rounds = [
        'Quarterfinals' => [
            'label' => 'Quarterfinals',
            'topPadding' => '',
        ],
        'Semifinals' => [
            'label' => 'Semifinals',
            'topPadding' => 'pt-14',
        ],
        'Grand Final' => [
            'label' => 'Grand Final',
            'topPadding' => 'pt-32',
        ],
    ];

    $final = ($playoffBracket ?? collect())->get('Grand Final', collect())->sortBy('bracket_position')->first();
    $champion = $final?->winner;
@endphp

<div class="overflow-x-auto">
    <div class="min-w-[1100px] rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
        <div class="grid grid-cols-4 gap-6">
            @foreach($rounds as $roundKey => $round)
                @php
                    $roundMatches = ($playoffBracket ?? collect())
                        ->get($roundKey, collect())
                        ->sortBy('bracket_position');
                @endphp

                <div>
                    <h3 class="mb-4 text-center text-sm font-black uppercase tracking-widest text-slate-400">
                        {{ $round['label'] }}
                    </h3>

                    <div class="space-y-6 {{ $round['topPadding'] }}">
                        @forelse($roundMatches as $match)
                            <div class="rounded-xl border border-slate-700 bg-slate-900 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 px-3 py-2">
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                        Slot {{ $match->bracket_position ?: $loop->iteration }}
                                    </span>

                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                        {{ strtoupper($match->format ?? 'bo3') }}
                                    </span>
                                </div>

                                <div class="divide-y divide-slate-800">
                                    <div class="flex items-center justify-between gap-3 px-3 py-3 {{ (int) $match->winner_team_id === (int) $match->team_one_id ? 'bg-emerald-400/10 text-emerald-200' : 'text-white' }}">
                                        <span class="truncate font-black">
                                            {{ $match->teamOne?->name ?? 'TBD' }}
                                        </span>

                                        <span class="shrink-0 rounded bg-slate-950 px-2 py-1 text-sm font-black">
                                            @if($match->status === 'completed')
                                                {{ $match->team_one_score ?? 0 }}
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-3 px-3 py-3 {{ (int) $match->winner_team_id === (int) $match->team_two_id ? 'bg-emerald-400/10 text-emerald-200' : 'text-white' }}">
                                        <span class="truncate font-black">
                                            {{ $match->teamTwo?->name ?? 'TBD' }}
                                        </span>

                                        <span class="shrink-0 rounded bg-slate-950 px-2 py-1 text-sm font-black">
                                            @if($match->status === 'completed')
                                                {{ $match->team_two_score ?? 0 }}
                                            @else
                                                —
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-3 border-t border-slate-800 px-3 py-2 text-xs text-slate-500">
                                    <span>{{ ucfirst($match->status ?? 'scheduled') }}</span>

                                    <span>
                                        @if(! empty($match->starts_at))
                                            {{ \Illuminate\Support\Carbon::parse($match->starts_at)->format('M j, g:i A') }}
                                        @else
                                            Time TBD
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-700 bg-slate-950/60 p-4 text-center text-sm text-slate-500">
                                No {{ strtolower($round['label']) }} matches added.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <div>
                <h3 class="mb-4 text-center text-sm font-black uppercase tracking-widest text-slate-400">
                    Champion
                </h3>

                <div class="pt-44">
                    <div class="rounded-xl border border-cyan-400/40 bg-cyan-400/10 p-5 text-center">
                        <p class="text-xs font-black uppercase tracking-widest text-cyan-300">
                            Winner
                        </p>

                        <p class="mt-3 text-2xl font-black text-white">
                            {{ $champion?->name ?? 'TBD' }}
                        </p>

                        @if($final)
                            <p class="mt-2 text-sm text-slate-400">
                                Grand Final
                                @if($final->status === 'completed')
                                    · {{ $final->team_one_score ?? 0 }}-{{ $final->team_two_score ?? 0 }}
                                @endif
                            </p>
                        @else
                            <p class="mt-2 text-sm text-slate-400">
                                Awaiting final result
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
