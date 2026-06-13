@extends('layouts.admin', [
    'title' => 'Event Stages | CS2 PickLab',
    'pageTitle' => 'Event Stages',
])

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('admin.events.edit', $event) }}" class="link-accent">
                ← Back to {{ $event->name }}
            </a>

            <h2 class="mt-3 page-title">{{ $event->name }} Stages</h2>
            <p class="page-subtitle">
                Manage event stages used for matches and Pick’em recommendations.
            </p>
        </div>

        <a href="{{ route('admin.events.stages.create', $event) }}" class="btn-primary">
            Add Stage
        </a>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Stage</th>
                    <th>Dates</th>
                    <th>Format</th>
                    <th>Pick’em</th>
                    <th>Sort</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($event->stages as $stage)
                    <tr>
                        <td>
                            <p class="table-primary-text">{{ $stage->name }}</p>
                            <p class="text-muted-xs">{{ $stage->slug }}</p>
                        </td>

                        <td>
                            {{ $stage->starts_on?->format('M j, Y') ?? 'TBD' }}

                            @if($stage->ends_on)
                                – {{ $stage->ends_on->format('M j, Y') }}
                            @endif
                        </td>

                        <td>
                            {{ $stage->format ? str_replace('_', ' ', ucfirst($stage->format)) : '—' }}
                        </td>

                        <td>
                            @if($stage->has_pickem)
                                <span class="status-pill status-pill-success">Yes</span>
                            @else
                                <span class="status-pill">No</span>
                            @endif
                        </td>

                        <td>
                            {{ $stage->sort_order }}
                        </td>

                        <td class="text-right">
                            <div class="table-actions">
                                <a href="{{ route('admin.events.stages.edit', [$event, $stage]) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.events.stages.destroy', [$event, $stage]) }}"
                                    onsubmit="return confirm('Delete this stage?');"
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
                        <td colspan="6" class="empty-row">
                            No stages yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection