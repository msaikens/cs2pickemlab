@extends('layouts.app', [
    'title' => 'Forgot Password | CS2 PickLab',
])

@section('content')
<section class="mx-auto max-w-xl px-6 py-12">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
        <h1 class="text-3xl font-black text-white">Forgot password</h1>

        <p class="mt-2 text-slate-400">
            Enter your email address and we’ll send you a password reset link.
        </p>

        @if(session('success'))
            <div class="mt-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 font-bold text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
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

            <button
                type="submit"
                class="w-full rounded-lg bg-cyan-400 px-5 py-3 font-black text-slate-950 hover:bg-cyan-300"
            >
                Send reset link
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Remembered your password?
            <a href="{{ route('login') }}" class="font-bold text-cyan-300 hover:text-cyan-200">
                Sign in
            </a>
        </p>
    </div>
</section>
@endsection