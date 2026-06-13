@extends('layouts.public', [
    'title' => 'Edit Profile | CS2 PickLab',
    'pageTitle' => 'Edit Profile',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-edit.css') }}">
@endpush

@section('content')
<section class="edit-account-page">
    <div class="edit-account-back">
        <a href="{{ route('account.show') }}">← Back to Account</a>
    </div>

    <section class="edit-account-card">
        <header class="edit-account-hero">
            <p class="edit-account-kicker">Account Center</p>
            <h1>Edit Profile</h1>
            <p>Update your display identity, avatar, and gaming platform names.</p>
        </header>

        @if ($errors->any())
            <div class="edit-account-alert danger">
                <strong>Fix the following:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('account.update') }}"
            class="edit-account-form"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="edit-account-section">
                <div class="edit-account-section-heading">
                    <p class="edit-account-kicker">Identity</p>
                    <h2>Profile Basics</h2>
                </div>

                <div class="edit-account-grid">
                    <div class="edit-account-field">
                        <label for="name">Account Name</label>

                        <input
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                        >

                        @error('name')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="edit-account-field">
                        <label for="display_name">Display Name</label>

                        <input
                            id="display_name"
                            name="display_name"
                            value="{{ old('display_name', $user->profile?->display_name) }}"
                        >

                        @error('display_name')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="edit-account-field">
                        <label for="first_name">First Name</label>

                        <input
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $user->profile?->first_name) }}"
                        >

                        @error('first_name')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="edit-account-field">
                        <label for="last_name">Last Name</label>

                        <input
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $user->profile?->last_name) }}"
                        >

                        @error('last_name')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="edit-account-section">
                <div class="edit-account-section-heading">
                    <p class="edit-account-kicker">Avatar & Bio</p>
                    <h2>Public Profile</h2>
                </div>

                <div class="edit-account-grid">
                    <div class="edit-account-field full">
                        <label for="avatar_file">Avatar File</label>

                        <input
                            id="avatar_file"
                            name="avatar_file"
                            type="file"
                            accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml"
                        >

                        <p class="edit-account-help">
                            Upload an image file for your avatar. Supported formats: PNG, JPEG, WebP, GIF, SVG.
                        </p>

                        @error('avatar_file')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="edit-account-field full">
                        <label for="about">About Me</label>

                        <textarea id="about" name="about" rows="5">{{ old('about', $user->profile?->about) }}</textarea>

                        @error('about')
                            <p class="edit-account-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="edit-account-section">
                <div class="edit-account-section-heading">
                    <p class="edit-account-kicker">Gaming Handles</p>
                    <h2>Platform Names</h2>
                </div>

                <div class="edit-account-grid">
                    <div class="edit-account-field">
                        <label for="steam_name">Steam Name</label>

                        <input
                            id="steam_name"
                            name="steam_name"
                            value="{{ old('steam_name', $user->profile?->steam_name) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="steam_id">Steam ID</label>

                        <input
                            id="steam_id"
                            name="steam_id"
                            value="{{ old('steam_id', $user->profile?->steam_id) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="faceit_name">FACEIT Name</label>

                        <input
                            id="faceit_name"
                            name="faceit_name"
                            value="{{ old('faceit_name', $user->profile?->faceit_name) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="discord_name">Discord Name</label>

                        <input
                            id="discord_name"
                            name="discord_name"
                            value="{{ old('discord_name', $user->profile?->discord_name) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="twitch_name">Twitch Name</label>

                        <input
                            id="twitch_name"
                            name="twitch_name"
                            value="{{ old('twitch_name', $user->profile?->twitch_name) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="youtube_name">YouTube Name</label>

                        <input
                            id="youtube_name"
                            name="youtube_name"
                            value="{{ old('youtube_name', $user->profile?->youtube_name) }}"
                        >
                    </div>
                </div>
            </div>

            <div class="edit-account-section">
                <div class="edit-account-section-heading">
                    <p class="edit-account-kicker">Region</p>
                    <h2>Location Settings</h2>
                </div>

                <div class="edit-account-grid">
                    <div class="edit-account-field">
                        <label for="country">Country</label>

                        <input
                            id="country"
                            name="country"
                            value="{{ old('country', $user->profile?->country) }}"
                        >
                    </div>

                    <div class="edit-account-field">
                        <label for="timezone">Timezone</label>

                        <input
                            id="timezone"
                            name="timezone"
                            value="{{ old('timezone', $user->profile?->timezone) }}"
                        >
                    </div>
                </div>
            </div>

            <div class="edit-account-submit">
                <a href="{{ route('account.show') }}" class="edit-account-button secondary">
                    Cancel
                </a>

                <button type="submit" class="edit-account-button primary">
                    Save Profile
                </button>
            </div>
        </form>
    </section>
</section>
@endsection