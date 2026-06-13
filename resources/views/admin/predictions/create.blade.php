@extends('layouts.admin', [
    'title' => 'Create Prediction | CS2 PickLab',
    'pageTitle' => 'Create Prediction',
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

            <h2 class="prediction-admin-title">Create Prediction</h2>
            <p class="prediction-admin-subtitle">
                Add a match prediction with winner, confidence, upset risk, and Pick’em usage.
            </p>
        </div>
    </div>

    <div class="prediction-admin-panel">
        <form method="POST" action="{{ route('admin.predictions.store') }}" class="prediction-admin-form">
            @csrf

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
                    Create Prediction
                </button>
            </div>
        </form>
    </div>
@endsection