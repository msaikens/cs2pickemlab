@extends('layouts.admin', [
    'title' => 'Edit Team | CS2 PickLab',
    'pageTitle' => 'Edit Team',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.teams.index') }}" class="link-accent">
        ← Back to Teams
    </a>

    <a href="{{ route('teams.show', $team) }}" class="btn-secondary">
        View Team
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.teams.form', ['team' => $team])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.teams.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Team</button>
        </div>
    </form>
</div>
@endsection
