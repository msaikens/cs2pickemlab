@extends('layouts.admin', [
    'title' => 'Edit Prediction | CS2 PickLab',
    'pageTitle' => 'Edit Prediction',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-predictions.css') }}">
@endpush

@section('content')
    <div class="prediction-admin-header">
        <div>
            <a href="{{ route('admin.predictions.index') }}" class="link-accent">
                ← Back to Predictions
            </a>

            <h2 class="prediction-admin-title">
                Edit {{ $prediction->headline ?? 'Prediction' }}
            </h2>

            <p class="prediction-admin-subtitle">
                Update match prediction, confidence, risk, status, and reasoning.
            </p>
        </div>

        @if($prediction->match)
            <a href="{{ route('matches.show', $prediction->match) }}" class="btn-secondary">
                View Match
            </a>
        @endif
    </div>

    <div class="prediction-admin-panel">
        <form method="POST" action="{{ route('admin.predictions.update', $prediction) }}" class="prediction-admin-form">
            @csrf
            @method('PUT')

            @include('admin.predictions.form', [
                'prediction' => $prediction,
                'matches' => $matches,
                'teams' => $teams,
            ])

            <div class="prediction-admin-form-actions">
                <a href="{{ route('admin.predictions.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Prediction
                </button>
            </div>
        </form>
    </div>
@endsection