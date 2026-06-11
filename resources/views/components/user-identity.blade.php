@props([
    'user',
    'size' => 'md',
    'showEmail' => false,
    'showAccountType' => false,
])

@php
    $avatarSize = match ($size) {
        'sm' => 'h-9 w-9 text-sm',
        'lg' => 'h-16 w-16 text-2xl',
        default => 'h-12 w-12 text-lg',
    };

    $nameSize = match ($size) {
        'sm' => 'text-sm',
        'lg' => 'text-xl',
        default => 'text-base',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-3']) }}>
    @if ($user?->avatar_url)
        <img
            src="{{ $user->avatar_url }}"
            alt="{{ $user->displayName() }}"
            class="{{ $avatarSize }} shrink-0 rounded-full border border-slate-700 object-cover"
        >
    @else
        <div class="{{ $avatarSize }} flex shrink-0 items-center justify-center rounded-full border border-slate-700 bg-slate-950 font-black text-cyan-300">
            {{ $user ? strtoupper(mb_substr($user->displayName(), 0, 1)) : '?' }}
        </div>
    @endif

    <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
            <p class="{{ $nameSize }} truncate font-black text-white">
                {{ $user?->displayName() ?? 'Unknown User' }}
            </p>

            @include('components.user-role-badge', [
                'user' => $user,
                'showFree' => $showAccountType,
                'showPremium' => $showAccountType,
            ])
        </div>

        @if ($user)
            <p class="truncate text-sm text-slate-400">
                Account: {{ $user->name ?: 'Unnamed Account' }}
            </p>
        @endif

        @if ($showEmail && $user?->email)
            <p class="truncate text-xs text-slate-500">
                {{ $user->email }}
            </p>
        @endif
    </div>
</div>