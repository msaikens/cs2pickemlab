@extends('layouts.admin', [
    'title' => 'Edit Match | CS2 PickLab',
    'pageTitle' => 'Edit Match',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-matches.css') }}">
@endpush

@section('content')
    <div class="match-admin-header">
        <div>
            <a href="{{ route('admin.matches.index') }}" class="link-accent">
                ← Back to Matches
            </a>

            <h2 class="match-admin-title">
                Edit {{ $match->teamOne?->name ?? 'TBD' }} vs {{ $match->teamTwo?->name ?? 'TBD' }}
            </h2>

            <p class="match-admin-subtitle">
                Update match timing, teams, score, bracket placement, and prediction context.
            </p>
        </div>

        <div class="match-admin-header-actions">
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

    <div class="match-admin-panel">
        <form method="POST" action="{{ route('admin.matches.update', $match) }}" class="match-admin-form">
            @csrf
            @method('PUT')

            @include('admin.matches.form', [
                'match' => $match,
                'events' => $events,
                'stages' => $stages,
                'teams' => $teams,
            ])

            <div class="match-admin-form-actions">
                <a href="{{ route('admin.matches.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Match
                </button>
            </div>
        </form>
    </div>
@endsection