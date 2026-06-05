@extends('layouts.admin', [
    'title' => 'Pick’em Recommendations | CS2 PickLab',
    'pageTitle' => 'Pick’em',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Pick’em Recommendations</h2>
        <p class="page-subtitle">Manage team recommendations for 3:0, advancement, 0:3, upset watch, and avoid slots.</p>
    </div>

    <a href="{{ route('admin.pickem.create') }}" class="btn-primary">
        Add Recommendation
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Recommendation</th>
                <th>Event</th>
                <th>Team</th>
                <th>Slot</th>
                <th>Confidence</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recommendations as $recommendation)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $recommendation->headline ?? 'Untitled recommendation' }}</p>
                        <p class="text-muted-xs">
                            {{ $recommendation->is_premium ? 'Premium' : 'Free' }}
                            · Risk: {{ ucfirst($recommendation->risk_level) }}
                            · Sort {{ $recommendation->sort_order }}
                        </p>
                    </td>

                    <td>
                        <p class="text-slate-300">{{ $recommendation->event?->name ?? 'No event' }}</p>
                        <p class="text-muted-xs">{{ $recommendation->stage?->name ?? 'No stage' }}</p>
                    </td>

                    <td class="text-slate-300">
                        @if($recommendation->team)
                            <a href="{{ route('teams.show', $recommendation->team) }}" class="text-cyan-300 hover:text-cyan-200">
                                {{ $recommendation->team->name }}
                            </a>
                        @else
                            —
                        @endif
                    </td>

                    <td class="text-slate-300">
                        {{ str_replace('_', ' ', strtoupper($recommendation->slot_type)) }}
                    </td>

                    <td class="price-text">{{ $recommendation->confidence_score }}%</td>

                    <td>
                        <span class="status-pill">{{ ucfirst($recommendation->status) }}</span>
                    </td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            @if($recommendation->team)
                                <a href="{{ route('teams.show', $recommendation->team) }}" class="btn-small-secondary">
                                    Team
                                </a>
                            @endif

                            <a href="{{ route('admin.pickem.edit', $recommendation) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.pickem.destroy', $recommendation) }}" onsubmit="return confirm('Delete this Pick’em recommendation?');">
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
                    <td colspan="7" class="empty-row">No Pick’em recommendations yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $recommendations->links() }}
</div>
@endsection
