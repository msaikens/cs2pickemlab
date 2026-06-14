<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class WalletTopUpController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_dollars' => ['required', 'numeric', 'min:5', 'max:500'],
        ]);

        $amountCents = (int) round(((float) $validated['amount_dollars']) * 100);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],

            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => config('services.stripe.currency', 'usd'),
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' => 'CS2 PickLab Wallet Top-Up',
                            'description' => 'Adds balance to your CS2 PickLab wallet.',
                        ],
                    ],
                ],
            ],

            'success_url' => route('wallet.topup.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('wallet.topup.cancel'),

            'metadata' => [
                'type' => 'wallet_topup',
                'user_id' => (string) Auth::id(),
                'amount_cents' => (string) $amountCents,
            ],

            'payment_intent_data' => [
                'metadata' => [
                    'type' => 'wallet_topup',
                    'user_id' => (string) Auth::id(),
                    'amount_cents' => (string) $amountCents,
                ],
            ],
        ]);

        return redirect()->away($session->url);
    }

    public function success(): RedirectResponse
    {
    return redirect()
        ->route('account.show')
        ->with('status', 'Wallet top-up payment received. Your balance has been updated.');
    }

    public function cancel(): RedirectResponse
    {
    return redirect()
        ->route('account.show')
        ->with('status', 'Wallet top-up cancelled.');
    }
}