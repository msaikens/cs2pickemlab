@extends('layouts.admin', [
    'title' => 'Create Event Stage | CS2 PickLab',
    'pageTitle' => 'Create Event Stage',
])

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.events.stages.index', $event) }}" class="link-accent">
                ← Back to Stages
            </a>

            <h2 class="mt-3 page-title">Create Event Stage</h2>
            <p class="page-subtitle">
                Add a stage for {{ $event->name }}. Stages control match grouping, Pick’em behavior, and display order.
            </p>
        </div>
    </div>

    <div class="panel">
        <form method="POST" action="{{ route('admin.events.stages.store', $event) }}" class="admin-form-stack">
            @csrf

            @include('admin.events.stages.form', [
                'event' => $event,
                'stage' => $stage,
            ])

            <div class="form-actions">
                <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Create Stage
                </button>
            </div>
        </form>
    </div>
@endsection