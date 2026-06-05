@extends('layouts.admin', [
    'title' => 'Edit Pick’em Recommendation | CS2 PickLab',
    'pageTitle' => 'Edit Pick’em Recommendation',
])

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('admin.pickem.index') }}" class="link-accent">
        ← Back to Pick’em
    </a>

    @if($recommendation->team)
        <a href="{{ route('teams.show', $recommendation->team) }}" class="btn-secondary">
            View Team
        </a>
    @endif
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.pickem.update', $recommendation) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.pickem.form', [
            'recommendation' => $recommendation,
            'events' => $events,
            'stages' => $stages,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pickem.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Save Recommendation</button>
        </div>
    </form>
</div>
@endsection
