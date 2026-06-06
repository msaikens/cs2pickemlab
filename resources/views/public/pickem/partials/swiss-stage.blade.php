@php
    $stage = $board['stage'];
    $buckets = $board['buckets'];
    $rounds = $board['rounds'];
@endphp

<section class="card">
    <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.3em] text-cyan-400">
                Swiss Stage
            </p>

            <h2 class="mt-2 text-2xl font-black text-white">
                {{ $stage->name }}
            </h2>

            @if(! empty($stage->summary))
                <p class="mt-2 max-w-3xl text-sm text-slate-400">
                    {{ $stage->summary }}
                </p>
            @endif
        </div>

        <div class="text-sm text-slate-400 md:text-right">
            @if(! empty($stage->starts_on))
                <p>{{ \Illuminate\Support\Carbon::parse($stage->starts_on)->format('M j, Y') }}</p>
            @endif

            @if(! empty($stage->ends_on))
                <p>to {{ \Illuminate\Support\Carbon::parse($stage->ends_on)->format('M j, Y') }}</p>
            @endif
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="rounded-xl border border-emerald-400/30 bg-emerald-400/5 p-4">
            <h3 class="text-lg font-black text-emerald-200">Advanced</h3>
            <p class="mt-1 text-sm text-slate-500">Teams reaching 3 wins.</p>

            <div class="mt-4 space-y-4">
                @foreach(['3-0', '3-1', '3-2'] as $record)
                    <div>
                        <p class="mb-2 text-xs font-black uppercase tracking-widest text-slate-500">
                            {{ $record }}
                        </p>

                        <div class="space-y-2">
                            @forelse($buckets['advanced'][$record] ?? [] as $team)
                                <div class="flex items-center justify-between rounded-lg bg-slate-950 px-3 py-2">
                                    <span class="font-bold text-white">{{ $team['name'] }}</span>
                                    <span class="text-sm font-black text-emerald-300">{{ $team['record'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-600">No teams yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-cyan-400/30 bg-cyan-400/5 p-4">
            <h3 class="text-lg font-black text-cyan-200">Still Alive</h3>
            <p class="mt-1 text-sm text-slate-500">Teams still playing in the stage.</p>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach($buckets['alive'] ?? [] as $record => $teams)
                    <div>
                        <p class="mb-2 text-xs font-black uppercase tracking-widest text-slate-500">
                            {{ $record }}
                        </p>

                        <div class="space-y-2">
                            @forelse($teams as $team)
                                <div class="flex items-center justify-between rounded-lg bg-slate-950 px-3 py-2">
                                    <span class="font-bold text-white">{{ $team['name'] }}</span>
                                    <span class="text-sm font-black text-cyan-300">{{ $team['record'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-600">—</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-red-400/30 bg-red-400/5 p-4">
            <h3 class="text-lg font-black text-red-200">Eliminated</h3>
            <p class="mt-1 text-sm text-slate-500">Teams reaching 3 losses.</p>

            <div class="mt-4 space-y-4">
                @foreach(['2-3', '1-3', '0-3'] as $record)
                    <div>
                        <p class="mb-2 text-xs font-black uppercase tracking-widest text-slate-500">
                            {{ $record }}
                        </p>

                        <div class="space-y-2">
                            @forelse($buckets['eliminated'][$record] ?? [] as $team)
                                <div class="flex items-center justify-between rounded-lg bg-slate-950 px-3 py-2">
                                    <span class="font-bold text-white">{{ $team['name'] }}</span>
                                    <span class="text-sm font-black text-red-300">{{ $team['record'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-600">No teams yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="mb-4 text-xl font-black text-white">
            {{ $stage->name }} Matches
        </h3>

        <div class="space-y-6">
            @forelse($rounds as $roundLabel => $roundMatches)
                <div>
                    <h4 class="mb-3 text-sm font-black uppercase tracking-widest text-slate-500">
                        {{ $roundLabel }}
                    </h4>

                    <div class="grid gap-3 lg:grid-cols-2">
                        @forelse($roundMatches as $match)
                            <div class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1 text-right">
                                        <p class="truncate font-black {{ (int) $match->winner_team_id === (int) $match->team_one_id ? 'text-emerald-300' : 'text-white' }}">
                                            {{ $match->teamOne?->name ?? 'TBD' }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 rounded-lg bg-slate-900 px-3 py-2 text-center font-black text-white">
                                        @if($match->status === 'completed')
                                            {{ $match->team_one_score ?? 0 }} - {{ $match->team_two_score ?? 0 }}
                                        @else
                                            vs
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-black {{ (int) $match->winner_team_id === (int) $match->team_two_id ? 'text-emerald-300' : 'text-white' }}">
                                            {{ $match->teamTwo?->name ?? 'TBD' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs font-bold uppercase tracking-widest text-slate-500">
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
                                    <p class="mt-3 text-sm text-slate-400">
                                        {{ $match->summary }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-400">
                                No matches added for {{ $roundLabel }} yet.
                            </p>
                        @endforelse
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-slate-800 bg-slate-950 p-4 text-slate-400">
                    No matches have been added for this stage yet.
                </p>
            @endforelse
        </div>
    </div>
</section>
