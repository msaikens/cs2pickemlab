@extends('layouts.admin', [
    'title' => 'Edit Prediction | CS2 PickLab',
    'pageTitle' => 'Edit Prediction',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.predictions.index') }}" class="link-accent">
        ← Back to Predictions
    </a>

    @if($prediction->match)
        <a href="{{ route('matches.show', $prediction->match) }}" class="btn-secondary">
            View Match
        </a>
    @endif
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.predictions.update', $prediction) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.predictions.form', [
            'prediction' => $prediction,
            'matches' => $matches,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.predictions.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Prediction</button>
        </div>
    </form>
</div>
@endsection
