@extends('layouts.app', ['title' => 'Steam Marketplace Profile | CS2 PickLab'])

@section('title', 'Steam Marketplace Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketplace-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/marketplace-profile.css') }}">
@endpush

@section('content')
<main class="marketplace-profile-page">
    <section class="marketplace-profile-shell">
        <header class="marketplace-profile-hero">
            <div class="marketplace-profile-kicker">Marketplace Setup</div>

            <h1>Steam Marketplace Profile</h1>

            <p>
                Finish your Steam verification, trade URL setup, and marketplace requirements
                before listing skins or requesting trades.
            </p>

            <div class="marketplace-status-pill {{ $user->canUseMarketplace() ? 'is-ready' : 'is-not-ready' }}">
                <span class="status-dot"></span>
                <strong>{{ $user->canUseMarketplace() ? 'Marketplace Ready' : 'Setup Incomplete' }}</strong>
            </div>
        </header>

        @if (session('success'))
            <div class="marketplace-alert marketplace-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="marketplace-alert marketplace-alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="marketplace-alert marketplace-alert-danger">
                <strong>Fix the following:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Readiness Checklist</span>
                <h2>Trading Requirements</h2>
                <p>Every item below must be complete before marketplace actions unlock.</p>
            </div>

            <div class="requirement-grid">
                <div class="requirement-tile {{ $user->isActive() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Account Active</strong>
                    <p>Your account is not suspended or banned.</p>
                </div>

                <div class="requirement-tile {{ $user->email_verified_at ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Email Verified</strong>
                    <p>Your email must be verified before trading.</p>
                </div>

                <div class="requirement-tile {{ $user->hasAcceptedMarketplaceTerms() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Terms Accepted</strong>
                    <p>You must accept the marketplace rules.</p>

                    @unless ($user->hasAcceptedMarketplaceTerms())
                        <a href="{{ route('marketplace.terms') }}">Review terms</a>
                    @endunless
                </div>

                <div class="requirement-tile {{ $user->hasVerifiedSteamAccount() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Steam Linked</strong>
                    <p>Your SteamID64 must be verified.</p>
                </div>

                <div class="requirement-tile {{ $user->hasPublicSteamProfile() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Steam Profile Public</strong>
                    <p>Your Steam profile must be public so marketplace identity can be verified.</p>
                </div>

                <div class="requirement-tile {{ $user->hasPublicSteamInventory() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Inventory Public</strong>
                    <p>Your CS2 inventory must be public before you can create marketplace listings.</p>
                </div>

                <div class="requirement-tile {{ $user->hasTradeProfileReady() ? 'complete' : 'incomplete' }}">
                    <div class="requirement-icon"></div>
                    <strong>Trade URL Saved</strong>
                    <p>Your trade URL must include partner and token.</p>
                </div>
            </div>
        </section>

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Steam Identity</span>
                <h2>Linked Steam Account</h2>
            </div>

            @if ($steamAccount)
                <div class="steam-identity-card">
                    @if ($steamAccount->avatar_url)
                        <img
                            src="{{ $steamAccount->avatar_url }}"
                            alt="{{ $steamAccount->persona_name ?? 'Steam User' }}"
                            class="steam-avatar"
                        >
                    @else
                        <div class="steam-avatar steam-avatar-placeholder">
                            {{ strtoupper(substr($steamAccount->persona_name ?? 'S', 0, 1)) }}
                        </div>
                    @endif

                    <div class="steam-identity-main">
                        <strong>{{ $steamAccount->persona_name ?? 'Steam User' }}</strong>
                        <p>{{ $steamAccount->steam_id_64 }}</p>

                        @if ($steamAccount->profile_url)
                            <a href="{{ $steamAccount->profile_url }}" target="_blank" rel="noopener">
                                View Steam Profile
                            </a>
                        @endif
                    </div>
                </div>

                <div class="marketplace-account-actions">
                    <form method="POST" action="{{ route('profile.steam.refresh') }}">
                        @csrf

                        <button type="submit" class="marketplace-button secondary">
                            Refresh Steam Profile
                        </button>
                    </form>

                    <form method="POST" action="{{ route('profile.steam.inventory.sync') }}">
                        @csrf

                        <button type="submit" class="marketplace-button primary">
                            Sync Inventory
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('profile.steam.unlink') }}"
                        onsubmit="return confirm('Unlinking Steam will disable marketplace access and cancel your active skin listings. Continue?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="marketplace-button danger">
                            Unlink Steam Account
                        </button>
                    </form>
                </div>

                <div class="marketplace-detail-grid">
                    <div>
                        <span>Linked</span>
                        <strong>{{ optional($steamAccount->linked_at)->format('M j, Y g:i A') ?? 'Unknown' }}</strong>
                    </div>

                    <div>
                        <span>Last Verified</span>
                        <strong>{{ optional($steamAccount->last_verified_at)->format('M j, Y g:i A') ?? 'Not verified' }}</strong>
                    </div>

                    <div>
                        <span>Profile Privacy</span>
                        <strong>
                            @switch((string) $steamAccount->profile_visibility)
                                @case('1')
                                    Private
                                    @break

                                @case('3')
                                    Public
                                    @break

                                @default
                                    Unknown
                            @endswitch
                        </strong>
                    </div>
                </div>
            @else
                <div class="marketplace-empty-state">
                    <strong>No Steam account linked yet.</strong>

                    <p>
                        Link your Steam account securely through Steam. Do not enter your Steam username
                        or password on this site.
                    </p>

                    <a href="{{ route('profile.steam.link') }}" class="steam-login-button" aria-label="Sign in through Steam">
                        <img
                            src="{{ asset('images/steamloginlong.png') }}"
                            alt="Sign in through Steam"
                        >
                    </a>
                </div>
            @endif
        </section>

        <section class="marketplace-card">
            <div class="marketplace-card-header centered">
                <span>Trade Access</span>
                <h2>Steam Trade URL</h2>
                <p>Save your trade URL so verified marketplace users can request trades.</p>
            </div>

            <form method="POST" action="{{ route('profile.steam.trade-url.update') }}" class="marketplace-form">
                @csrf

                <div class="marketplace-form-help-row">
                    <a href="{{ route('help.steam-trade-url') }}">
                        How do I find my trade URL?
                    </a>
                </div>

                <div class="form-row">
                    <label for="steam_trade_url">Steam Trade URL</label>

                    <input
                        id="steam_trade_url"
                        name="steam_trade_url"
                        type="url"
                        value="{{ old('steam_trade_url', $steamTradeProfile?->steam_trade_url) }}"
                        placeholder="https://steamcommunity.com/tradeoffer/new/?partner=123456789&token=abcdef"
                        required
                    >

                    <p class="form-help">
                        Your trade URL must contain both <code>partner</code> and <code>token</code>.
                    </p>
                </div>

                <label class="marketplace-check">
                    <input
                        type="checkbox"
                        name="trade_hold_warning_acknowledged"
                        value="1"
                        {{ $steamTradeProfile?->trade_hold_warning_acknowledged_at ? 'checked' : '' }}
                    >

                    <span>I understand Steam trade holds or account restrictions may still apply.</span>
                </label>

                <button type="submit" class="marketplace-button primary">
                    Save Trade URL
                </button>
            </form>

            @if ($steamTradeProfile)
                <div class="marketplace-detail-grid trade-details">
                    <div>
                        <span>Partner ID</span>
                        <strong>{{ $steamTradeProfile->trade_partner_id ?? 'Missing' }}</strong>
                    </div>

                    <div>
                        <span>Token</span>
                        <strong>{{ $steamTradeProfile->trade_token ? 'Saved' : 'Missing' }}</strong>
                    </div>

                    <div>
                        <span>Inventory Public</span>
                        <strong>{{ $steamTradeProfile->inventory_public ? 'Yes' : 'No / Unknown' }}</strong>
                    </div>

                    <div>
                        <span>Last Inventory Sync</span>
                        <strong>{{ optional($steamTradeProfile->last_inventory_sync_at)->format('M j, Y g:i A') ?? 'Never' }}</strong>
                    </div>
                </div>
            @endif
        </section>
    </section>
</main>
@endsection