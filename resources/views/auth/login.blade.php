@extends('layouts.app', [
    'title' => 'Sign In | CS2 PickLab',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-page">
    <div class="auth-card">
        <header class="auth-header">
            <p class="auth-kicker">Welcome Back</p>
            <h1>Sign in</h1>

            <p>
                Access your CS2 PickLab account.
            </p>
        </header>

        @if(session('success'))
            <div class="auth-alert success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="auth-alert danger">
                {{ session('error') }}
            </div>
        @endif

        @if(config('services.google.client_id'))
            <div class="auth-social">
                <a href="{{ route('social.redirect', 'google') }}" class="auth-button social">
                    Continue with Google
                </a>
            </div>

            <div class="auth-divider">
                <span></span>
                <strong>or</strong>
                <span></span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf

            <div class="auth-field">
                <label for="email">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                >

                @error('email')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password">Password</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                >

                @error('password')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-row">
                <label class="auth-check">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                    >

                    <span>Remember me</span>
                </label>

                <a href="{{ route('password.request') }}" class="auth-link">
                    Forgot password?
                </a>
            </div>

            <button type="submit" class="auth-button primary">
                Sign in
            </button>
        </form>

        <p class="auth-switch">
            No account?
            <a href="{{ route('register') }}">Create one</a>
        </p>
    </div>
</section>
@endsection