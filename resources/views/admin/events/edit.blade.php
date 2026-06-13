@extends('layouts.admin', [
    'title' => 'Edit Event | CS2 PickLab',
    'pageTitle' => 'Edit Event',
])

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.events.index') }}" class="link-accent">
                ← Back to Events
            </a>

            <h2 class="mt-3 page-title">Edit {{ $event->name }}</h2>
            <p class="page-subtitle">
                Update event details, Pick’em settings, and admin notes.
            </p>
        </div>

        <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-accent">
            Manage Stages
        </a>
    </div>

    <div class="panel">
        <form method="POST" action="{{ route('admin.events.update', $event) }}" class="admin-form-stack">
            @csrf
            @method('PUT')

            @include('admin.events.form', ['event' => $event])

            <div class="form-actions">
                <a href="{{ route('admin.events.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Event
                </button>
            </div>
        </form>
    </div>
@endsection