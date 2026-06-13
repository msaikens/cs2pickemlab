@extends('layouts.admin', [
    'title' => 'Edit Event Stage | CS2 PickLab',
    'pageTitle' => 'Edit Event Stage',
])

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.events.stages.index', $event) }}" class="link-accent">
                ← Back to Stages
            </a>

            <h2 class="mt-3 page-title">Edit {{ $stage->name }}</h2>
            <p class="page-subtitle">
                Update this stage for {{ $event->name }}.
            </p>
        </div>
    </div>

    <div class="panel">
        <form method="POST" action="{{ route('admin.events.stages.update', [$event, $stage]) }}" class="admin-form-stack">
            @csrf
            @method('PUT')

            @include('admin.events.stages.form', [
                'event' => $event,
                'stage' => $stage,
            ])

            <div class="form-actions">
                <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-secondary-lg">
                    Cancel
                </a>

                <button type="submit" class="btn-primary-lg">
                    Save Stage
                </button>
            </div>
        </form>
    </div>
@endsection