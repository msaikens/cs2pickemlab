@extends('layouts.admin', [
    'title' => 'Teams | CS2 PickLab',
    'pageTitle' => 'Teams',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-resource.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/teams.css') }}">
@endpush

@section('content')
<section class="admin-resource-page admin-teams-page">
    <header class="admin-resource-header">
        <div>
            <p class="admin-resource-kicker">CS2 Content</p>

            <h2>Teams</h2>

            <p>
                Manage CS2 teams used for matches, predictions, players, and Pick&#8217;em recommendations.
            </p>
        </div>

        <a href="{{ route('admin.teams.create') }}" class="admin-button primary">
            Add Team
        </a>
    </header>

    <section class="admin-table-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Region</th>
                        <th>Country</th>
                        <th>Rating</th>
                        <th>Players</th>
                        <th>Status</th>
                        <th class="align-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($teams as $team)
                        <tr>
                            <td>
                                <strong class="admin-table-title">{{ $team->name }}</strong>

                                <span class="admin-table-subtitle">
                                    {{ $team->short_name ?? 'No short name' }} &middot; /teams/{{ $team->slug }}
                                </span>
                            </td>

                            <td>{{ $team->region ?? '—' }}</td>
                            <td>{{ $team->country ?? '—' }}</td>

                            <td>
                                <span class="admin-price-text">
                                    {{ $team->picklab_rating }}
                                </span>
                            </td>

                            <td>{{ $team->players_count }}</td>

                            <td>
                                <span class="admin-status-pill {{ $team->status }}">
                                    {{ ucfirst($team->status) }}
                                </span>
                            </td>

                            <td class="align-right">
                                <div class="admin-table-actions">
                                    <a href="{{ route('teams.show', $team) }}" class="admin-button small secondary">
                                        View
                                    </a>

                                    <a href="{{ route('admin.teams.edit', $team) }}" class="admin-button small primary">
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.teams.destroy', $team) }}"
                                        onsubmit="return confirm('Delete this team? This can affect players, matches, predictions, and Pick’em records.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="admin-button small danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="admin-empty-row">
                                No teams yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-pagination">
        {{ $teams->links() }}
    </div>
</section>
@endsection