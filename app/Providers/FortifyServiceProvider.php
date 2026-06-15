<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::confirmPasswordView(function () {
            $user = Request::user();

            return view('auth.confirm-password', [
                'user' => $user,
                'hasPassword' => ! empty($user?->password),
                'hasTwoFactor' => ! empty($user?->two_factor_secret)
                    && ! empty($user?->two_factor_confirmed_at),
            ]);
        });
    }
}