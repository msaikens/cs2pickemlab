<?php

namespace App\Http\Middleware;

use App\Services\WalletTermsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWalletTermsAccepted
{
    public function __construct(
        private readonly WalletTermsService $walletTerms
    ) {
    }

    public function handle(Request $request, Closure $next, ?string $source = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('warning', 'Please sign in before using wallet features.');
        }

        if (! $this->walletTerms->hasAcceptedCurrentTerms($user)) {
            return redirect()
                ->route('wallet.terms', [
                    'source' => $this->walletTerms->cleanSource($source),
                ])
                ->with('warning', 'Please review and accept the Wallet Terms before continuing.');
        }

        return $next($request);
    }
}