@extends('layouts.public', [
    'title' => 'Enter Wallet Access Code | CS2 PickLab',
    'pageTitle' => 'Enter Wallet Access Code',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth-confirm.css') }}">
@endpush

@section('content')
<section class="confirm-page">
    <div class="confirm-shell">
        <aside class="confirm-hero">
            <div>
                <p class="confirm-kicker">One-Time Code</p>
                <h1>Check your email.</h1>
                <p class="confirm-copy">
                    Enter the six-digit code we sent to your email address. The code expires after 10 minutes.
                </p>
            </div>

            <div class="confirm-security-list">
                <div class="confirm-security-item">
                    <div class="confirm-security-icon">✓</div>
                    <div>
                        <strong>Email verified access</strong>
                        <span>This lets Google sign-in users confirm wallet access without creating a password.</span>
                    </div>
                </div>

                <div class="confirm-security-item">
                    <div class="confirm-security-icon">⏱</div>
                    <div>
                        <strong>Expires quickly</strong>
                        <span>The access code is temporary and can only be used during this session.</span>
                    </div>
                </div>

                <div class="confirm-security-item">
                    <div class="confirm-security-icon">↻</div>
                    <div>
                        <strong>Need another?</strong>
                        <span>You can request a new code if the first one expires or gets lost.</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="confirm-card">
            <div class="confirm-card-header">
                <p class="confirm-kicker">Verify Code</p>
                <h2>Enter Access Code</h2>
                <p class="confirm-copy">
                    Use the six-digit wallet access code from your email.
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

            <form method="POST" action="{{ route('password.confirm.code.verify') }}" class="confirm-form">
                @csrf

                <div class="confirm-field">
                    <label for="code">Access Code</label>

                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        class="confirm-code-input"
                        required
                        autofocus
                    >

                    <p class="confirm-help">
                        The code is six digits and expires after 10 minutes.
                    </p>

                    @error('code')
                        <p class="confirm-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="confirm-button">
                    Confirm Wallet Access
                </button>
            </form>

            <form method="POST" action="{{ route('password.confirm.store') }}" class="confirm-secondary-action">
                @csrf

                <button type="submit" class="confirm-link-button">
                    Send a new code
                </button>
            </form>

            <div class="confirm-meta">
                If you did not request wallet access, ignore the email and keep your account signed in only on trusted devices.
            </div>
        </div>
    </div>
</section>
@endsection