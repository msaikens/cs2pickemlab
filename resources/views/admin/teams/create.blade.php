@extends('layouts.admin', [
    'title' => 'Create Team | CS2 PickLab',
    'pageTitle' => 'Create Team',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-resource.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/teams.css') }}">
@endpush

@section('content')
<section class="admin-resource-page admin-teams-page">
    <div class="admin-back-row">
        <a href="{{ route('admin.teams.index') }}" class="admin-link">
            ← Back to Teams
        </a>
    </div>

    <section class="admin-panel">
        <form method="POST" action="{{ route('admin.teams.store') }}" class="admin-form">
            @csrf

            @include('admin.teams.form', ['team' => $team])

            <div class="admin-form-actions">
                <a href="{{ route('admin.teams.index') }}" class="admin-button secondary">
                    Cancel
                </a>

                <button type="submit" class="admin-button primary">
                    Create Team
                </button>
            </div>
        </form>
    </section>
</section>
@endsection