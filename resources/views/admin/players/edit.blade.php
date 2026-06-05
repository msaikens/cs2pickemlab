@extends('layouts.admin', [
    'title' => 'Edit Player | CS2 PickLab',
    'pageTitle' => 'Edit Player',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.players.index') }}" class="link-accent">
        ← Back to Players
    </a>

    @if($player->team)
        <a href="{{ route('teams.show', $player->team) }}" class="btn-secondary">
            View Team
        </a>
    @endif
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.players.update', $player) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.players.form', [
            'player' => $player,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.players.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Player</button>
        </div>
    </form>
</div>
@endsection
