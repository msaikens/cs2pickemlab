@extends('layouts.admin', [
    'title' => 'Predictions | CS2 PickLab',
    'pageTitle' => 'Predictions',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Predictions</h2>
        <p class="page-subtitle">Manage match predictions, confidence scores, upset risk, and Pick’em usage.</p>
    </div>

    <a href="{{ route('admin.predictions.create') }}" class="btn-primary">
        Add Prediction
    </a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Prediction</th>
                <th>Match</th>
                <th>Winner</th>
                <th>Confidence</th>
                <th>Risk</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($predictions as $prediction)
                <tr>
                    <td>
                        <p class="font-bold text-white">{{ $prediction->headline ?? 'Untitled prediction' }}</p>
                        <p class="text-muted-xs">
                            {{ $prediction->is_premium ? 'Premium' : 'Free' }}
                            @if($prediction->published_at)
                                · Published {{ $prediction->published_at->format('M j, Y') }}
                            @endif
                        </p>
                    </td>

                    <td>
                        @if($prediction->match)
                            <p class="text-slate-300">
                                {{ $prediction->match->teamOne->name }} vs {{ $prediction->match->teamTwo->name }}
                            </p>
                        @else
                            <p class="text-slate-500">No match</p>
                        @endif
                    </td>

                    <td class="text-slate-300">{{ $prediction->predictedWinner?->name ?? 'TBD' }}</td>
                    <td class="price-text">{{ $prediction->confidence_score }}%</td>
                    <td class="text-slate-300">{{ ucfirst($prediction->upset_risk) }}</td>

                    <td>
                        <span class="status-pill">{{ ucfirst($prediction->status) }}</span>
                    </td>

                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            @if($prediction->match)
                                <a href="{{ route('matches.show', $prediction->match) }}" class="btn-small-secondary">
                                    View
                                </a>
                            @endif

                            <a href="{{ route('admin.predictions.edit', $prediction) }}" class="btn-small-primary">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('admin.predictions.destroy', $prediction) }}" onsubmit="return confirm('Delete this prediction?');">
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
                    <td colspan="7" class="empty-row">No predictions yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $predictions->links() }}
</div>
@endsection
