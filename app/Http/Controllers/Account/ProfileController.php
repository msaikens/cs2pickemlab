<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load([
            'profile',
            'socialAccounts',
            'emailVerificationCodes',
        ]);

        return view('account.show', compact('user'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile');

        $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $user->load('profile');

        return view('account.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
            'avatar_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],

            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:2000'],

            'steam_name' => ['nullable', 'string', 'max:255'],
            'steam_id' => ['nullable', 'string', 'max:255'],
            'faceit_name' => ['nullable', 'string', 'max:255'],
            'discord_name' => ['nullable', 'string', 'max:255'],
            'twitch_name' => ['nullable', 'string', 'max:255'],
            'youtube_name' => ['nullable', 'string', 'max:255'],

            'country' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $avatarUrl = $data['avatar_url'] ?? $user->avatar_url;

        if ($request->hasFile('avatar_file')) {
            if ($user->avatar_url && str_starts_with($user->avatar_url, '/storage/avatars/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar_file')->store('avatars', 'public');
            $avatarUrl = Storage::url($path);
        }

        $user->update([
            'name' => $data['name'] ?? null,
            'avatar_url' => $avatarUrl,
        ]);

        $profileData = collect($data)
            ->only([
                'first_name',
                'last_name',
                'display_name',
                'about',
                'steam_name',
                'steam_id',
                'faceit_name',
                'discord_name',
                'twitch_name',
                'youtube_name',
                'country',
                'timezone',
            ])
            ->toArray();

        UserProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()
            ->route('account.show')
            ->with('success', 'Profile updated.');
    }
}