<?php

namespace App\Http\Controllers;

use App\Models\WalletTermsAcceptance;
use App\Services\WalletTermsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletTermsController extends Controller
{
    public function __construct(
        private readonly WalletTermsService $walletTerms
    ) {
    }

    public function show(Request $request): View
    {
        $source = $this->walletTerms->cleanSource(
            $request->query('source', WalletTermsAcceptance::SOURCE_WALLET_TERMS_PAGE)
        );

        $user = $request->user();

        return view('wallet.terms', [
            'termsVersion' => $this->walletTerms->currentVersion(),
            'hasAccepted' => $user
                ? $this->walletTerms->hasAcceptedCurrentTerms($user)
                : false,
            'source' => $source,
        ]);
    }

    public function accept(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'accepted' => ['accepted'],
            'source' => ['nullable', 'string', 'max:64'],
        ]);

        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('warning', 'Please sign in to accept the Wallet Terms.');
        }

        $source = $this->walletTerms->cleanSource(
            $validated['source'] ?? WalletTermsAcceptance::SOURCE_WALLET_TERMS_PAGE
        );

        $this->walletTerms->acceptCurrentTerms($user, $request, $source);

        return $this->redirectAfterAcceptance($source);
    }

    private function redirectAfterAcceptance(string $source): RedirectResponse
    {
        return match ($source) {
            WalletTermsAcceptance::SOURCE_TOP_UP_GATE => redirect()
                ->route('account.wallet')
                ->with('status', 'Wallet Terms accepted. You can now add funds.'),

            WalletTermsAcceptance::SOURCE_MARKETPLACE_GATE => redirect()
                ->route('marketplace.listings.create')
                ->with('success', 'Wallet Terms accepted. You can now use marketplace features.'),

            WalletTermsAcceptance::SOURCE_WITHDRAWAL_GATE => redirect()
                ->route('account.wallet')
                ->with('status', 'Wallet Terms accepted. You can now continue with eligible wallet actions.'),

            default => redirect()
                ->route('account.wallet')
                ->with('status', 'Wallet Terms accepted. You can now use wallet features.'),
        };
    }
}