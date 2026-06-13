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
    @if(app()->environment(['local', 'development', 'staging']) || auth()->user()?->isAdmin())
        <form
            method="POST"
            action="{{ route('account.complete-resync') }}"
            onsubmit="return confirm('Run a complete account re-sync? This will repair missing profile, Steam, and marketplace records for your account.');"
        >
            @csrf

            <button type="submit" class="btn-secondary">
                Complete Re-Sync
            </button>
        </form>
    @endif
    
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-red-200">
            <p class="font-bold">Fix the following:</p>

            <ul class="mt-2 list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $user->hasVerifiedEmail())
        <section class="mb-6 rounded-2xl border border-amber-400/40 bg-amber-400/10 p-6">
            <div class="text-center">
                <p class="text-xs font-black uppercase tracking-widest text-amber-200">
                    Account Verification
                </p>

                <h2 class="mt-2 text-2xl font-black text-white">
                    Verify Your Email
                </h2>

                <p class="mx-auto mt-2 max-w-2xl text-slate-300">
                    We sent a verification link and one-time code to
                    <strong class="text-white">{{ $user->email }}</strong>.
                    Delivery can take up to a minute. Check spam if it does not arrive.
                    Click the email link or enter the six-digit code below.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.code.verify') }}" class="mx-auto mt-6 grid max-w-md gap-4">
                @csrf

                <div>
                    <label for="verification_code" class="mb-2 block text-sm font-bold text-slate-200">
                        One-Time Verification Code
                    </label>

                    <input
                        id="verification_code"
                        name="verification_code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="123456"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-center text-2xl font-black tracking-[0.35em] text-white outline-none focus:border-amber-400"
                    >
                </div>

                <button type="submit" class="btn-primary justify-center">
                    Verify Email
                </button>
            </form>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4 flex justify-center">
                @csrf

                <button type="submit" class="btn-secondary">
                    Send New Code
                </button>
            </form>
        </section>
    @else
        <section class="mb-6 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 p-6 text-center">
            <p class="text-xs font-black uppercase tracking-widest text-emerald-200">
                Account Verification
            </p>

            <h2 class="mt-2 text-2xl font-black text-white">
                E-mail successfully verified.
            </h2>

            <p class="mt-2 text-slate-300">
                Your account email is verified and marketplace verification can continue.
            </p>
        </section>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-1">
            <div class="flex flex-col items-center text-center">
                @if($user->avatar_url)
                    <img
                        src="{{ $user->avatar_url }}"
                        alt="{{ $user->displayName() }}"
                        class="h-28 w-28 rounded-full border border-slate-700 object-cover"
                    >
                @else
                    <div class="flex h-28 w-28 items-center justify-center rounded-full border border-slate-700 bg-slate-900 text-4xl font-black text-cyan-300">
                        {{ strtoupper(mb_substr($user->displayName(), 0, 1)) }}
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <h2 class="text-2xl font-black text-white">
                        {{ $user->displayName() }}
                    </h2>

                    @include('components.user-role-badge', [
                        'user' => $user,
                        'showFree' => false,
                        'showPremium' => false,
                    ])
                </div>

                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>

                @if($user->profile?->first_name || $user->profile?->last_name)
                    <p class="mt-1 text-sm text-slate-400">
                        {{ trim(($user->profile?->first_name ?? '') . ' ' . ($user->profile?->last_name ?? '')) }}
                    </p>
                @endif

                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    @if ($user->hasVerifiedEmail())
                        <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-black uppercase text-emerald-200">
                            Email Verified
                        </span>
                    @else
                        <span class="rounded-full border border-amber-400/40 bg-amber-400/10 px-3 py-1 text-xs font-black uppercase text-amber-200">
                            Email Not Verified
                        </span>
                    @endif

                    @include('components.user-role-badge', [
                        'user' => $user,
                        'showFree' => true,
                        'showPremium' => true,
                    ])
                </div>
            </div>
        </section>

        <section class="card lg:col-span-2">
            <h2 class="text-2xl font-black text-white">About</h2>

            <p class="mt-3 whitespace-pre-line text-slate-300">
                {{ $user->profile?->about ?: 'No profile details added yet.' }}
            </p>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Account Name</p>
                    <p class="mt-1 text-white">{{ $user->name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Display Name</p>
                    <p class="mt-1 text-white">{{ $user->displayName() ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">First Name</p>
                    <p class="mt-1 text-white">{{ $user->profile?->first_name ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Last Name</p>
                    <p class="mt-1 text-white">{{ $user->profile?->last_name ?: '—' }}</p>
                </div>

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