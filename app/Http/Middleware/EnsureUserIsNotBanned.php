<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($this->isAllowedRestrictionRoute($request)) {
            return $next($request);
        }

        if ($user->isSiteBanned()) {
            return redirect()->route('account.banned');
        }

        if ($user->isSiteSuspended()) {
            return redirect()->route('account.suspended');
        }

        return $next($request);
    }

    private function isAllowedRestrictionRoute(Request $request): bool
    {
        return $request->routeIs([
            'account.banned',
            'account.suspended',
            'account.moderation-appeals.store',
            'logout',
        ]);
    }
}