@extends('layouts.public', [
    'title' => 'My Account | CS2 PickLab',
    'pageTitle' => 'My Account',
])

@section('content')
<section class="mx-auto max-w-5xl px-6 py-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-white">My Account</h1>
            <p class="mt-2 text-slate-400">Manage your CS2 PickLab profile and account settings.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('account.edit') }}" class="btn-primary">Edit Profile</a>
            <a href="{{ route('account.security') }}" class="btn-secondary">Security</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-1">
            <div class="flex flex-col items-center text-center">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->displayName() }}" class="h-28 w-28 rounded-full border border-slate-700 object-cover">
                @else
                    <div class="flex h-28 w-28 items-center justify-center rounded-full border border-slate-700 bg-slate-900 text-4xl font-black text-cyan-300">
                        {{ strtoupper(mb_substr($user->displayName(), 0, 1)) }}
                    </div>
                @endif

                <h2 class="mt-4 text-2xl font-black text-white">
                    {{ $user->displayName() }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>

                <span class="mt-4 rounded-full border border-cyan-400/40 bg-cyan-400/10 px-3 py-1 text-xs font-black uppercase text-cyan-200">
                    {{ $user->subscription_status === 'active' ? 'Subscribed' : 'Free Account' }}
                </span>
            </div>
        </section>

        <section class="card lg:col-span-2">
            <h2 class="text-2xl font-black text-white">About</h2>

            <p class="mt-3 whitespace-pre-line text-slate-300">
                {{ $user->profile?->about ?: 'No profile details added yet.' }}
            </p>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Steam</p>
                    <p class="mt-1 text-white">{{ $user->profile?->steam_name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">FACEIT</p>
                    <p class="mt-1 text-white">{{ $user->profile?->faceit_name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Discord</p>
                    <p class="mt-1 text-white">{{ $user->profile?->discord_name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Twitch</p>
                    <p class="mt-1 text-white">{{ $user->profile?->twitch_name ?: '—' }}</p>
                </div>
            </div>
        </section>
    </div>
</section>
@endsection
