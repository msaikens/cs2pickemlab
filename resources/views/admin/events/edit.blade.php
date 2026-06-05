@extends('layouts.admin', [
    'title' => 'Edit Event | CS2 PickLab',
    'pageTitle' => 'Edit Event',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.events.index') }}" class="link-accent">
        ← Back to Events
    </a>

    <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-accent">
        Manage Stages
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.events.form', ['event' => $event])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.events.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Event</button>
        </div>
    </form>
</div>
@endsection
