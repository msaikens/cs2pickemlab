@props([
    'user',
    'size' => 'md',
    'showEmail' => false,
    'showAccountType' => false,
])

@php
    $sizeClass = match ($size) {
        'sm' => 'small',
        'lg' => 'large',
        default => 'medium',
    };
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
    </div>
</div>