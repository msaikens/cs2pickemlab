<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function creditTopUp(
        User $user,
        int $amountCents,
        string $currency,
        string $stripeCheckoutSessionId,
        ?string $stripePaymentIntentId = null,
        array $metadata = []
    ): WalletTransaction {
        return DB::transaction(function () use (
            $user,
            $amountCents,
            $currency,
            $stripeCheckoutSessionId,
            $stripePaymentIntentId,
            $metadata
        ) {
            $existing = WalletTransaction::where('stripe_checkout_session_id', $stripeCheckoutSessionId)->first();

            if ($existing) {
                return $existing;
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'currency' => $currency,
                    'available_balance_cents' => 0,
                    'pending_balance_cents' => 0,
                ]
            );

            $wallet->increment('available_balance_cents', $amountCents);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'wallet_topup',
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'stripe_checkout_session_id' => $stripeCheckoutSessionId,
                'stripe_payment_intent_id' => $stripePaymentIntentId,
                'status' => 'completed',
                'metadata' => $metadata,
            ]);
        });
    }
}