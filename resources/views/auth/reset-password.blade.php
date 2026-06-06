@extends('layouts.app', [
    'title' => 'Reset Password | CS2 PickLab',
])

@section('content')
<section class="mx-auto max-w-xl px-6 py-12">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
        <h1 class="text-3xl font-black text-white">Reset password</h1>

        <p class="mt-2 text-slate-400">
            Choose a new password for your account.
        </p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-300">
                    Email
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $email) }}"
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
                    New Password
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
                    Confirm New Password
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
                Reset password
            </button>
        </form>
    </div>
</section>
@endsection