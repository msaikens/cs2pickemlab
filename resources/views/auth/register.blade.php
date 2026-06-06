@extends('layouts.app', [
    'title' => 'Create Account | CS2 PickLab',
])

@section('content')
<section class="mx-auto max-w-xl px-6 py-12">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
        <h1 class="text-3xl font-black text-white">Create account</h1>

        <p class="mt-2 text-slate-400">
            Save your profile, future purchases, and Pick’em activity.
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
                    Sign up with Google
                </a>
            </div>

            <div class="my-6 flex items-center gap-3">
                <div class="h-px flex-1 bg-slate-800"></div>
                <span class="text-xs font-bold uppercase tracking-widest text-slate-500">or</span>
                <div class="h-px flex-1 bg-slate-800"></div>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-slate-300">
                    Name
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-400"
                >

                @error('name')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

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
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-400"
                >

                @error('password')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-300">
                    Confirm Password
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-400"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-cyan-400 px-5 py-3 font-black text-slate-950 hover:bg-cyan-300"
            >
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account?
            <a href="{{ route('login') }}" class="font-bold text-cyan-300 hover:text-cyan-200">
                Sign in
            </a>
        </p>
    </div>
</section>
@endsection