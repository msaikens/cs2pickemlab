@extends('layouts.admin', [
    'title' => 'Teams | CS2 PickLab',
    'pageTitle' => 'Teams',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Teams</h2>
        <p class="page-subtitle">Manage CS2 teams used for matches, predictions, players, and Pick’em recommendations.</p>
    </div>

    <a href="{{ route('admin.teams.create') }}" class="btn-primary">
        Add Team
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Team</th>
                <th>Region</th>
                <th>Country</th>
                <th>Rating</th>
                <th>Players</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teams as $team)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $team->name }}</p>
                        <p class="text-muted-xs">
                            {{ $team->short_name ?? 'No short name' }} · /teams/{{ $team->slug }}
                        </p>
                    </td>

                    <td class="text-slate-300">{{ $team->region ?? '—' }}</td>
                    <td class="text-slate-300">{{ $team->country ?? '—' }}</td>
                    <td class="price-text">{{ $team->picklab_rating }}</td>
                    <td class="text-slate-300">{{ $team->players_count }}</td>

                    <td>
                        <span class="status-pill">{{ ucfirst($team->status) }}</span>
                    </td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('teams.show', $team) }}" class="btn-small-secondary">
                                View
                            </a>

                            <a href="{{ route('admin.teams.edit', $team) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Delete this team? This can affect players, matches, predictions, and Pick’em records.');">
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
                    <td colspan="7" class="empty-row">No teams yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $teams->links() }}
</div>
@endsection
