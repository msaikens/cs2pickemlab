@extends('layouts.admin', [
    'title' => 'Pick&#x2019;em Recommendations | CS2 PickLab',
    'pageTitle' => 'Pick&#x2019;em',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-pickem.css') }}">
@endpush

@section('content')
    <div class="pickem-admin-header">
        <div>
            <h2 class="pickem-admin-title">Pick&#x2019;em Recommendations</h2>
            <p class="pickem-admin-subtitle">
                Manage team recommendations for 3:0, advancement, 0:3, upset watch, and avoid slots.
            </p>
        </div>

        <a href="{{ route('admin.pickem.create') }}" class="btn-primary">
            Add Recommendation
        </a>
    </div>

    <div class="pickem-admin-table-wrap">
        <table class="pickem-admin-table">
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
                            <p class="pickem-admin-row-title">
                                {{ $recommendation->headline ?? 'Untitled recommendation' }}
                            </p>

                            <p class="pickem-admin-muted">
                                <span class="pickem-access pickem-access-{{ $recommendation->is_premium ? 'premium' : 'free' }}">
                                    {{ $recommendation->is_premium ? 'Premium' : 'Free' }}
                                </span>

                                <span class="pickem-risk pickem-risk-{{ $recommendation->risk_level }}">
                                    Risk: {{ ucfirst($recommendation->risk_level) }}
                                </span>

                                <span>Sort {{ $recommendation->sort_order }}</span>
                            </p>
                        </td>

                        <td>
                            <p class="pickem-admin-secondary-text">
                                {{ $recommendation->event?->name ?? 'No event' }}
                            </p>

                            <p class="pickem-admin-muted">
                                {{ $recommendation->stage?->name ?? 'No stage' }}
                            </p>
                        </td>

                        <td>
                            @if($recommendation->team)
                                <a href="{{ route('teams.show', $recommendation->team) }}" class="pickem-admin-link">
                                    {{ $recommendation->team->name }}
                                </a>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <span class="pickem-slot pickem-slot-{{ $recommendation->slot_type }}">
                                {{ str_replace('_', ' ', strtoupper($recommendation->slot_type)) }}
                            </span>
                        </td>

                        <td>
                            <span class="pickem-confidence">
                                {{ $recommendation->confidence_score }}%
                            </span>
                        </td>

                        <td>
                            <span class="pickem-status pickem-status-{{ $recommendation->status }}">
                                {{ ucfirst($recommendation->status) }}
                            </span>
                        </td>

                        <td class="text-right">
                            <div class="pickem-admin-actions">
                                @if($recommendation->team)
                                    <a href="{{ route('teams.show', $recommendation->team) }}" class="btn-small-secondary">
                                        Team
                                    </a>
                                @endif

                                <a href="{{ route('admin.pickem.edit', $recommendation) }}" class="btn-small-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.pickem.destroy', $recommendation) }}"
                                    onsubmit="return confirm('Delete this Pick&#x2019;em recommendation?');"
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
                        <td colspan="7" class="pickem-admin-empty">
                            No Pick&#x2019;em recommendations yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($recommendations->hasPages())
        <div class="pickem-admin-pagination">
            {{ $recommendations->links() }}
        </div>
    @endif
@endsection