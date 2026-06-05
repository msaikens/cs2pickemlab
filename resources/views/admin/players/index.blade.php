@extends('layouts.admin', [
    'title' => 'Players | CS2 PickLab',
    'pageTitle' => 'Players',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Players</h2>
        <p class="page-subtitle">Manage roster members, roles, and basic player form metrics.</p>
    </div>

    <a href="{{ route('admin.players.create') }}" class="btn-primary">
        Add Player
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
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
                        <p class="font-bold text-white">{{ $player->handle }}</p>
                        <p class="text-muted-xs">
                            {{ $player->real_name ?? 'No real name' }} · {{ $player->country ?? 'No country' }}
                        </p>
                    </td>

                    <td class="text-slate-300">
                        @if($player->team)
                            <a href="{{ route('admin.teams.edit', $player->team) }}" class="text-cyan-300 hover:text-cyan-200">
                                {{ $player->team->name }}
                            </a>
                        @else
                            —
                        @endif
                    </td>

                    <td class="text-slate-300">{{ $player->role ? strtoupper($player->role) : '—' }}</td>
                    <td class="price-text">{{ $player->rating ?? '—' }}</td>
                    <td class="text-slate-300">{{ $player->kd_ratio ?? '—' }}</td>

                    <td>
                        <span class="status-pill">{{ ucfirst($player->status) }}</span>
                    </td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            @if($player->team)
                                <a href="{{ route('teams.show', $player->team) }}" class="btn-small-secondary">
                                    Team
                                </a>
                            @endif

                            <a href="{{ route('admin.players.edit', $player) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.players.destroy', $player) }}" onsubmit="return confirm('Delete this player?');">
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
                    <td colspan="7" class="empty-row">No players yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $players->links() }}
</div>
@endsection
