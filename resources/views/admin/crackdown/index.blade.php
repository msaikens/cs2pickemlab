@extends('layouts.admin', [
    'title' => 'Crackdown | CS2 PickLab',
    'pageTitle' => 'Crackdown',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-crackdown.css') }}">
@endpush

@section('content')
<section class="admin-crackdown-page">
    <header class="admin-crackdown-hero">
        <div>
            <p class="admin-crackdown-kicker">Moderation</p>
            <h2 class="admin-crackdown-fire-title" data-text="Crackdown">
                Crackdown
            </h2>
            <p class="admin-crackdown-fire-subtitle">
                Moderation tools are hot. Use carefully.
            </p>
            <p>
                Issue warnings, suspensions, bans, listing removals, appeal decisions, and reversals with incident records and user inbox notices.
            </p>
        </div>
    </header>

    @if($errors->any())
        <div class="admin-crackdown-alert danger">
            <strong>Action could not be completed.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-crackdown-card">
        <form method="GET" action="{{ route('admin.crackdown.index') }}" class="admin-crackdown-search">
            <label for="search">Find User</label>

            <div>
                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, email, or incident number"
                >

                <button type="submit">Search</button>

                @if(request()->filled('search'))
                    <a href="{{ route('admin.crackdown.index') }}">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="admin-crackdown-card">
        <div class="admin-crackdown-section-header">
            <div>
                <h2>Pending Appeals</h2>
                <p>Review user-submitted appeals and either reverse the incident or deny the appeal.</p>
            </div>
        </div>

        <div class="admin-crackdown-incidents">
            @forelse(($pendingAppeals ?? collect()) as $appeal)
                <article class="admin-crackdown-incident-card appeal">
                    <header>
                        <div>
                            <strong>
                                Appeal #{{ $appeal->id }}

                                @if($appeal->incident?->incident_number)
                                    <span>&middot; {{ $appeal->incident->incident_number }}</span>
                                @endif
                            </strong>

                            <p>
                                User: {{ $appeal->user?->displayName() ?? 'Unknown user' }}
                            </p>
                        </div>

                        <span class="admin-crackdown-pill warning">
                            Pending
                        </span>
                    </header>

                    <div class="admin-crackdown-message">
                        {!! nl2br(e($appeal->message)) !!}
                    </div>

                    <div class="admin-crackdown-appeal-actions">
                        <form
                            method="POST"
                            action="{{ route('admin.crackdown.appeals.approve', $appeal) }}"
                            onsubmit="return confirm('Approve this appeal and reverse the incident?');"
                        >
                            @csrf

                            <label for="approve-note-{{ $appeal->id }}">
                                Approval / reversal reason
                            </label>

                            <textarea
                                id="approve-note-{{ $appeal->id }}"
                                name="review_note"
                                required
                                minlength="10"
                                maxlength="5000"
                                placeholder="Explain why this appeal is approved and the incident is being reversed."
                            ></textarea>

                            <button type="submit">
                                Approve + Reverse
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('admin.crackdown.appeals.deny', $appeal) }}"
                            onsubmit="return confirm('Deny this appeal?');"
                        >
                            @csrf

                            <label for="deny-note-{{ $appeal->id }}">
                                Denial reason
                            </label>

                            <textarea
                                id="deny-note-{{ $appeal->id }}"
                                name="review_note"
                                required
                                minlength="10"
                                maxlength="5000"
                                placeholder="Explain why this appeal is denied."
                            ></textarea>

                            <button type="submit" class="danger">
                                Deny Appeal
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="admin-crackdown-empty">No pending appeals.</p>
            @endforelse
        </div>
    </section>

    <section class="admin-crackdown-grid">
        @forelse($users as $user)
            <article class="admin-crackdown-user-card">
                <header>
                    @include('components.user-identity', [
                        'user' => $user,
                        'showEmail' => true,
                        'showAccountType' => true,
                    ])

                    <div class="admin-crackdown-statuses">
                        @if($user->isSiteBanned())
                            <span class="danger">Banned</span>
                        @elseif($user->isSiteSuspended())
                            <span class="warning">Suspended</span>
                        @else
                            <span class="success">Allowed</span>
                        @endif

                        <span>{{ $user->moderation_incidents_count ?? 0 }} incidents</span>
                        <span>{{ $user->inbox_messages_count ?? 0 }} inbox</span>
                    </div>
                </header>

                <div class="admin-crackdown-actions">
                    <details>
                        <summary>Issue Warning</summary>

                        <form method="POST" action="{{ route('admin.crackdown.users.warn', $user) }}">
                            @csrf

                            <label>Title</label>
                            <input name="title" value="Account warning">

                            <label>User-facing message</label>
                            <textarea name="user_message" required placeholder="User-facing warning message"></textarea>

                            <label>Internal admin note</label>
                            <textarea name="admin_note" placeholder="Internal admin note"></textarea>

                            <button type="submit">Issue Warning</button>
                        </form>
                    </details>

                    <details>
                        <summary>Suspend User</summary>

                        <form method="POST" action="{{ route('admin.crackdown.users.suspend', $user) }}">
                            @csrf

                            <label>Title</label>
                            <input name="title" value="Account suspended">

                            <label>Suspension ends</label>
                            <input name="ends_at" type="datetime-local" required>

                            <label>User-facing message</label>
                            <textarea name="user_message" required placeholder="User-facing suspension message"></textarea>

                            <label>Internal admin note</label>
                            <textarea name="admin_note" placeholder="Internal admin note"></textarea>

                            <button type="submit">Suspend User</button>
                        </form>
                    </details>

                    <details>
                        <summary>Ban User</summary>

                        <form
                            method="POST"
                            action="{{ route('admin.crackdown.users.ban', $user) }}"
                            onsubmit="return confirm('Ban this user? This will block site access.');"
                        >
                            @csrf

                            <label>Title</label>
                            <input name="title" value="Account banned">

                            <label>User-facing message</label>
                            <textarea name="user_message" required placeholder="User-facing ban message"></textarea>

                            <label>Internal admin note</label>
                            <textarea name="admin_note" placeholder="Internal admin note"></textarea>

                            <button type="submit" class="danger">Ban User</button>
                        </form>
                    </details>

                    <details>
                        <summary>Remove Marketplace Listings</summary>

                        <form
                            method="POST"
                            action="{{ route('admin.crackdown.users.remove-listings', $user) }}"
                            onsubmit="return confirm('Remove this user’s active/pending marketplace listings?');"
                        >
                            @csrf

                            <label>Title</label>
                            <input name="title" value="Marketplace listings removed">

                            <label>User-facing message</label>
                            <textarea name="user_message" required placeholder="User-facing listing removal message"></textarea>

                            <label>Internal admin note</label>
                            <textarea name="admin_note" placeholder="Internal admin note"></textarea>

                            <button type="submit">Remove Listings</button>
                        </form>
                    </details>
                </div>
            </article>
        @empty
            <div class="admin-crackdown-empty">
                No users found.
            </div>
        @endforelse
    </section>

    @if($users->hasPages())
        <div class="admin-crackdown-pagination">
            {{ $users->links() }}
        </div>
    @endif

    <section class="admin-crackdown-card">
        <div class="admin-crackdown-section-header">
            <div>
                <h2>Latest Incidents</h2>
                <p>Recent moderation actions and reversal status.</p>
            </div>
        </div>

        <div class="admin-crackdown-incidents">
            @forelse(($latestIncidents ?? collect()) as $incident)
                <article class="admin-crackdown-incident-card">
                    <header>
                        <div>
                            <strong>{{ $incident->incident_number }}</strong>

                            <p>
                                {{ ucfirst(str_replace('_', ' ', $incident->action_type)) }}
                                against {{ $incident->subjectUser?->displayName() ?? 'Unknown user' }}
                                by {{ $incident->adminUser?->displayName() ?? 'Unknown admin' }}
                            </p>
                        </div>

                        @if($incident->isReversed())
                            <span class="admin-crackdown-pill success">Reversed</span>
                        @else
                            <span class="admin-crackdown-pill">Active</span>
                        @endif
                    </header>

                    <footer>
                        <span>Created: {{ $incident->created_at?->format('M j, Y g:i A') }}</span>

                        @if($incident->listings_removed_count)
                            <span>{{ $incident->listings_removed_count }} listings removed</span>
                        @endif
                    </footer>

                    @if($incident->isReversed())
                        <div class="admin-crackdown-reversed-note">
                            <strong>
                                Reversed by {{ $incident->reversedByUser?->displayName() ?? 'Unknown admin' }}
                                @if($incident->reversed_at)
                                    on {{ $incident->reversed_at->format('M j, Y g:i A') }}
                                @endif
                            </strong>

                            @if($incident->reversal_reason)
                                <p>{!! nl2br(e($incident->reversal_reason)) !!}</p>
                            @endif
                        </div>
                    @else
                        <details class="admin-crackdown-reversal">
                            <summary>Reverse Incident</summary>

                            <form
                                method="POST"
                                action="{{ route('admin.crackdown.incidents.reverse', $incident) }}"
                                onsubmit="return confirm('Reverse this incident?');"
                            >
                                @csrf

                                <label for="reversal-reason-{{ $incident->id }}">
                                    Reversal reason
                                </label>

                                <textarea
                                    id="reversal-reason-{{ $incident->id }}"
                                    name="reversal_reason"
                                    required
                                    minlength="10"
                                    maxlength="5000"
                                    placeholder="Explain why this incident is being reversed."
                                ></textarea>

                                <button type="submit">
                                    Reverse Incident
                                </button>
                            </form>
                        </details>
                    @endif
                </article>
            @empty
                <p class="admin-crackdown-empty">No incidents yet.</p>
            @endforelse
        </div>
    </section>
</section>
@endsection