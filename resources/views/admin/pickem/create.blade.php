@extends('layouts.admin', [
    'title' => 'Edit Pick’em Recommendation | CS2 PickLab',
    'pageTitle' => 'Edit Pick’em Recommendation',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-pickem.css') }}">
@endpush

@section('content')
    <div class="pickem-admin-header">
        <div>
            <a href="{{ route('admin.pickem.index') }}" class="link-accent">
                ← Back to Pick’em
            </a>

            <h2 class="pickem-admin-title">
                Edit {{ $recommendation->headline ?? 'Recommendation' }}
            </h2>

            <p class="pickem-admin-subtitle">
                Update recommendation slot, risk level, confidence, status, and reasoning.
            </p>
        </div>

        @if($recommendation->team)
            <a href="{{ route('teams.show', $recommendation->team) }}" class="btn-secondary">
                View Team
            </a>
        @endif
    </div>

    <div class="pickem-admin-panel">
        <form method="POST" action="{{ route('admin.pickem.update', $recommendation) }}" class="pickem-admin-form">
            @csrf
            @method('PUT')

            @include('admin.pickem.form', [
                'recommendation' => $recommendation,
                'events' => $events,
                'stages' => $stages,
                'teams' => $teams,
            ])

            <div class="pickem-admin-form-actions">
                <a href="{{ route('admin.pickem.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Recommendation
                </button>
            </div>
        </form>
    </div>
@endsection