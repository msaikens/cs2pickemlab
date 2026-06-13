@extends('layouts.admin', [
    'title' => 'Edit Player | CS2 PickLab',
    'pageTitle' => 'Edit Player',
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

            <h2 class="player-admin-title">
                Edit {{ $player->handle }}
            </h2>

            <p class="player-admin-subtitle">
                Update roster assignment, role, status, and player metrics.
            </p>
        </div>

        @if($player->team)
            <a href="{{ route('teams.show', $player->team) }}" class="btn-secondary">
                View Team
            </a>
        @endif
    </div>

    <div class="player-admin-panel">
        <form method="POST" action="{{ route('admin.players.update', $player) }}" class="player-admin-form">
            @csrf
            @method('PUT')

            @include('admin.players.form', [
                'player' => $player,
                'teams' => $teams,
            ])

            <div class="player-admin-form-actions">
                <a href="{{ route('admin.players.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Player
                </button>
            </div>
        </form>
    </div>
@endsection