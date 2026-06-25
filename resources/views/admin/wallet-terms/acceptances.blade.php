@extends('layouts.admin', [
    'title' => 'Wallet Terms Acceptances | CS2 PickLab',
    'pageTitle' => 'Wallet Terms Acceptances',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-wallet-terms.css') }}">
@endpush

@section('content')
<section class="admin-wallet-terms-page">
    <header class="admin-wallet-terms-hero">
        <div>
            <p class="admin-wallet-terms-kicker">Wallet Audit</p>
            <h2>Wallet Terms Acceptances</h2>
            <p>
                Review which users accepted the current Wallet Terms, when they accepted, and where the acceptance came from.
            </p>
        </div>

        <div class="admin-wallet-terms-version">
            Current version: <strong>{{ $currentVersion }}</strong>
        </div>
    </header>

    <section class="admin-wallet-terms-stats">
        <article>
            <span>Total Acceptances</span>
            <strong>{{ number_format($stats['total']) }}</strong>
        </article>

        <article>
            <span>Current Version</span>
            <strong>{{ number_format($stats['current_version']) }}</strong>
        </article>

        <article>
            <span>Unique Users</span>
            <strong>{{ number_format($stats['unique_users']) }}</strong>
        </article>

        <article>
            <span>Latest Acceptance</span>
            <strong>
                @if($stats['latest_acceptance'])
                    {{ \Illuminate\Support\Carbon::parse($stats['latest_acceptance'])->format('M j, Y g:i A') }}
                @else
                    —
                @endif
            </strong>
        </article>
    </section>

    <section class="admin-wallet-terms-card">
        <form method="GET" action="{{ route('admin.wallet-terms.acceptances') }}" class="admin-wallet-terms-filters">
            <div>
                <label for="search">Search</label>
                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ request('search') }}"
                    placeholder="User, email, IP, source, version"
                >
            </div>

            <div>
                <label for="version">Version</label>
                <select id="version" name="version">
                    <option value="">All versions</option>

                    @foreach($versions as $version)
                        <option value="{{ $version }}" @selected(request('version') === $version)>
                            {{ $version }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="source">Source</label>
                <select id="source" name="source">
                    <option value="">All sources</option>

                    @foreach($sources as $source)
                        <option value="{{ $source }}" @selected(request('source') === $source)>
                            {{ str_replace('_', ' ', ucfirst($source)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-wallet-terms-filter-actions">
                <button type="submit">Filter</button>

                <a href="{{ route('admin.wallet-terms.acceptances') }}">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="admin-wallet-terms-card">
        <div class="admin-wallet-terms-table-wrap">
            <table class="admin-wallet-terms-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Version</th>
                        <th>Accepted</th>
                        <th>Source</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($acceptances as $acceptance)
                        <tr>
                            <td>
                                @if($acceptance->user)
                                    @include('components.user-identity', [
                                        'user' => $acceptance->user,
                                        'size' => 'sm',
                                        'showEmail' => true,
                                        'showAccountType' => true,
                                        'showAccountName' => false,
                                    ])
                                @else
                                    <span class="admin-wallet-terms-muted">Deleted user</span>
                                @endif
                            </td>

                            <td>
                                <span class="admin-wallet-terms-pill {{ $acceptance->terms_version === $currentVersion ? 'current' : '' }}">
                                    {{ $acceptance->terms_version }}
                                </span>
                            </td>

                            <td>
                                {{ $acceptance->accepted_at?->format('M j, Y g:i A') ?? '—' }}
                            </td>

                            <td>
                                {{ str_replace('_', ' ', ucfirst($acceptance->source ?? 'unknown')) }}
                            </td>

                            <td>
                                {{ $acceptance->ip_address ?? '—' }}
                            </td>

                            <td>
                                <span title="{{ $acceptance->user_agent }}">
                                    {{ \Illuminate\Support\Str::limit($acceptance->user_agent ?? '—', 90) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="admin-wallet-terms-empty">
                                No Wallet Terms acceptances found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($acceptances->hasPages())
            <div class="admin-wallet-terms-pagination">
                {{ $acceptances->links() }}
            </div>
        @endif
    </section>
</section>
@endsection