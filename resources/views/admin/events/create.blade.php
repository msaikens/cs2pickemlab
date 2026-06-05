@extends('layouts.admin', [
    'title' => 'Create Event | CS2 PickLab',
    'pageTitle' => 'Create Event',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events.index') }}" class="link-accent">
        ← Back to Events
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-6">
        @csrf

        @include('admin.events.form', ['event' => $event])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.events.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Event</button>
        </div>
    </form>
</div>
@endsection
