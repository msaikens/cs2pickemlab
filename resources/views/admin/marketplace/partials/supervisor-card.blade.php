@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-marketplace.css') }}">
@endpush

<div class="admin-marketplace-supervisor">
    <span>Supervisor</span>

    @if($listing->supervisor)
        <strong>{{ $listing->supervisor->displayName() }}</strong>

        <small>{{ $listing->supervisor->email }}</small>

        @if($listing->supervisor_assigned_at)
            <small>
                Assigned {{ $listing->supervisor_assigned_at->format('M j, Y g:i A') }}
            </small>
        @endif
    @else
        <strong>Unassigned</strong>
        <small>No supervisor assigned yet.</small>
    @endif
</div>