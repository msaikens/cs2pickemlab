@extends('layouts.admin', [
    'title' => 'Create Match | CS2 PickLab',
    'pageTitle' => 'Create Match',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.matches.index') }}" class="link-accent">
        ← Back to Matches
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.matches.store') }}" class="space-y-6">
        @csrf

        @include('admin.matches.form', [
            'match' => $match,
            'events' => $events,
            'stages' => $stages,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.matches.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Match</button>
        </div>
    </form>
</div>
@endsection
