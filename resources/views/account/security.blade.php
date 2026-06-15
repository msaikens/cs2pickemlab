@extends('layouts.public', [
    'title' => 'Account Security | CS2 PickLab',
    'pageTitle' => 'Account Security',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-security.css') }}">
@endpush

@section('content')
@php
    $twoFactorSecret = ! empty($user->two_factor_secret);
    $twoFactorConfirmed = ! empty($user->two_factor_confirmed_at);

    $twoFactorAvailable =
        method_exists($user, 'twoFactorQrCodeSvg')
        && method_exists($user, 'recoveryCodes');

    $recoveryCodes = [];

    if ($twoFactorAvailable && $twoFactorConfirmed && ! empty($user->two_factor_recovery_codes)) {
        $recoveryCodes = $user->recoveryCodes();
    }
@endphp

<section class="security-page">
    <div class="security-back">
        <a href="{{ route('account.show') }}">← Back to Account</a>
    </div>

    <header class="security-hero">
        <p class="security-kicker">Account Center</p>
        <h1>Account Security</h1>
        <p>Update your password, manage authenticator app protection, and review external sign-in providers connected to your account.</p>
    </header>

    @if(session('success'))
        <div class="security-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('status') === 'two-factor-authentication-enabled')
        <div class="security-alert success">
            Two-factor authentication has been started. Scan the QR code below, then enter the 6-digit code from your authenticator app.
        </div>
    @endif

    @if(session('status') === 'two-factor-authentication-confirmed')
        <div class="security-alert success">
            Two-factor authentication is now enabled.
        </div>
    @endif

    @if(session('status') === 'two-factor-authentication-disabled')
        <div class="security-alert success">
            Two-factor authentication has been disabled.
        </div>
    @endif

    @if(session('status') === 'recovery-codes-generated')
        <div class="security-alert success">
            New recovery codes were generated.
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
                <p class="security-kicker">Authenticator App</p>
                <h2>Two-Factor Authentication</h2>
                <p>Use a 6-digit authenticator code for stronger account protection and faster wallet confirmation.</p>
            </div>

            @if(! $twoFactorAvailable)
                <div class="security-empty">
                    <strong>Two-factor authentication is not wired into the app yet.</strong>
                    <p>
                        Install and configure Laravel Fortify, then add the TwoFactorAuthenticatable trait to your User model.
                    </p>

                    <code>composer require laravel/fortify</code>
                    <code>php artisan fortify:install</code>
                    <code>php artisan migrate</code>
                </div>
            @elseif(! $twoFactorSecret)
                <div class="security-2fa-panel">
                    <p>
                        Two-factor authentication is currently disabled.
                    </p>

                    <form method="POST" action="/user/two-factor-authentication" class="security-form">
                        @csrf

                        <button type="submit" class="security-button primary">
                            Enable Two-Factor Authentication
                        </button>
                    </form>
                </div>
            @elseif($twoFactorSecret && ! $twoFactorConfirmed)
                <div class="security-2fa-panel">
                    <p>
                        Scan this QR code with Google Authenticator, Microsoft Authenticator, 1Password, Authy, or another authenticator app.
                    </p>

                    <div class="security-qr-box">
                        {!! $user->twoFactorQrCodeSvg() !!}
                    </div>

                    <form method="POST" action="/user/confirmed-two-factor-authentication" class="security-form">
                        @csrf

                        <div class="security-field">
                            <label for="code">Authenticator Code</label>

                            <input
                                id="code"
                                name="code"
                                type="text"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                maxlength="6"
                                placeholder="123456"
                                required
                            >

                            @error('code')
                                <p class="security-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="security-button primary">
                            Confirm Two-Factor Authentication
                        </button>
                    </form>

                    <form method="POST" action="/user/two-factor-authentication" class="security-inline-form">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="security-button secondary">
                            Cancel Setup
                        </button>
                    </form>
                </div>
            @else
                <div class="security-2fa-panel">
                    <div class="security-enabled">
                        <strong>Two-factor authentication is enabled.</strong>
                        <p>You can now use your authenticator app code to confirm wallet access.</p>
                    </div>

                    @if(count($recoveryCodes))
                        <div class="security-recovery-codes">
                            <h3>Recovery Codes</h3>

                            <p>
                                Store these somewhere safe. Recovery codes can help you regain access if you lose your authenticator app.
                            </p>

                            <ul>
                                @foreach($recoveryCodes as $recoveryCode)
                                    <li><code>{{ $recoveryCode }}</code></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="security-button-row">
                        <form method="POST" action="/user/two-factor-recovery-codes">
                            @csrf

                            <button type="submit" class="security-button secondary">
                                Regenerate Recovery Codes
                            </button>
                        </form>

                        <form method="POST" action="/user/two-factor-authentication">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="security-button danger">
                                Disable Two-Factor Authentication
                            </button>
                        </form>
                    </div>
                </div>
            @endif
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