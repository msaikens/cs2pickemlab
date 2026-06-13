@extends('layouts.app', [
    'title' => 'Forgot Password | CS2 PickLab',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<section class="auth-page">
    <div class="auth-card">
        <header class="auth-header">
            <p class="auth-kicker">Account Recovery</p>
            <h1>Forgot password</h1>

            <p>
                Enter your email address and we&#8217;ll send you a password reset link.
            </p>
        </header>

        @if(session('success'))
            <div class="auth-alert success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
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

            <button type="submit" class="auth-button primary">
                Send reset link
            </button>
        </form>

        <p class="auth-switch">
            Remembered your password?
            <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</section>
@endsection