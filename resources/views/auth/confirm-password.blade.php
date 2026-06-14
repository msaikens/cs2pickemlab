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
                <p class="confirm-copy">
                    @if($hasPassword)
                        Enter your account password to continue.
                    @else
                        Your account uses Google sign-in, so we will send a one-time wallet access code to your email.
                    @endif
                </p>
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
                            autofocus
                        >

                        @error('password')
                            <p class="confirm-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="confirm-button">
                        Confirm Access
                    </button>
                </form>
            @else
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