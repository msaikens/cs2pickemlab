@extends('layouts.public', [
    'title' => 'Confirm Wallet Access | CS2 PickLab',
    'pageTitle' => 'Confirm Wallet Access',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth-confirm.css') }}">
@endpush

@section('content')
<section class="confirm-page">
    <div class="confirm-shell">
        <aside class="confirm-hero">
            <div>
                <p class="confirm-kicker">Wallet Security</p>

                <h1>Confirm wallet access.</h1>

                <p class="confirm-copy">
                    Your wallet contains private balance and transaction information. CS2 PickLab requires a fresh security check before showing it.
                </p>
            </div>

            <div class="confirm-security-list">
                <div class="confirm-security-item">
                    <div class="confirm-security-icon">1</div>

                    <div>
                        <strong>Private balance</strong>
                        <span>Your wallet is only visible to you and authorized staff.</span>
                    </div>
                </div>

                <div class="confirm-security-item">
                    <div class="confirm-security-icon">2</div>

                    <div>
                        <strong>Fresh confirmation</strong>
                        <span>We verify access before showing sensitive wallet data.</span>
                    </div>
                </div>

                <div class="confirm-security-item">
                    <div class="confirm-security-icon">3</div>

                    <div>
                        <strong>Protected activity</strong>
                        <span>Wallet history and funding activity stay behind this check.</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="confirm-card">
            <div class="confirm-card-header">
                <p class="confirm-kicker">Security Check</p>

                <h2>Continue to Wallet</h2>

                <div class="confirm-copy">
                    @if($hasTwoFactor && $hasPassword)
                        <p>
                            Choose a security method to continue to your wallet.
                        </p>

                        <p>
                            Use the 6-digit code from your authenticator app, or enter your account password below.
                        </p>
                    @elseif($hasTwoFactor)
                        <p>
                            Use your authenticator app to continue to your wallet.
                        </p>

                        <p>
                            Enter the 6-digit code from your authenticator app.
                        </p>
                    @elseif($hasPassword)
                        <p>
                            Enter your account password to continue.
                        </p>
                    @else
                        <p>
                            Your account uses Google sign-in, so we will send a one-time wallet access code to your email.
                        </p>

                        <p id="confirm-instruction">
                            Please allow a few minutes for the code to arrive. You should not need to check spam, but do so just to be thorough.
                        </p>
                    @endif
                </div>
            </div>

            @if(session('status'))
                <div class="confirm-alert">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="confirm-error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            @if($hasTwoFactor)
                <form method="POST" action="{{ route('wallet.confirm.2fa') }}" class="confirm-form">
                    @csrf

                    <div class="confirm-field">
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
                            @if(! $hasPassword) autofocus @endif
                        >

                        @error('code')
                            <p class="confirm-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="confirm-button">
                        Verify with Authenticator
                    </button>
                </form>
            @endif

            @if($hasPassword)
                <form method="POST" action="{{ route('password.confirm.store') }}" class="confirm-form">
                    @csrf

                    <div class="confirm-field">
                        <label for="password">Password</label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            @if(! $hasTwoFactor) autofocus @endif
                        >

                        @error('password')
                            <p class="confirm-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="confirm-button">
                        Confirm with Password
                    </button>
                </form>
            @endif

            @if(! $hasPassword && ! $hasTwoFactor)
                <form method="POST" action="{{ route('password.confirm.store') }}" class="confirm-form">
                    @csrf

                    <button type="submit" class="confirm-button">
                        Send Wallet Access Code
                    </button>
                </form>
            @endif

            <div class="confirm-meta">
                This confirmation is temporary. You may be asked to verify again later before viewing your wallet.
            </div>
        </div>
    </div>
</section>
@endsection
@php
    $user = $user ?? auth()->user();

    $hasPassword = $hasPassword ?? ! empty($user?->password);

    $hasTwoFactor = $hasTwoFactor ?? (
        ! empty($user?->two_factor_secret)
        && ! empty($user?->two_factor_confirmed_at)
    );
@endphp