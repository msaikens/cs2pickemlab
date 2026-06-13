@extends('layouts.admin', [
    'title' => 'Matches | CS2 PickLab',
    'pageTitle' => 'Matches',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-matches.css') }}">
@endpush

@section('content')
    <div class="match-admin-header">
        <div>
            <h2 class="match-admin-title">Matches</h2>
            <p class="match-admin-subtitle">
                Manage scheduled, live, completed, Swiss, and playoff bracket CS2 matches.
            </p>
        </div>

        <a href="{{ route('admin.matches.create') }}" class="btn-primary">
            Add Match
        </a>
    </div>

    <div class="match-admin-table-wrap">
        <table class="match-admin-table">
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Event</th>
                    <th>Start</th>
                    <th>Status</th>
                    <th>Format</th>
                    <th>Bracket</th>
                    <th>Prediction</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($matches as $match)
                    <tr>
                        <td>
                            <p class="match-admin-row-title">
                                {{ $match->teamOne?->name ?? 'TBD' }} vs {{ $match->teamTwo?->name ?? 'TBD' }}
                            </p>

                            <p class="match-admin-muted">
                                @if($match->status === 'completed')
                                    Score: {{ $match->team_one_score ?? '—' }} - {{ $match->team_two_score ?? '—' }}
                                @else
                                    Winner: {{ $match->winner?->name ?? 'TBD' }}
                                @endif
                            </p>
                        </td>

                        <td>
                            <p class="match-admin-secondary-text">
                                {{ $match->event?->name ?? 'No event' }}
                            </p>

                            <p class="match-admin-muted">
                                {{ $match->stage?->name ?? 'No stage' }}
                            </p>
                        </td>

                        <td>
                            {{ $match->starts_at?->format('M j, Y g:i A') ?? 'TBD' }}
                        </td>

                        <td>
                            <span class="match-status match-status-{{ $match->status }}">
                                {{ ucfirst($match->status) }}
                            </span>
                        </td>

                        <td>
                            <span class="match-format-pill">
                                {{ strtoupper($match->format) }}
                            </span>
                        </td>

                        <td>
                            @if(! empty($match->bracket_group) || ! empty($match->round_label))
                                <p class="match-admin-row-title">
                                    {{ ucfirst($match->bracket_group ?? 'General') }}
                                </p>

                                <p class="match-admin-muted">
                                    {{ $match->round_label ?? 'No round' }}

                                    @if(! empty($match->bracket_position))
                                        · Slot {{ $match->bracket_position }}
                                    @endif
                                </p>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if($match->prediction)
                                <span class="match-confidence">
                                    {{ $match->prediction->confidence_score }}%
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td class="text-right">
                            <div class="match-admin-actions">
                                <a href="{{ route('matches.show', $match) }}" class="btn-small-secondary">
                                    View
                                </a>

                                <a href="{{ route('admin.matches.edit', $match) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.matches.destroy', $match) }}"
                                    onsubmit="return confirm('Delete this match? This will also delete its prediction.');"
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
                        <td colspan="8" class="match-admin-empty">
                            No matches yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($matches->hasPages())
        <div class="match-admin-pagination">
            {{ $matches->links() }}
        </div>
    @endif
@endsection