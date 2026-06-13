@extends('layouts.public', [
    'title' => 'User Search | CS2 PickLab',
    'pageTitle' => 'User Search',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user-search.css') }}">
@endpush

@section('content')
<section class="user-search-page">
    <header class="user-search-hero">
        <p class="user-search-kicker">Community</p>
        <h1>User Search</h1>
        <p>
            Find CS2 PickLab users by account name, display name, Steam name, or Discord name.
        </p>
    </header>

    <form method="GET" action="{{ route('users.search') }}" class="user-search-form">
        <div class="user-search-input-wrap">
            <label for="q">Search users</label>

            <input
                id="q"
                name="q"
                type="search"
                value="{{ $search }}"
                placeholder="Search users..."
            >
        </div>

        <button type="submit" class="user-search-button primary">
            Search
        </button>

        @if ($search !== '')
            <a href="{{ route('users.search') }}" class="user-search-button secondary">
                Reset
            </a>
        @endif
    </form>

    @if ($users->count() === 0)
        <section class="user-search-empty">
            <div class="user-search-empty-icon">?</div>
            <h2>No users found.</h2>
            <p>Try a different name or handle.</p>
        </section>
    @else
        <section class="user-search-results">
            @foreach ($users as $resultUser)
                <article class="user-search-card">
                    <div class="user-search-identity">
                        @include('components.user-identity', [
                            'user' => $resultUser,
                            'size' => 'lg',
                            'showAccountType' => true,
                            'showAccountName' => true,
                        ])
                    </div>

                    <div class="user-search-card-actions">
                        @if ($resultUser->hasVerifiedEmail())
                            <span class="user-search-pill verified">
                                Verified
                            </span>
                        @else
                            <span class="user-search-pill unverified">
                                Unverified
                            </span>
                        @endif

                        @if(auth()->user()?->isAdmin())
                            <form
                                method="POST"
                                action="{{ route('admin.users.complete-resync', $resultUser) }}"
                                onsubmit="return confirm('Run a complete re-sync for this user?');"
                            >
                                @csrf

                                <button type="submit" class="user-search-button secondary">
                                    Complete Re-Sync
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        <div class="user-search-pagination">
            {{ $users->links() }}
        </div>
    @endif
</section>
@endsection