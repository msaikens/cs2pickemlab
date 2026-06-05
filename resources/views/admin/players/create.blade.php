@extends('layouts.admin', [
    'title' => 'Create Player | CS2 PickLab',
    'pageTitle' => 'Create Player',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.players.index') }}" class="link-accent">
        ← Back to Players
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.players.store') }}" class="space-y-6">
        @csrf

        @include('admin.players.form', [
            'player' => $player,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.players.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Player</button>
        </div>
    </form>
</div>
@endsection
