@extends('layouts.admin', [
    'title' => 'Events | CS2 PickLab',
    'pageTitle' => 'Events',
])

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Events</h2>
            <p class="page-subtitle">
                Manage tournaments, stages, matches, and Pick’em events.
            </p>
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
                            <p class="table-primary-text">{{ $event->name }}</p>
                            <p class="text-muted-xs">
                                {{ $event->organizer ?? 'No organizer' }} · {{ $event->location ?? 'No location' }}
                            </p>
                        </td>

                        <td>
                            {{ $event->starts_on?->format('M j, Y') ?? 'TBD' }}

                            @if($event->ends_on)
                                – {{ $event->ends_on->format('M j, Y') }}
                            @endif
                        </td>

                        <td>
                            <span class="status-pill status-pill-{{ $event->status }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>

                        <td>
                            @if($event->has_pickem)
                                <span class="status-pill status-pill-success">Yes</span>
                            @else
                                <span class="status-pill">No</span>
                            @endif
                        </td>

                        <td>{{ $event->stages_count }}</td>
                        <td>{{ $event->matches_count }}</td>

                        <td class="text-right">
                            <div class="table-actions">
                                <a href="{{ route('admin.events.stages.index', $event) }}" class="btn-small-accent">
                                    Stages
                                </a>

                                <a href="{{ route('admin.events.edit', $event) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.events.destroy', $event) }}"
                                    onsubmit="return confirm('Delete this event? This will also remove stages and may affect matches/Pick’em records.');"
                                >
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
                        <td colspan="7" class="empty-row">
                            No events yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
        <div class="pagination-wrap">
            {{ $events->links() }}
        </div>
    @endif
@endsection