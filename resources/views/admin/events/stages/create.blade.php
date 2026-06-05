@extends('layouts.admin', [
    'title' => 'Create Event Stage | CS2 PickLab',
    'pageTitle' => 'Create Event Stage',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.stages.index', $event) }}" class="link-accent">
        ← Back to Stages
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.events.stages.store', $event) }}" class="space-y-6">
        @csrf

        @include('admin.events.stages.form', [
            'event' => $event,
            'stage' => $stage,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Stage</button>
        </div>
    </form>
</div>
@endsection
