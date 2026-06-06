@extends('layouts.public', [
    'title' => 'Account Security | CS2 PickLab',
    'pageTitle' => 'Account Security',
])

@section('content')
<section class="mx-auto max-w-4xl px-6 py-10">
    <div class="mb-6">
        <a href="{{ route('account.show') }}" class="link-accent">← Back to Account</a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="card">
            <h1 class="text-3xl font-black text-white">Password</h1>
            <p class="mt-2 text-slate-400">Update your local account password.</p>

            <form method="POST" action="{{ route('account.password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                @if($user->password)
                    <div>
                        <label class="form-label" for="current_password">Current Password</label>
                        <input id="current_password" name="current_password" type="password" class="form-input">
                        @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label class="form-label" for="password">New Password</label>
                    <input id="password" name="password" type="password" class="form-input">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input">
                </div>

                <button type="submit" class="btn-primary-lg w-full">Update Password</button>
            </form>
        </section>

        <section class="card">
            <h2 class="text-3xl font-black text-white">Linked Accounts</h2>
            <p class="mt-2 text-slate-400">OAuth sign-in providers connected to your account.</p>

            <div class="mt-6 space-y-3">
                @forelse($user->socialAccounts as $account)
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                        <p class="font-black text-white">{{ ucfirst($account->provider) }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $account->provider_email ?: 'No email returned' }}</p>
                    </div>
                @empty
                    <p class="text-slate-400">No external accounts linked yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection
