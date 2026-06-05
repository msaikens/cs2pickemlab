@extends('layouts.admin', [
    'title' => 'Edit Match | CS2 PickLab',
    'pageTitle' => 'Edit Match',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.matches.index') }}" class="link-accent">
        ← Back to Matches
    </a>

    <div class="flex flex-wrap gap-2">
        @if($match->prediction)
            <a href="{{ route('admin.predictions.edit', $match->prediction) }}" class="btn-accent">
                Edit Prediction
            </a>
        @endif

        <a href="{{ route('matches.show', $match) }}" class="btn-secondary">
            View Match
        </a>
    </div>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.matches.update', $match) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.matches.form', [
            'match' => $match,
            'events' => $events,
            'stages' => $stages,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.matches.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Match</button>
        </div>
    </form>
</div>
@endsection
