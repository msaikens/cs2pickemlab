@extends('layouts.admin', [
    'title' => 'Create Team | CS2 PickLab',
    'pageTitle' => 'Create Team',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.teams.index') }}" class="link-accent">
        ← Back to Teams
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.teams.store') }}" class="space-y-6">
        @csrf

        @include('admin.teams.form', ['team' => $team])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.teams.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Team</button>
        </div>
    </form>
</div>
@endsection
