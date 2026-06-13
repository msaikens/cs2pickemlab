@extends('layouts.admin', [
    'title' => 'Predictions | CS2 PickLab',
    'pageTitle' => 'Predictions',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-predictions.css') }}">
@endpush

@section('content')
    <div class="prediction-admin-header">
        <div>
            <h2 class="prediction-admin-title">Predictions</h2>
            <p class="prediction-admin-subtitle">
                Manage match predictions, confidence scores, upset risk, and Pick’em usage.
            </p>
        </div>

        <a href="{{ route('admin.predictions.create') }}" class="btn-primary">
            Add Prediction
        </a>
    </div>

    <div class="prediction-admin-table-wrap">
        <table class="prediction-admin-table">
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
                            <p class="prediction-admin-row-title">
                                {{ $prediction->headline ?? 'Untitled prediction' }}
                            </p>

                            <p class="prediction-admin-muted">
                                <span class="prediction-access prediction-access-{{ $prediction->is_premium ? 'premium' : 'free' }}">
                                    {{ $prediction->is_premium ? 'Premium' : 'Free' }}
                                </span>

                                @if($prediction->published_at)
                                    <span>Published {{ $prediction->published_at->format('M j, Y') }}</span>
                                @endif
                            </p>
                        </td>

                        <td>
                            @if($prediction->match)
                                <p class="prediction-admin-secondary-text">
                                    {{ $prediction->match->teamOne?->name ?? 'TBD' }} vs {{ $prediction->match->teamTwo?->name ?? 'TBD' }}
                                </p>
                            @else
                                <p class="prediction-admin-muted">No match</p>
                            @endif
                        </td>

                        <td>
                            {{ $prediction->predictedWinner?->name ?? 'TBD' }}
                        </td>

                        <td>
                            <span class="prediction-confidence">
                                {{ $prediction->confidence_score }}%
                            </span>
                        </td>

                        <td>
                            <span class="prediction-risk prediction-risk-{{ $prediction->upset_risk }}">
                                {{ ucfirst($prediction->upset_risk) }}
                            </span>
                        </td>

                        <td>
                            <span class="prediction-status prediction-status-{{ $prediction->status }}">
                                {{ ucfirst($prediction->status) }}
                            </span>
                        </td>

                        <td class="text-right">
                            <div class="prediction-admin-actions">
                                @if($prediction->match)
                                    <a href="{{ route('matches.show', $prediction->match) }}" class="btn-small-secondary">
                                        View
                                    </a>
                                @endif

                                <a href="{{ route('admin.predictions.edit', $prediction) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.predictions.destroy', $prediction) }}"
                                    onsubmit="return confirm('Delete this prediction?');"
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
                        <td colspan="7" class="prediction-admin-empty">
                            No predictions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($predictions->hasPages())
        <div class="prediction-admin-pagination">
            {{ $predictions->links() }}
        </div>
    @endif
@endsection