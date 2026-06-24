<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\WalletTermsAcceptance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Throwable;

class WalletTopUpController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount_dollars' => ['required', 'numeric', 'min:5', 'max:500'],
        ]);

        $user = $request->user();

        if (! $user || ! $user->hasAcceptedCurrentWalletTerms()) {
            return redirect()
                ->route('wallet.terms', [
                    'source' => WalletTermsAcceptance::SOURCE_TOP_UP_GATE,
                ])
                ->with('warning', 'Please review and accept the Wallet Terms before adding funds.');
        }

        $stripeSecret = config('services.stripe.secret');

        if (blank($stripeSecret)) {
            return back()
                ->withInput()
                ->with('error', 'Wallet top-ups are temporarily unavailable. Please try again later.');
        }

        $amountCents = (int) round(((float) $validated['amount_dollars']) * 100);

        Stripe::setApiKey($stripeSecret);

        try {
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
                                'description' => 'Adds spendable site credit to your CS2 PickLab wallet.',
                            ],
                        ],
                    ],
                ],
                'success_url' => route('wallet.topup.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('wallet.topup.cancel'),
                'metadata' => [
                    'type' => 'wallet_topup',
                    'user_id' => (string) $user->id,
                    'amount_cents' => (string) $amountCents,
                    'wallet_terms_version' => WalletTermsAcceptance::currentTermsVersion(),
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'type' => 'wallet_topup',
                        'user_id' => (string) $user->id,
                        'amount_cents' => (string) $amountCents,
                        'wallet_terms_version' => WalletTermsAcceptance::currentTermsVersion(),
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Wallet top-up checkout could not be started. Please try again.');
        }

        if (blank($session->url)) {
            return back()
                ->withInput()
                ->with('error', 'Wallet top-up checkout could not be started. Please try again.');
        }

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