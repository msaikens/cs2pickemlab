@extends('layouts.admin', [
    'title' => 'Matches | CS2 PickLab',
    'pageTitle' => 'Matches',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Matches</h2>
        <p class="page-subtitle">Manage scheduled, live, and completed CS2 matches.</p>
    </div>

    <a href="{{ route('admin.matches.create') }}" class="btn-primary">
        Add Match
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Match</th>
                <th>Event</th>
                <th>Start</th>
                <th>Status</th>
                <th>Format</th>
                <th>Prediction</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matches as $match)
                <tr>
                    <td>
                        <p class="font-bold text-white">
                            {{ $match->teamOne->name }} vs {{ $match->teamTwo->name }}
                        </p>
                        <p class="text-muted-xs">
                            @if($match->status === 'completed')
                                Score: {{ $match->team_one_score ?? '—' }} - {{ $match->team_two_score ?? '—' }}
                            @else
                                Winner: {{ $match->winner?->name ?? 'TBD' }}
                            @endif
                        </p>
                    </td>

                    <td>
                        <p class="text-slate-300">{{ $match->event?->name ?? 'No event' }}</p>
                        <p class="text-muted-xs">{{ $match->stage?->name ?? 'No stage' }}</p>
                    </td>

                    <td class="text-slate-300">
                        {{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}
                    </td>

                    <td>
                        <span class="status-pill">{{ ucfirst($match->status) }}</span>
                    </td>

                    <td class="text-slate-300">{{ strtoupper($match->format) }}</td>

                    <td class="text-slate-300">
                        @if($match->prediction)
                            {{ $match->prediction->confidence_score }}%
                        @else
                            —
                        @endif
                    </td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('matches.show', $match) }}" class="btn-small-secondary">
                                View
                            </a>

                            <a href="{{ route('admin.matches.edit', $match) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Delete this match? This will also delete its prediction.');">
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
                    <td colspan="7" class="empty-row">No matches yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $matches->links() }}
</div>
@endsection
