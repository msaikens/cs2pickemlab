@extends('layouts.app', ['title' => 'Teams | CS2 PickLab'])

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-4xl font-black text-white">Teams</h1>
    <p class="mt-3 text-slate-400">Team profiles, ratings, and PickLab notes.</p>

    <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($teams as $team)
            <a href="{{ route('teams.show', $team) }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5 hover:border-cyan-400">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-white">{{ $team->name }}</h2>
                    <span class="rounded-full bg-slate-950 px-3 py-1 text-sm text-cyan-300">{{ $team->picklab_rating }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-400">{{ $team->region }} · {{ $team->players_count }} players</p>
                <p class="mt-3 text-sm text-slate-300">{{ $team->summary }}</p>
            </a>
        @empty
            <p class="text-slate-400">No teams available.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $teams->links() }}
    </div>
</section>
@endsection
