@extends('layouts.public', [
    'title' => 'Account Security | CS2 PickLab',
    'pageTitle' => 'Account Security',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-security.css') }}">
@endpush

@section('content')
<section class="security-page">
    <div class="security-back">
        <a href="{{ route('account.show') }}">← Back to Account</a>
    </div>

    <header class="security-hero">
        <p class="security-kicker">Account Center</p>
        <h1>Account Security</h1>
        <p>Update your password and review external sign-in providers connected to your account.</p>
    </header>

    @if(session('success'))
        <div class="security-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="security-alert danger">
            <strong>Fix the following:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="security-grid">
        <section class="security-card">
            <div class="security-card-heading">
                <p class="security-kicker">Password</p>
                <h2>Update Password</h2>
                <p>Change the password used for your local CS2 PickLab account.</p>
            </div>

            <form method="POST" action="{{ route('account.password.update') }}" class="security-form">
                @csrf
                @method('PUT')

                @if($user->password)
                    <div class="security-field">
                        <label for="current_password">Current Password</label>

                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                        >

                        @error('current_password')
                            <p class="security-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="security-field">
                    <label for="password">New Password</label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                    >

                    @error('password')
                        <p class="security-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="security-field">
                    <label for="password_confirmation">Confirm New Password</label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="security-button primary">
                    Update Password
                </button>
            </form>
        </section>

        <section class="security-card">
            <div class="security-card-heading">
                <p class="security-kicker">Linked Accounts</p>
                <h2>OAuth Providers</h2>
                <p>External sign-in providers connected to your CS2 PickLab account.</p>
            </div>

            <div class="linked-account-list">
                @forelse($user->socialAccounts as $account)
                    <div class="linked-account-card">
                        <div class="linked-account-icon">
                            {{ strtoupper(mb_substr($account->provider, 0, 1)) }}
                        </div>

                        <div>
                            <strong>{{ ucfirst($account->provider) }}</strong>
                            <p>{{ $account->provider_email ?: 'No email returned' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="security-empty">
                        <strong>No external accounts linked yet.</strong>
                        <p>You can still sign in with your local account credentials.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection