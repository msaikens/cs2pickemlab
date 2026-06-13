@extends('layouts.app', [
    'title' => 'Reset Password | CS2 PickLab',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-page">
    <div class="auth-card">
        <header class="auth-header">
            <p class="auth-kicker">Account Recovery</p>
            <h1>Reset password</h1>

            <p>
                Choose a new password for your account.
            </p>
        </header>

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-field">
                <label for="email">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email"
                >

                @error('email')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password">New Password</label>

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
                <label for="password_confirmation">Confirm New Password</label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="auth-button primary">
                Reset password
            </button>
        </form>
    </div>
</section>
@endsection