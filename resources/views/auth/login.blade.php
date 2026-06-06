@extends('layouts.app', [
    'title' => 'Sign In | CS2 PickLab',
])

@section('content')
<section class="mx-auto max-w-xl px-6 py-12">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
        <h1 class="text-3xl font-black text-white">Sign in</h1>

        <p class="mt-2 text-slate-400">
            Access your CS2 PickLab account.
        </p>

        @if(session('success'))
            <div class="mt-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 font-bold text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 font-bold text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if(config('services.google.client_id'))
            <div class="mt-6">
                <a
                    href="{{ route('social.redirect', 'google') }}"
                    class="block rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-center font-black text-white hover:border-cyan-400 hover:text-cyan-200"
                >
                    Continue with Google
                </a>
            </div>

            <div class="my-6 flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-800"></div>
                <span class="text-xs font-bold uppercase tracking-widest text-slate-500">or</span>
                <div class="h-px flex-1 bg-slate-800"></div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-300">
                    Email
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-400"
                >

                @error('email')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-bold text-slate-300">
                    Password
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-400"
                >

                @error('password')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded border-slate-700 bg-slate-950"
                    >
                    Remember me
                </label>

                <a href="{{ route('password.request') }}" class="text-sm font-bold text-cyan-300 hover:text-cyan-200">
                    Forgot password?
                </a>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-cyan-400 px-5 py-3 font-black text-slate-950 hover:bg-cyan-300"
            >
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            No account?
            <a href="{{ route('register') }}" class="font-bold text-cyan-300 hover:text-cyan-200">
                Create one
            </a>
        </p>
    </div>
</section>
@endsection