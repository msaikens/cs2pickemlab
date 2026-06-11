<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketplaceReady
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'You must sign in before using the marketplace.');
        }

        if (! $user->isActive()) {
            abort(403, 'Your account is not active.');
        }

        if ($user->email_verified_at === null) {
            return redirect()
                ->route('verification.notice')
                ->with('error', 'Please verify your email before using the marketplace.');
        }

        if (! $user->hasAcceptedMarketplaceTerms()) {
            return redirect()
                ->route('marketplace.terms')
                ->with('error', 'Please accept the marketplace terms before trading.');
        }

        if (! $user->hasVerifiedSteamAccount()) {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Please link and verify your Steam account before trading.');
        }

        if (! $user->hasTradeProfileReady()) {
            return redirect()
                ->route('profile.steam')
                ->with('error', 'Please add your Steam trade URL before trading.');
        }

        return $next($request);
    }
}