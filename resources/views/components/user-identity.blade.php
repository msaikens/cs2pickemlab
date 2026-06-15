@props([
    'user',
    'size' => 'md',
    'showEmail' => false,
    'showAccountType' => false,
    'showTwoFactorStatus' => false,
    'showTwoFactorLink' => false,
])

@php
    $sizeClass = match ($size) {
        'sm' => 'small',
        'lg' => 'large',
        default => 'medium',
    };

    $hasTwoFactor = ! empty($user?->two_factor_secret)
        && ! empty($user?->two_factor_confirmed_at);
@endphp

<div {{ $attributes->merge(['class' => 'user-identity ' . $sizeClass]) }}>
    @if ($user?->avatar_url)
        <img
            src="{{ $user->avatar_url }}"
            alt="{{ $user->displayName() }}"
            class="user-identity-avatar"
        >
    @else
        <div class="user-identity-avatar placeholder">
            {{ $user ? strtoupper(mb_substr($user->displayName(), 0, 1)) : '?' }}
        </div>
    @endif

    <div class="user-identity-main">
        <div class="user-identity-name-row">
            <p class="user-identity-name">
                {{ $user?->displayName() ?? 'Unknown User' }}
            </p>

            @include('components.user-role-badge', [
                'user' => $user,
                'showFree' => $showAccountType,
                'showPremium' => $showAccountType,
            ])

            @if($showTwoFactorStatus && $user)
                <span class="user-identity-2fa-badge {{ $hasTwoFactor ? 'enabled' : 'disabled' }}">
                    {{ $hasTwoFactor ? '2FA Enabled' : '2FA Off' }}
                </span>
            @endif
        </div>

        @if ($user)
            <p class="user-identity-account">
                Account: {{ $user->name ?: 'Unnamed Account' }}
            </p>
        @endif

        @if ($showEmail && $user?->email)
            <p class="user-identity-email">
                {{ $user->email }}
            </p>
        @endif

        @if($showTwoFactorLink && $user)
            <p class="user-identity-security-link-row">
                <a href="{{ route('account.security') }}" class="user-identity-security-link">
                    {{ $hasTwoFactor ? 'Manage two-factor authentication' : 'Set up two-factor authentication' }}
                </a>
            </p>
        @endif
    </div>
</div>