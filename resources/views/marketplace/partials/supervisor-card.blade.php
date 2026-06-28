@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-sell.css') }}">
@endpush

<div class="marketplace-supervisor-card">
    <span>Marketplace Supervisor</span>

    @if($listing->supervisor)
        <strong>
            {{ method_exists($listing->supervisor, 'publicDisplayName')
                ? $listing->supervisor->publicDisplayName(auth()->user())
                : $listing->supervisor->displayName() }}
        </strong>

        @if($listing->supervisor_assigned_at)
            <small>
                Assigned {{ $listing->supervisor_assigned_at->format('M j, Y') }}
            </small>
        @endif
    @else
        <strong>Pending assignment</strong>
        <small>A site admin will be assigned automatically.</small>
    @endif
</div>