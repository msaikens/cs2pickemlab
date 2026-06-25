<!-- app/resoureces/views/components/navigation-link.blade.php -->

@props([
    'route',
    'label',
    'class' => '',
    'activePattern' => null,
])

@php
    $routeExists = \Illuminate\Support\Facades\Route::has($route);

    $isActive = $routeExists
        ? request()->routeIs($activePattern ?: $route)
        : false;

    $classes = trim(($class ?? '') . ' ' . ($isActive ? 'active' : ''));
@endphp

@if($routeExists)
    <a
    href="{{ route($route) }}"
    @if($isActive) aria-current="page" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
        {{ $label }}
    </a>
@endif