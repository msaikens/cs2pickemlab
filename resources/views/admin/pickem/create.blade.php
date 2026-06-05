@extends('layouts.admin', [
    'title' => 'Create Pick’em Recommendation | CS2 PickLab',
    'pageTitle' => 'Create Pick’em Recommendation',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.pickem.index') }}" class="link-accent">
        ← Back to Pick’em
    </a>
</div>

<div class="panel">
    <form method="POST" action="{{ route('admin.pickem.store') }}" class="space-y-6">
        @csrf

        @include('admin.pickem.form', [
            'recommendation' => $recommendation,
            'events' => $events,
            'stages' => $stages,
            'teams' => $teams,
        ])

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.pickem.index') }}" class="btn-secondary-lg">Cancel</a>
            <button type="submit" class="btn-primary-lg">Create Recommendation</button>
        </div>
    </form>
</div>
@endsection
