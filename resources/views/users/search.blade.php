@extends('layouts.public', [
    'title' => 'User Search | CS2 PickLab',
    'pageTitle' => 'User Search',
])

@section('content')
<section class="mx-auto max-w-6xl px-6 py-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-white">User Search</h1>
            <p class="mt-2 text-slate-400">Find CS2 PickLab users by account name, display name, Steam name, or Discord name.</p>
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
                @php
                    $accountType = match (true) {
                        $resultUser->role === 'admin' => 'Administrator',
                        $resultUser->role === 'moderator' => 'Moderator',
                        $resultUser->hasActiveSubscription() => 'Premium User',
                        default => 'Free User',
                    };

                    $badgeClass = match ($accountType) {
                        'Administrator' => 'border-red-400/50 bg-red-500/10 text-red-200',
                        'Moderator' => 'border-violet-400/50 bg-violet-500/10 text-violet-200',
                        'Premium User' => 'border-cyan-400/50 bg-cyan-400/10 text-cyan-200',
                        default => 'border-slate-600 bg-slate-800 text-slate-300',
                    };
                @endphp

                <article class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        @if ($resultUser->avatar_url)
                            <img
                                src="{{ $resultUser->avatar_url }}"
                                alt="{{ $resultUser->displayName() }}"
                                class="h-16 w-16 shrink-0 rounded-full border border-slate-700 object-cover"
                            >
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-slate-700 bg-slate-950 text-2xl font-black text-cyan-300">
                                {{ strtoupper(mb_substr($resultUser->displayName(), 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-black text-white">
                                {{ $resultUser->name ?: 'Unnamed Account' }}
                            </h2>

                            <p class="truncate text-sm text-slate-400">
                                Display: {{ $resultUser->displayName() }}
                            </p>

                            @if ($resultUser->profile?->first_name || $resultUser->profile?->last_name)
                                <p class="truncate text-sm text-slate-500">
                                    {{ trim(($resultUser->profile?->first_name ?? '') . ' ' . ($resultUser->profile?->last_name ?? '')) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <span class="rounded-full border px-3 py-1 text-xs font-black uppercase {{ $badgeClass }}">
                            {{ $accountType }}
                        </span>

                        @if ($resultUser->hasVerifiedEmail())
                            <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-black uppercase text-emerald-200">
                                Verified
                            </span>
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