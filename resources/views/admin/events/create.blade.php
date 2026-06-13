@extends('layouts.admin', [
    'title' => 'Create Event | CS2 PickLab',
    'pageTitle' => 'Create Event',
])

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.events.index') }}" class="link-accent">
                ← Back to Events
            </a>

            <h2 class="mt-3 page-title">Create Event</h2>
            <p class="page-subtitle">
                Add a tournament or Pick’em event to CS2 PickLab.
            </p>
        </div>
    </div>

    <div class="panel">
        <form method="POST" action="{{ route('admin.events.store') }}" class="admin-form-stack">
            @csrf

            @include('admin.events.form', ['event' => $event])

            <div class="form-actions">
                <a href="{{ route('admin.events.index') }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Event
                </button>
            </div>
        </form>
    </div>
@endsection