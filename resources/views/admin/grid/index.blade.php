{{-- resources/views/admin/grid/index.blade.php --}}

@extends('layouts.admin', [
    'title' => 'GRID Imports | CS2 PickLab',
    'pageTitle' => 'GRID Imports'
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-grid.css') }}">
@endpush

@section('content')
@php
    $currentUser = auth()->user();

    $isAdmin = $currentUser && method_exists($currentUser, 'isAdmin')
        ? (bool) $currentUser->isAdmin()
        : false;

    $runCollection = collect();

    if (isset($runs)) {
        $runCollection = is_object($runs) && method_exists($runs, 'getCollection')
            ? $runs->getCollection()
            : collect($runs);
    }

    $recentFailedRuns = $runCollection
        ->where('status', 'failed')
        ->filter(function ($run) {
            $output = $run->output ?? [];

            if (is_string($output)) {
                $output = json_decode($output, true) ?: [];
            }

            return empty(data_get($output, 'notification_dismissed_at'));
        })
        ->take(3);
@endphp

<section class="grid-admin-page">
    <header class="grid-admin-hero">
        <div>
            <p class="grid-admin-kicker">Data Operations</p>

            <h1>GRID Imports</h1>

            <p>
                Search tournaments, cache GRID tournament records, create local event records,
                discover series, download GRID files, and import tournament-wide CS2 stats.
            </p>
        </div>

        <div class="grid-admin-hero-badge">
            <span>CS2 Title ID</span>
            <strong>{{ config('services.grid.cs2_title_id', '28') }}</strong>
        </div>
    </header>

    @if(session('status'))
        <div class="grid-admin-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if(session('grid_status'))
        <div class="grid-admin-alert success">
            {{ session('grid_status') }}
        </div>
    @endif

    @if(session('grid_warning'))
        <div class="grid-admin-alert warning">
            <strong>GRID warning:</strong>
            <p>{{ session('grid_warning') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="grid-admin-alert danger">
            <strong>Fix this first:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($recentFailedRuns->isNotEmpty())
        <div class="grid-admin-alert warning">
            <div class="grid-admin-alert-heading-row">
                <strong>Recent GRID import warning</strong>
            </div>

            <p>
                One or more GRID import tasks failed safely. The admin tools are still available and you can keep working.
            </p>

            <ul class="grid-admin-warning-list">
                @foreach($recentFailedRuns as $failedRun)
                    <li>
                        <div class="grid-admin-warning-row">
                            <span>
                                <strong>Run #{{ $failedRun->id }}:</strong>

                                @if($isAdmin)
                                    {{ $failedRun->error_message ?: 'No error message was saved for this failed run.' }}
                                @else
                                    The issue has been logged for admin review.
                                @endif
                            </span>

                            @if($isAdmin)
                                <form
                                    method="POST"
                                    action="{{ route('admin.grid.dismiss-run-notification', $failedRun) }}"
                                    class="grid-admin-dismiss-form"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="grid-admin-dismiss-button">
                                        Dismiss
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid-admin-actions-grid">
        <article class="grid-admin-card">
            <div class="grid-admin-card-header">
                <span class="grid-admin-card-icon">01</span>

                <div>
                    <h2>Search / Pull Tournaments</h2>
                    <p>Search by name or leave blank to pull tournament pages into the cache.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.grid.search-tournaments') }}" class="grid-admin-form">
                @csrf

                <label for="search">Tournament search</label>

                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ old('search') }}"
                    placeholder="Leave blank to pull tournament pages..."
                >

                <button type="submit" class="grid-admin-button primary">
                    Search / Pull Tournaments
                </button>
            </form>
        </article>

        <article class="grid-admin-card">
            <div class="grid-admin-card-header">
                <span class="grid-admin-card-icon">02</span>

                <div>
                    <h2>Create Local Event</h2>
                    <p>Use cached tournament rows below to create or link a local event.</p>
                </div>
            </div>

            <div class="grid-admin-help-box">
                <p>
                    Click <strong>Create / Link Event</strong> on the correct GRID tournament.
                    The local event becomes available for discovery, downloads, and stats imports.
                </p>
            </div>
        </article>

        <article class="grid-admin-card">
            <div class="grid-admin-card-header">
                <span class="grid-admin-card-icon">03</span>

                <div>
                    <h2>Discover Series</h2>
                    <p>Use a local event GRID ID to find series IDs.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.grid.discover-series') }}" class="grid-admin-form">
                @csrf

                <label for="event_id_discover">Event</label>

                <select id="event_id_discover" name="event_id" required>
                    @forelse($events as $event)
                        <option value="{{ $event->id }}">
                            {{ $event->name }} — GRID {{ $event->grid_id ?? 'missing' }}
                        </option>
                    @empty
                        <option value="" disabled selected>
                            No local events yet
                        </option>
                    @endforelse
                </select>

                <label for="event_stage_id_discover">Stage ID optional</label>

                <input
                    id="event_stage_id_discover"
                    name="event_stage_id"
                    type="number"
                    placeholder="Leave blank for full tournament"
                >

                <button type="submit" class="grid-admin-button primary" @disabled($events->isEmpty())>
                    Discover Series IDs
                </button>
            </form>
        </article>

        <article class="grid-admin-card">
            <div class="grid-admin-card-header">
                <span class="grid-admin-card-icon">04</span>

                <div>
                    <h2>Download / Import</h2>
                    <p>Download files for discovered series or import aggregate stats.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.grid.download-series-files') }}" class="grid-admin-form grid-admin-stacked-form">
                @csrf

                <label for="event_id_download">Event for downloads</label>

                <select id="event_id_download" name="event_id" required>
                    @forelse($events as $event)
                        <option value="{{ $event->id }}">
                            {{ $event->name }}
                        </option>
                    @empty
                        <option value="" disabled selected>
                            No local events yet
                        </option>
                    @endforelse
                </select>

                <label for="event_stage_id_download">Stage ID optional</label>

                <input
                    id="event_stage_id_download"
                    name="event_stage_id"
                    type="number"
                    placeholder="Leave blank for full tournament"
                >

                <button type="submit" class="grid-admin-button primary" @disabled($events->isEmpty())>
                    Download Files
                </button>
            </form>

            <hr class="grid-admin-divider">

            <form method="POST" action="{{ route('admin.grid.import-stats') }}" class="grid-admin-form grid-admin-stacked-form">
                @csrf

                <label for="event_id_stats">Event for stats</label>

                <select id="event_id_stats" name="event_id" required>
                    @forelse($events as $event)
                        <option value="{{ $event->id }}">
                            {{ $event->name }}
                        </option>
                    @empty
                        <option value="" disabled selected>
                            No local events yet
                        </option>
                    @endforelse
                </select>

                <label for="scope">Scope</label>

                <select id="scope" name="scope">
                    <option value="tournament_to_date">Tournament to date</option>
                    <option value="stage_to_date">Stage to date</option>
                    <option value="recent">Recent</option>
                </select>

                <label for="event_stage_id_stats">Stage ID optional</label>

                <input
                    id="event_stage_id_stats"
                    name="event_stage_id"
                    type="number"
                    placeholder="Leave blank for full tournament"
                >

                <button type="submit" class="grid-admin-button primary" @disabled($events->isEmpty())>
                    Import Stats
                </button>
            </form>
        </article>
    </div>

    <section class="grid-admin-card grid-admin-wide-card">
        <div class="grid-admin-section-header">
    <div>
        <p class="grid-admin-kicker">Tournament Cache</p>

        <h2>Cached GRID Tournaments</h2>

        <p>
            These rows come from GRID searches or blank paginated pulls. Create a local event from the correct tournament.
        </p>
    </div>

    @if($isAdmin && ($gridTournamentResults ?? collect())->isNotEmpty())
        <form
            method="POST"
            action="{{ route('admin.grid.clear-tournament-cache') }}"
            onsubmit="return confirm('Clear all cached GRID tournament rows from this page? Local events will not be deleted.');"
        >
            @csrf
            @method('DELETE')

            <button type="submit" class="grid-admin-button danger small">
                Clear Tournament Cache
            </button>
        </form>
    @endif
</div>

        <div class="grid-admin-table-wrap">
            <table class="grid-admin-table">
                <thead>
                    <tr>
                        <th>GRID ID</th>
                        <th>Tournament</th>
                        <th>Last Seen</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($gridTournamentResults ?? collect()) as $tournament)
                        <tr>
                            <td>
                                <span class="grid-admin-code">
                                    {{ $tournament->grid_tournament_id }}
                                </span>
                            </td>

                            <td>
                                {{ $tournament->name }}
                            </td>

                            <td>
                                {{ $tournament->last_seen_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>

                            <td>
                                <form
                                    method="POST"
                                    action="{{ route('admin.grid.create-event-from-tournament') }}"
                                    class="grid-admin-inline-form"
                                >
                                    @csrf

                                    <input type="hidden" name="grid_id" value="{{ $tournament->grid_tournament_id }}">
                                    <input type="hidden" name="name" value="{{ $tournament->name }}">

                                    <button type="submit" class="grid-admin-button small">
                                        Create / Link Event
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="grid-admin-empty">
                                No cached GRID tournaments yet. Run Search / Pull Tournaments first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid-admin-card grid-admin-wide-card">
        <div class="grid-admin-section-header">
            <div>
                <p class="grid-admin-kicker">Local Events</p>

                <h2>Events Available for Import</h2>

                <p>
                    Series discovery and stats imports use these local records.
                </p>
            </div>
        </div>

        <div class="grid-admin-table-wrap">
            <table class="grid-admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Slug</th>
                        <th>GRID ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>#{{ $event->id }}</td>

                            <td>{{ $event->name }}</td>

                            <td>
                                <span class="grid-admin-code">
                                    {{ $event->slug ?? '—' }}
                                </span>
                            </td>

                            <td>
                                <span class="grid-admin-code">
                                    {{ $event->grid_id ?? 'missing' }}
                                </span>
                            </td>

                            <td>
                                <span class="grid-admin-status {{ $event->status ?? 'draft' }}">
                                    {{ ucfirst($event->status ?? 'draft') }}
                                </span>
                            </td>
                            <td>
                                @if($isAdmin)
                                    <form
                                        method="POST"
                                        action="{{ route('admin.grid.delete-local-event', $event) }}"
                                        class="grid-admin-inline-form"
                                        onsubmit="return confirm('Delete this local event and its GRID discoveries? This should only be used for erroneous or unwanted local events.');"
                                    >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="grid-admin-button danger small">
                                        Delete Event
                                    </button>
                                    </form>
                                @else
                                    <span class="grid-admin-muted">Admin only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="grid-admin-empty">
                                No local events yet. Create/link one from the tournament cache above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid-admin-card grid-admin-wide-card">
        <div class="grid-admin-section-header">
            <div>
                <p class="grid-admin-kicker">Import History</p>

                <h2>Recent GRID Import Runs</h2>
            </div>
        </div>

        <div class="grid-admin-table-wrap">
            <table class="grid-admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Event</th>
                        <th>Started</th>
                        <th>Finished</th>
                        <th>Error</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($runs as $run)
                        <tr>
                            <td>#{{ $run->id }}</td>

                            <td>
                                <span class="grid-admin-code">
                                    {{ $run->action }}
                                </span>
                            </td>

                            <td>
                                <span class="grid-admin-status {{ $run->status }}">
                                    {{ ucfirst($run->status) }}
                                </span>
                            </td>

                            <td>{{ $run->event?->name ?? '—' }}</td>

                            <td>{{ $run->started_at?->format('Y-m-d H:i') ?? '—' }}</td>

                            <td>{{ $run->finished_at?->format('Y-m-d H:i') ?? '—' }}</td>

                            <td class="grid-admin-error-cell">
                                @if($run->error_message)
                                    @if($isAdmin)
                                        {{ \Illuminate\Support\Str::limit($run->error_message, 120) }}
                                    @else
                                        Reported to admin
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        @if(!empty($run->output))
                            <tr>
                                <td></td>

                                <td colspan="6">
                                    @if($isAdmin)
                                        <details class="grid-admin-details">
                                            <summary>View output</summary>

                                            <pre>{{ json_encode($run->output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @else
                                        <span class="grid-admin-muted">
                                            Output details are available to admins.
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="grid-admin-empty">
                                No GRID import runs yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid-admin-card grid-admin-wide-card">
        <div class="grid-admin-section-header">
    <div>
        <p class="grid-admin-kicker">Series Cache</p>

        <h2>Discovered GRID Series</h2>
    </div>

    @if($isAdmin && $series->isNotEmpty())
        <form
            method="POST"
            action="{{ route('admin.grid.clear-series-discoveries') }}"
            onsubmit="return confirm('Clear discovered GRID series from this page? Local events will not be deleted.');"
        >
            @csrf
            @method('DELETE')

            <button type="submit" class="grid-admin-button danger small">
                Clear Series Discoveries
            </button>
        </form>
    @endif
</div>

        <div class="grid-admin-table-wrap">
            <table class="grid-admin-table">
                <thead>
                    <tr>
                        <th>Series ID</th>
                        <th>Status</th>
                        <th>Event</th>
                        <th>Teams</th>
                        <th>Files</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($series as $item)
                        <tr>
                            <td>
                                <span class="grid-admin-code">
                                    {{ $item->grid_series_id }}
                                </span>
                            </td>

                            <td>
                                <span class="grid-admin-status {{ $item->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>

                            <td>{{ $item->event?->name ?? '—' }}</td>

                            <td>
                                {{ $item->team_one_name ?? '—' }}

                                <span class="grid-admin-versus">vs</span>

                                {{ $item->team_two_name ?? '—' }}
                            </td>

                            <td>
                                <div class="grid-admin-file-flags">
                                    <span class="{{ $item->events_file_path ? 'yes' : 'no' }}">
                                        Events {{ $item->events_file_path ? 'yes' : 'no' }}
                                    </span>

                                    <span class="{{ $item->end_state_file_path ? 'yes' : 'no' }}">
                                        State {{ $item->end_state_file_path ? 'yes' : 'no' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="grid-admin-empty">
                                No GRID series discovered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</section>
@endsection