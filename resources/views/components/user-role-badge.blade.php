@props([
    'user',
    'showFree' => false,
    'showPremium' => true,
])

@php
    $label = null;
    $classes = null;

    if ($user?->isAdmin()) {
        $label = 'Administrator';
        $classes = 'border-red-400/50 bg-red-500/10 text-red-200';
    } elseif ($user?->isModerator()) {
        $label = 'Moderator';
        $classes = 'border-violet-400/50 bg-violet-500/10 text-violet-200';
    } elseif ($showPremium && $user?->hasActiveSubscription()) {
        $label = 'Premium User';
        $classes = 'border-cyan-400/50 bg-cyan-400/10 text-cyan-200';
    } elseif ($showFree) {
        $label = 'Free User';
        $classes = 'border-slate-600 bg-slate-800 text-slate-300';
    }
@endphp

@if ($label)
    <span {{ $attributes->merge([
        'class' => 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-black uppercase tracking-wide ' . $classes,
    ]) }}>
        {{ $label }}
    </span>
@endif