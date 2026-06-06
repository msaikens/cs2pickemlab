<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    private array $allowedProviders = [
        'google',
        'apple',
        'orcid',
    ];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->allowedProviders, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, $this->allowedProviders, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->with('error', 'Unable to sign in with ' . ucfirst($provider) . '.');
        }

        $providerId = (string) $socialUser->getId();
        $email = $socialUser->getEmail()
            ? mb_strtolower($socialUser->getEmail())
            : null;

        if (! $providerId) {
            return redirect()
                ->route('login')
                ->with('error', 'The provider did not return a usable account ID.');
        }

        if (! $email) {
            return redirect()
                ->route('login')
                ->with('error', ucfirst($provider) . ' did not return an email address. Use email sign up instead.');
        }

        $user = DB::transaction(function () use ($provider, $providerId, $email, $socialUser): User {
            $socialAccount = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($socialAccount) {
                $socialAccount->update([
                    'provider_email' => $email,
                    'provider_name' => $socialUser->getName(),
                    'avatar_url' => $socialUser->getAvatar(),
                ]);

                return $socialAccount->user;
            }

            $user = User::query()
                ->where('email', $email)
                ->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?: Str::before($email, '@'),
                    'email' => $email,
                    'password' => null,
                    'avatar_url' => $socialUser->getAvatar(),
                    'role' => 'user',
                ]);
            }

            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $providerId,
                'provider_email' => $email,
                'provider_name' => $socialUser->getName(),
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            if (! $user->avatar_url && $socialUser->getAvatar()) {
                $user->update([
                    'avatar_url' => $socialUser->getAvatar(),
                ]);
            }

            return $user;
        });

        Auth::login($user, remember: true);

        return redirect()
            ->intended(route('home'))
            ->with('success', 'Signed in with ' . ucfirst($provider) . '.');
    }
}