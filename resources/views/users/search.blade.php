@extends('layouts.public', [
    'title' => 'User Search | CS2 PickLab',
    'pageTitle' => 'User Search',
])

@section('content')
<section class="mx-auto max-w-6xl px-6 py-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-white">User Search</h1>
            <p class="mt-2 text-slate-400">
                Find CS2 PickLab users by account name, display name, Steam name, or Discord name.
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('users.search') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 md:flex-row">
        <input
            name="q"
            type="search"
            value="{{ $search }}"
            placeholder="Search users..."
            class="min-h-12 flex-1 rounded-xl border border-slate-700 bg-slate-950 px-4 text-white outline-none focus:border-cyan-400"
        >

        <button type="submit" class="btn-primary">
            Search
        </button>

        @if ($search !== '')
            <a href="{{ route('users.search') }}" class="btn-secondary text-center">
                Reset
            </a>
        @endif
    </form>

    @if ($users->count() === 0)
        <section class="card text-center">
            <h2 class="text-2xl font-black text-white">No users found.</h2>
            <p class="mt-2 text-slate-400">Try a different name or handle.</p>
        </section>
    @else
        <div class="grid gap-4">
            @foreach ($users as $resultUser)
    <article class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 md:flex-row md:items-center md:justify-between">
        @include('components.user-identity', [
            'user' => $resultUser,
            'size' => 'lg',
            'showAccountType' => true,
            'showAccountName' => true,
        ])

        <div class="flex shrink-0 flex-wrap items-center gap-2">
            @if ($resultUser->hasVerifiedEmail())
                <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-black uppercase text-emerald-200">
                    Verified
                </span>
            @else
                <span class="rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-xs font-black uppercase text-amber-200">
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

                    <button type="submit" class="btn-secondary">
                        Complete Re-Sync
                    </button>
                </form>
            @endif
        </div>
    </article>
@endforeach
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</section>
@endsection