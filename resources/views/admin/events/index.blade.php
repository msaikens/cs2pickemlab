@extends('layouts.admin', [
    'title' => 'Events | CS2 PickLab',
    'pageTitle' => 'Events',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Events</h2>
        <p class="page-subtitle">Manage tournaments, stages, matches, and Pick’em events.</p>
    </div>

    <a href="{{ route('admin.events.create') }}" class="btn-primary">
        Add Event
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Event</th>
                <th>Dates</th>
                <th>Status</th>
                <th>Pick’em</th>
                <th>Stages</th>
                <th>Matches</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $event->name }}</p>
                        <p class="text-muted-xs">
                            {{ $event->organizer ?? 'No organizer' }} · {{ $event->location ?? 'No location' }}
                        </p>
                    </td>

                    <td class="text-slate-300">
                        {{ $event->starts_on?->format('M j, Y') ?? 'TBD' }}
                        @if($event->ends_on)
                            – {{ $event->ends_on->format('M j, Y') }}
                        @endif
                    </td>

                    <td>
                        <span class="status-pill">{{ ucfirst($event->status) }}</span>
                    </td>

                    <td class="text-slate-300">{{ $event->has_pickem ? 'Yes' : 'No' }}</td>
                    <td class="text-slate-300">{{ $event->stages_count }}</td>
                    <td class="text-slate-300">{{ $event->matches_count }}</td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-small-accent">
                                Stages
                            </a>

                            <a href="{{ route('admin.events.edit', $event) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This will also remove stages and may affect matches/Pick’em records.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-small-danger">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">No events yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $events->links() }}
</div>
@endsection
