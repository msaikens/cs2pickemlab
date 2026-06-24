<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\WalletTermsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletTermsService $walletTerms
    ) {
    }

    public function show(Request $request): View
    {
        $user = $request->user();

        $wallet = $user->wallet()->firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'currency' => config('services.stripe.currency', 'usd'),
                'available_balance_cents' => 0,
                'pending_balance_cents' => 0,
            ]
        );

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);

        $walletTermsAcceptance = $this->walletTerms->currentAcceptanceFor($user);

        return view('account.wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'walletTermsVersion' => $this->walletTerms->currentVersion(),
            'walletTermsAcceptance' => $walletTermsAcceptance,
            'hasAcceptedWalletTerms' => $walletTermsAcceptance !== null,
        ]);
    }
}