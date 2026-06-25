<!-- app/views/components/user-role-badge.blade.php -->

@props([
    'user',
    'showFree' => false,
    'showPremium' => true,
])

@php
    $label = null;
    $class = null;

    if ($user?->isAdmin()) {
        $label = 'Administrator';
        $class = 'admin';
    } elseif ($user?->isModerator()) {
        $label = 'Moderator';
        $class = 'moderator';
    } elseif ($showPremium && $user?->hasActiveSubscription()) {
        $label = 'Premium User';
        $class = 'premium';
    } elseif ($showFree) {
        $label = 'Free User';
        $class = 'free';
    }
@endphp

@if ($label)
    <span {{ $attributes->merge(['class' => 'user-role-badge ' . $class]) }}>
        {{ $label }}
    </span>
@endif