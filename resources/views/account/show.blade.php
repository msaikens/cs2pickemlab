@extends('layouts.public', [
    'title' => 'My Account | CS2 PickLab',
    'pageTitle' => 'My Account',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-show.css') }}">
@endpush

@section('content')
<section class="account-page">
    <header class="account-hero">
        <div>
            <p class="account-kicker">Account Center</p>

            <h1>My Account</h1>

            <p>
                Manage your CS2 PickLab profile, verification, and account settings.
            </p>
        </div>

        <div class="account-actions">
            <a href="{{ route('account.edit') }}" class="account-button primary">
                Edit Profile
            </a>

            <a href="{{ route('account.security') }}" class="account-button secondary">
                Security
            </a>
            @auth
                @if(auth()->id() === $user->id || auth()->user()?->isAdmin() || auth()->user()?->isModerator())
                <a href="{{ route('account.wallet') }}" class="account-button wallet">
                    Wallet
                </a>
                @endif
            @endauth
        </div>
    </header>

    @if(app()->environment(['local', 'development', 'staging']) || auth()->user()?->isAdmin())
        <form
            method="POST"
            action="{{ route('account.complete-resync') }}"
            class="account-dev-sync"
            onsubmit="return confirm('Run a complete account re-sync? This will repair missing profile, Steam, and marketplace records for your account.');"
        >
            @csrf

            <button type="submit" class="account-button secondary">
                Complete Re&#8209;Sync
            </button>
        </form>
    @endif

    @if(session('success'))
        <div class="account-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="account-alert danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="account-alert danger">
            <strong>Fix the following:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (! $user->hasVerifiedEmail())
        <section class="account-verification warning">
            <div class="account-verification-header">
                <p class="account-kicker">Account Verification</p>

                <h2>Verify Your Email</h2>

                <p>
                    We sent a verification link and one-time code to
                    <strong>{{ $user->email }}</strong>.
                    Delivery can take up to a minute. Check spam if it does not arrive.
                    Click the email link or enter the six-digit code below.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.code.verify') }}" class="account-code-form">
                @csrf

                <div class="account-field">
                    <label for="verification_code">One-Time Verification Code</label>

                    <input
                        id="verification_code"
                        name="verification_code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="123456"
                        required
                        class="account-code-input"
                    >
                </div>

                <button type="submit" class="account-button primary">
                    Verify Email
                </button>
            </form>

            <form method="POST" action="{{ route('verification.send') }}" class="account-resend-form">
                @csrf

                <button type="submit" class="account-button secondary">
                    Send New Code
                </button>
            </form>
        </section>
    @else
        <section class="account-verification success">
            <p class="account-kicker">Account Verification</p>

            <h2>Email successfully verified.</h2>

            <p>
                Your account email is verified and marketplace verification can continue.
            </p>
        </section>
    @endif

    <div class="account-grid">
        <section class="account-card account-profile-card">
            <div class="account-avatar-wrap">
                @if($user->avatar_url)
                    <img
                        src="{{ $user->avatar_url }}"
                        alt="{{ $user->displayName() }}"
                        class="account-avatar"
                    >
                @else
                    <div class="account-avatar-placeholder">
                        {{ strtoupper(mb_substr($user->displayName(), 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="account-profile-main">
                <div class="account-name-row">
                    <h2>{{ $user->displayName() }}</h2>

                    @include('components.user-role-badge', [
                        'user' => $user,
                        'showFree' => false,
                        'showPremium' => false,
                    ])
                </div>

                <p class="account-email">
                    {{ $user->email }}
                </p>

                @if($user->profile?->first_name || $user->profile?->last_name)
                    <p class="account-real-name">
                        {{ trim(($user->profile?->first_name ?? '') . ' ' . ($user->profile?->last_name ?? '')) }}
                    </p>
                @endif

                <div class="account-badges">
                    @if ($user->hasVerifiedEmail())
                        <span class="account-pill verified">
                            Email Verified
                        </span>
                    @else
                        <span class="account-pill unverified">
                            Email Not Verified
                        </span>
                    @endif

                    @include('components.user-role-badge', [
                        'user' => $user,
                        'showFree' => true,
                        'showPremium' => true,
                    ])
                </div>
            </div>
        </section>
        <section class="account-card account-about-card">
            <div class="account-card-heading">
                <p class="account-kicker">Profile Details</p>

                <h2>About</h2>
            </div>

            <p class="account-about-text">
                {{ $user->profile?->about ?: 'No profile details added yet.' }}
            </p>

            <div class="account-detail-grid">
                <div>
                    <span>Account Name</span>
                    <strong>{{ $user->name ?: '—' }}</strong>
                </div>

                <div>
                    <span>Display Name</span>
                    <strong>{{ $user->displayName() ?: '—' }}</strong>
                </div>

                <div>
                    <span>First Name</span>
                    <strong>{{ $user->profile?->first_name ?: '—' }}</strong>
                </div>

                <div>
                    <span>Last Name</span>
                    <strong>{{ $user->profile?->last_name ?: '—' }}</strong>
                </div>

                <div>
                    <span>Steam</span>
                    <strong>{{ $user->profile?->steam_name ?: '—' }}</strong>
                </div>

                <div>
                    <span>FACEIT</span>
                    <strong>{{ $user->profile?->faceit_name ?: '—' }}</strong>
                </div>

                <div>
                    <span>Discord</span>
                    <strong>{{ $user->profile?->discord_name ?: '—' }}</strong>
                </div>

                <div>
                    <span>Twitch</span>
                    <strong>{{ $user->profile?->twitch_name ?: '—' }}</strong>
                </div>
            </div>
        </section>
    </div>
</section>
@endsection