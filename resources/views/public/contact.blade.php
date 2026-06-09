@extends('layouts.app')

@section('title', 'Contact CS2 PickLab')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-12">
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-6 shadow-xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-sky-400">
                Contact
            </p>

            <h1 class="mt-2 text-3xl font-bold text-white">
                Contact CS2 PickLab
            </h1>

            <p class="mt-3 text-slate-300">
                Send a message about the site, pick’em tools, match data, partnerships, or general feedback.
            </p>

            @if (session('status'))
                <div class="mt-6 rounded-lg border border-emerald-700 bg-emerald-950/60 px-4 py-3 text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-700 bg-red-950/60 px-4 py-3 text-red-200">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                @csrf

                <input type="hidden" name="form_started_at" value="{{ $formStartedAt }}">

                {{-- Honeypot: hidden from humans, attractive to bots --}}
                <div class="hidden" aria-hidden="true">
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

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-200">
                        Name
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        maxlength="120"
                        class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-white outline-none focus:border-sky-500"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-200">
                        Email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="255"
                        class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-white outline-none focus:border-sky-500"
                    >
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-200">
                        Subject
                    </label>
                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        maxlength="160"
                        class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-white outline-none focus:border-sky-500"
                    >
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-slate-200">
                        Message
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        rows="8"
                        required
                        maxlength="5000"
                        class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-white outline-none focus:border-sky-500"
                    >{{ old('message') }}</textarea>
                </div>

                @if ($turnstileSiteKey)
                    <div class="rounded-xl border border-white/15 bg-slate-900/70 p-4">
                        <div
                            class="cf-turnstile"
                            data-sitekey="{{ $turnstileSiteKey }}"
                            data-callback="onTurnstileSuccess"
                            data-expired-callback="onTurnstileExpired"
                            data-error-callback="onTurnstileError">
                        </div>

                            <p id="turnstile-status" class="mt-3 text-sm text-slate-400">
                                Security check loading...
                            </p>
                        </div>
                    </div>  

                    <script>
                        function setContactButtonState(enabled, message) {
                        const button = document.getElementById('contact-submit-button');
            const status = document.getElementById('turnstile-status');

            if (button) {
                button.disabled = !enabled;

                button.classList.toggle('opacity-50', !enabled);
                button.classList.toggle('cursor-not-allowed', !enabled);
                button.classList.toggle('hover:bg-white', enabled);
                button.classList.toggle('hover:text-slate-950', enabled);
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
@endif

<div class="pt-2">
    <button
        id="contact-submit-button"
        type="submit"
        @if ($turnstileSiteKey) disabled @endif
        class="inline-flex items-center justify-center rounded-xl border border-white bg-transparent px-6 py-3 font-semibold text-white shadow-sm transition duration-150 @if ($turnstileSiteKey) opacity-50 cursor-not-allowed @else hover:bg-white hover:text-slate-950 @endif"
    >
        Send Message
    </button>
</div>
    </section>
@endsection