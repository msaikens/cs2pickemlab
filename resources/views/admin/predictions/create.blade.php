@extends('layouts.admin', [
    'title' => 'Create Prediction | CS2 PickLab',
    'pageTitle' => 'Create Prediction',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.predictions.index') }}" class="link-accent">
        ← Back to Predictions
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.predictions.store') }}" class="space-y-6">
        @csrf

        @include('admin.predictions.form', [
            'prediction' => $prediction,
            'matches' => $matches,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.predictions.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Prediction</button>
        </div>
    </form>
</div>
@endsection
