@extends('layouts.public', [
    'title' => 'Edit Profile | CS2 PickLab',
    'pageTitle' => 'Edit Profile',
])

@section('content')
<section class="mx-auto max-w-4xl px-6 py-10">
    <div class="mb-6">
        <a href="{{ route('account.show') }}" class="link-accent">← Back to Account</a>
    </div>

    <div class="card">
        <h1 class="text-3xl font-black text-white">Edit Profile</h1>
        <p class="mt-2 text-slate-400">Update your display identity, avatar, and gaming platform names.</p>

        <form method="POST" action="{{ route('account.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="form-label" for="name">Account Name</label>
                    <input id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" enctype="multipart/form-data">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="display_name">Display Name</label>
                    <input id="display_name" name="display_name" class="form-input" value="{{ old('display_name', $user->profile?->display_name) }}">
                    @error('display_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="first_name">First Name</label>
                    <input id="first_name" name="first_name" class="form-input" value="{{ old('first_name', $user->profile?->first_name) }}">
                    @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" class="form-input" value="{{ old('last_name', $user->profile?->last_name) }}">
                    @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label" for="avatar_file">Avatar File - Click the box to Upload</label>
                    <input id="avatar_file" name="avatar_file" type="file" class="form-input" accept="image/png,image/jpeg,image/webp,image.gif,image/svg+xml">
                    <p class="form-help">Upload an image file for your avatar. Supported formats: PNG, JPEG, WebP, GIF, SVG.</p>
                    @error('avatar_file') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label" for="about">About Me</label>
                    <textarea id="about" name="about" rows="5" class="form-input">{{ old('about', $user->profile?->about) }}</textarea>
                    @error('about') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="steam_name">Steam Name</label>
                    <input id="steam_name" name="steam_name" class="form-input" value="{{ old('steam_name', $user->profile?->steam_name) }}">
                </div>

                <div>
                    <label class="form-label" for="steam_id">Steam ID</label>
                    <input id="steam_id" name="steam_id" class="form-input" value="{{ old('steam_id', $user->profile?->steam_id) }}">
                </div>

                <div>
                    <label class="form-label" for="faceit_name">FACEIT Name</label>
                    <input id="faceit_name" name="faceit_name" class="form-input" value="{{ old('faceit_name', $user->profile?->faceit_name) }}">
                </div>

                <div>
                    <label class="form-label" for="discord_name">Discord Name</label>
                    <input id="discord_name" name="discord_name" class="form-input" value="{{ old('discord_name', $user->profile?->discord_name) }}">
                </div>

                <div>
                    <label class="form-label" for="twitch_name">Twitch Name</label>
                    <input id="twitch_name" name="twitch_name" class="form-input" value="{{ old('twitch_name', $user->profile?->twitch_name) }}">
                </div>

                <div>
                    <label class="form-label" for="youtube_name">YouTube Name</label>
                    <input id="youtube_name" name="youtube_name" class="form-input" value="{{ old('youtube_name', $user->profile?->youtube_name) }}">
                </div>

                <div>
                    <label class="form-label" for="country">Country</label>
                    <input id="country" name="country" class="form-input" value="{{ old('country', $user->profile?->country) }}">
                </div>

                <div>
                    <label class="form-label" for="timezone">Timezone</label>
                    <input id="timezone" name="timezone" class="form-input" value="{{ old('timezone', $user->profile?->timezone) }}">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('account.show') }}" class="btn-secondary-lg">Cancel</a>
                <button type="submit" class="btn-primary-lg">Save Profile</button>
            </div>
        </form>
    </div>
</section>
@endsection
