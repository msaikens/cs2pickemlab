@extends('layouts.app', ['title' => 'Contact CS2 PickLab'])

@section('title', 'Contact CS2 PickLab')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')
<section class="contact-page">
    <div class="contact-card">
        <header class="contact-hero">
            <p class="contact-kicker">Contact</p>

            <h1>Contact CS2 PickLab</h1>

            <p>
                Send a message about the site, Pick&#8217;em tools, match data, partnerships, or general feedback.
            </p>
        </header>

        @if (session('status'))
            <div class="contact-alert success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="contact-alert danger">
                <strong>Please fix the following:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="contact-form">
            @csrf

            <input type="hidden" name="form_started_at" value="{{ $formStartedAt }}">

            {{-- Honeypot: hidden from humans, attractive to bots --}}
            <div class="contact-honeypot" aria-hidden="true">
                <label for="website">Website</label>
                <input
                    id="website"
                    type="text"
                    name="website"
                    tabindex="-1"
                    autocomplete="off"
                    value=""
                >
            </div>

            <div class="contact-field">
                <label for="name">Name</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    maxlength="120"
                >
            </div>

            <div class="contact-field">
                <label for="email">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                >
            </div>

            <div class="contact-field">
                <label for="subject">Subject</label>

                <input
                    id="subject"
                    name="subject"
                    type="text"
                    value="{{ old('subject') }}"
                    maxlength="160"
                >
            </div>

            <div class="contact-field">
                <label for="message">Message</label>

                <textarea
                    id="message"
                    name="message"
                    rows="8"
                    required
                    maxlength="5000"
                >{{ old('message') }}</textarea>
            </div>

            @if ($turnstileSiteKey)
                <div class="contact-turnstile-box">
                    <div
                        class="cf-turnstile"
                        data-sitekey="{{ $turnstileSiteKey }}"
                        data-callback="onTurnstileSuccess"
                        data-expired-callback="onTurnstileExpired"
                        data-error-callback="onTurnstileError"
                    ></div>

                    <p id="turnstile-status" class="contact-turnstile-status">
                        Security check loading...
                    </p>
                </div>
            @endif

            <div class="contact-submit">
                <button
                    id="contact-submit-button"
                    type="submit"
                    @if ($turnstileSiteKey) disabled @endif
                    class="contact-button @if ($turnstileSiteKey) is-disabled @endif"
                >
                    Send Message
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@if ($turnstileSiteKey)
    @push('scripts')
        <script>
            function setContactButtonState(enabled, message) {
                const button = document.getElementById('contact-submit-button');
                const status = document.getElementById('turnstile-status');

                if (button) {
                    button.disabled = !enabled;
                    button.classList.toggle('is-disabled', !enabled);
                }

                if (status) {
                    status.textContent = message;
                }
            }

            window.onTurnstileSuccess = function () {
                setContactButtonState(true, 'Security check complete.');
            };

            window.onTurnstileExpired = function () {
                setContactButtonState(false, 'Security check expired. Please refresh or try again.');
            };

            window.onTurnstileError = function () {
                setContactButtonState(false, 'Security check failed to load. Please refresh the page.');
            };
        </script>

        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif