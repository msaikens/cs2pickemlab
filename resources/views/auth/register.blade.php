@extends('layouts.app', [
    'title' => 'Create Account | CS2 PickLab',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-page">
    <div class="auth-card">
        <header class="auth-header">
            <p class="auth-kicker">Join CS2 PickLab</p>
            <h1>Create account</h1>

            <p>
                Save your profile, future purchases, and Pick&#8217;em activity.
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
                    Sign up with Google
                </a>
            </div>

            <div class="auth-divider">
                <span></span>
                <strong>or</strong>
                <span></span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="auth-form">
            @csrf

            <div class="auth-field">
                <label for="name">Name</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                >

                @error('name')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="email">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
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
                    autocomplete="new-password"
                >

                @error('password')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password_confirmation">Confirm Password</label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="auth-button primary">
                Create account
            </button>
        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</section>
@endsection