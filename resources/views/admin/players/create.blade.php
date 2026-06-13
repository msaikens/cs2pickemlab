@extends('layouts.admin', [
    'title' => 'Create Player | CS2 PickLab',
    'pageTitle' => 'Create Player',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-players.css') }}">
@endpush

@section('content')
    <div class="player-admin-header">
        <div>
            <a href="{{ route('admin.players.index') }}" class="link-accent">
                ← Back to Players
            </a>

            <h2 class="player-admin-title">Create Player</h2>
            <p class="player-admin-subtitle">
                Add a roster member, role, country, and basic player form metrics.
            </p>
        </div>
    </div>

    <div class="player-admin-panel">
        <form method="POST" action="{{ route('admin.players.store') }}" class="player-admin-form">
            @csrf

            @include('admin.players.form', [
                'player' => $player,
                'teams' => $teams,
            ])

            <div class="player-admin-form-actions">
                <a href="{{ route('admin.players.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Player
                </button>
            </div>
        </form>
    </div>
@endsection