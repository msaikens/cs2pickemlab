@extends('layouts.admin', [
    'title' => 'Marketplace Trade Requests | CS2 PickLab',
    'pageTitle' => 'Marketplace Trade Requests',
])
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-marketplace.css') }}">
@endpush
@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Marketplace Trade Requests</h2>
            <p class="page-subtitle">
                Review marketplace trade activity and audit history.
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.marketplace.trade-requests') }}" class="admin-filter-panel">
        <select name="status" class="form-input admin-filter-select">
            <option value="">All Statuses</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
            <option value="declined" @selected(request('status') === 'declined')>Declined</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
        </select>

        <button type="submit" class="btn-primary">
            Filter
        </button>

        <a href="{{ route('admin.marketplace.trade-requests') }}" class="btn-secondary">
            Reset
        </a>
    </form>

    <div class="admin-audit-list">
        @forelse($tradeRequests as $tradeRequest)
            <article class="admin-audit-card">
                <header class="admin-audit-header">
                    <div>
                        <h2 class="admin-audit-title">
                            {{ $tradeRequest->listing?->market_hash_name ?? 'Removed Listing' }}
                        </h2>

                        <p class="admin-audit-subtitle">
                            Trade request #{{ $tradeRequest->id }}
                        </p>
                    </div>

                    <span class="status-pill status-pill-{{ $tradeRequest->status }}">
                        {{ ucfirst($tradeRequest->status) }}
                    </span>
                </header>

                <div class="admin-user-grid">
                    <section class="admin-mini-card">
                        <p class="admin-mini-card-label">Buyer</p>

                        @include('components.user-identity', [
                            'user' => $tradeRequest->buyer,
                            'size' => 'sm',
                            'showEmail' => true,
                            'showAccountType' => true,
                            'showAccountName' => false,
                        ])
                    </section>

                    <section class="admin-mini-card">
                        <p class="admin-mini-card-label">Seller</p>

                        @include('components.user-identity', [
                            'user' => $tradeRequest->seller,
                            'size' => 'sm',
                            'showEmail' => true,
                            'showAccountType' => true,
                            'showAccountName' => false,
                        ])
                    </section>
                </div>

                @if($tradeRequest->message)
                    <section class="admin-message-card">
                        {{ $tradeRequest->message }}
                    </section>
                @endif

                <section class="admin-activity">
                    <p class="admin-mini-card-label">Activity</p>

                    <div class="admin-activity-list">
                        @forelse($tradeRequest->events as $event)
                            <article class="admin-activity-item">
                                <div class="admin-activity-line">
                                    <strong>
                                        {{ str($event->event_type)->replace('_', ' ')->title() }}
                                    </strong>

                                    <span class="text-muted-xs">by</span>

                                    @if($event->actor)
                                        @include('components.user-role-badge', [
                                            'user' => $event->actor,
                                            'showFree' => false,
                                            'showPremium' => true,
                                        ])

                                        <span>{{ $event->actor->displayName() }}</span>
                                    @else
                                        <span>System</span>
                                    @endif

                                    <span class="text-muted-xs">
                                        · {{ $event->created_at?->format('M j, Y g:i A') }}
                                    </span>
                                </div>

                                @if($event->old_status || $event->new_status)
                                    <p class="admin-activity-status-change">
                                        {{ $event->old_status ?? 'none' }} → {{ $event->new_status ?? 'none' }}
                                    </p>
                                @endif
                            </article>
                        @empty
                            <article class="admin-activity-item muted">
                                No activity events logged.
                            </article>
                        @endforelse
                    </div>
                </section>
            </article>
        @empty
            <section class="card text-center">
                <h2 class="page-title">No trade requests found.</h2>
                <p class="page-subtitle">
                    Marketplace trade activity will appear here when users submit requests.
                </p>
            </section>
        @endforelse
    </div>

    @if($tradeRequests->hasPages())
        <div class="pagination-wrap">
            {{ $tradeRequests->links() }}
        </div>
    @endif
@endsection