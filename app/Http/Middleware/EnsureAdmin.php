<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('warning', 'Please sign in to access the admin area.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}