@extends('layouts.admin', [
    'title' => 'Edit Team | CS2 PickLab',
    'pageTitle' => 'Edit Team',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-resource.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/teams.css') }}">
@endpush

@section('content')
<section class="admin-resource-page admin-teams-page">
    <div class="admin-back-row split">
        <a href="{{ route('admin.teams.index') }}" class="admin-link">
            ← Back to Teams
        </a>

        <a href="{{ route('teams.show', $team) }}" class="admin-button secondary">
            View Team
        </a>
    </div>

    <section class="admin-panel">
        <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="admin-form">
            @csrf
            @method('PUT')

            @include('admin.teams.form', ['team' => $team])

            <div class="admin-form-actions">
                <a href="{{ route('admin.teams.index') }}" class="admin-button secondary">
                    Cancel
                </a>

                <button type="submit" class="admin-button primary">
                    Save Team
                </button>
            </div>
        </form>
    </section>
</section>
@endsection