@extends('layouts.admin', [
    'title' => 'Players | CS2 PickLab',
    'pageTitle' => 'Players',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-players.css') }}">
@endpush

@section('content')
    <div class="player-admin-header">
        <div>
            <h2 class="player-admin-title">Players</h2>
            <p class="player-admin-subtitle">
                Manage roster members, roles, and basic player form metrics.
            </p>
        </div>

        <a href="{{ route('admin.players.create') }}" class="btn-primary">
            Add Player
        </a>
    </div>

    <div class="player-admin-table-wrap">
        <table class="player-admin-table">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Team</th>
                    <th>Role</th>
                    <th>Rating</th>
                    <th>K/D</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($players as $player)
                    <tr>
                        <td>
                            <p class="player-admin-row-title">{{ $player->handle }}</p>
                            <p class="player-admin-muted">
                                {{ $player->real_name ?? 'No real name' }} · {{ $player->country ?? 'No country' }}
                            </p>
                        </td>

                        <td>
                            @if($player->team)
                                <a href="{{ route('admin.teams.edit', $player->team) }}" class="player-admin-link">
                                    {{ $player->team->name }}
                                </a>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if($player->role)
                                <span class="player-role player-role-{{ $player->role }}">
                                    {{ strtoupper($player->role) }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <span class="player-stat">
                                {{ $player->rating ?? '—' }}
                            </span>
                        </td>

                        <td>
                            {{ $player->kd_ratio ?? '—' }}
                        </td>

                        <td>
                            <span class="player-status player-status-{{ $player->status }}">
                                {{ ucfirst($player->status) }}
                            </span>
                        </td>

                        <td class="text-right">
                            <div class="player-admin-actions">
                                @if($player->team)
                                    <a href="{{ route('teams.show', $player->team) }}" class="btn-small-secondary">
                                        Team
                                    </a>
                                @endif

                                <a href="{{ route('admin.players.edit', $player) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.players.destroy', $player) }}"
                                    onsubmit="return confirm('Delete this player?');"
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
                        <td colspan="7" class="player-admin-empty">
                            No players yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($players->hasPages())
        <div class="player-admin-pagination">
            {{ $players->links() }}
        </div>
    @endif
@endsection