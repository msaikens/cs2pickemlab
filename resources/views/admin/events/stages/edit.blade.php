@extends('layouts.admin', [
    'title' => 'Edit Event Stage | CS2 PickLab',
    'pageTitle' => 'Edit Event Stage',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.stages.index', $event) }}" class="link-accent">
        ← Back to Stages
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.events.stages.update', [$event, $stage]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.events.stages.form', [
            'event' => $event,
            'stage' => $stage,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Stage</button>
        </div>
    </form>
</div>
@endsection
