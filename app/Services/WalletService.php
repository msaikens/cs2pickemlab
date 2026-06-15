<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function getOrCreateWallet(User $user, ?string $currency = null): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'currency' => $currency ?: config('services.stripe.currency', 'usd'),
                'available_balance_cents' => 0,
                'reserved_balance_cents' => 0,
                'pending_balance_cents' => 0,
            ]
        );
    }

    public function creditTopUp(
        User $user,
        int $amountCents,
        string $currency,
        string $stripeCheckoutSessionId,
        ?string $stripePaymentIntentId = null,
        array $metadata = []
    ): WalletTransaction {
        $this->assertPositiveAmount($amountCents);

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

            $wallet = $this->lockWalletForUser($user, $currency);

            $wallet->available_balance_cents += $amountCents;
            $wallet->save();

            return $this->recordTransaction(
                wallet: $wallet,
                type: 'wallet_topup',
                direction: 'credit',
                amountCents: $amountCents,
                balanceBucket: 'available',
                status: 'completed',
                description: 'Wallet top-up via Stripe Checkout.',
                stripeCheckoutSessionId: $stripeCheckoutSessionId,
                stripePaymentIntentId: $stripePaymentIntentId,
                metadata: $metadata,
            );
        });
    }

    public function reserveBuyerFunds(
        User $buyer,
        int $amountCents,
        string $referenceType,
        int $referenceId,
        ?string $description = null,
        array $metadata = []
    ): WalletTransaction {
        $this->assertPositiveAmount($amountCents);

        return DB::transaction(function () use (
            $buyer,
            $amountCents,
            $referenceType,
            $referenceId,
            $description,
            $metadata
        ) {
            $existing = $this->findExistingReferenceTransaction(
                user: $buyer,
                type: 'trade_reserve',
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            if ($existing) {
                return $existing;
            }

            $wallet = $this->lockWalletForUser($buyer);

            if ($wallet->available_balance_cents < $amountCents) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $wallet->available_balance_cents -= $amountCents;
            $wallet->reserved_balance_cents += $amountCents;
            $wallet->save();

            return $this->recordTransaction(
                wallet: $wallet,
                type: 'trade_reserve',
                direction: 'transfer',
                amountCents: $amountCents,
                balanceBucket: 'available_to_reserved',
                status: 'completed',
                referenceType: $referenceType,
                referenceId: $referenceId,
                description: $description ?: 'Funds reserved for marketplace trade.',
                metadata: $metadata,
            );
        });
    }

    public function releaseBuyerReservedFunds(
        User $buyer,
        int $amountCents,
        string $referenceType,
        int $referenceId,
        ?string $description = null,
        array $metadata = []
    ): WalletTransaction {
        $this->assertPositiveAmount($amountCents);

        return DB::transaction(function () use (
            $buyer,
            $amountCents,
            $referenceType,
            $referenceId,
            $description,
            $metadata
        ) {
            $existing = $this->findExistingReferenceTransaction(
                user: $buyer,
                type: 'trade_release',
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            if ($existing) {
                return $existing;
            }

            $wallet = $this->lockWalletForUser($buyer);

            if ($wallet->reserved_balance_cents < $amountCents) {
                throw new RuntimeException('Insufficient reserved wallet balance.');
            }

            $wallet->reserved_balance_cents -= $amountCents;
            $wallet->available_balance_cents += $amountCents;
            $wallet->save();

            return $this->recordTransaction(
                wallet: $wallet,
                type: 'trade_release',
                direction: 'transfer',
                amountCents: $amountCents,
                balanceBucket: 'reserved_to_available',
                status: 'completed',
                referenceType: $referenceType,
                referenceId: $referenceId,
                description: $description ?: 'Reserved trade funds released.',
                metadata: $metadata,
            );
        });
    }

    public function completeTradeSettlement(
        User $buyer,
        User $seller,
        int $buyerAmountCents,
        int $sellerAmountCents,
        int $platformFeeCents,
        string $referenceType,
        int $referenceId,
        array $metadata = []
    ): array {
        $this->assertPositiveAmount($buyerAmountCents);

        if ($sellerAmountCents < 0 || $platformFeeCents < 0) {
            throw new RuntimeException('Settlement amounts cannot be negative.');
        }

        if (($sellerAmountCents + $platformFeeCents) !== $buyerAmountCents) {
            throw new RuntimeException('Settlement amounts do not balance.');
        }

        return DB::transaction(function () use (
            $buyer,
            $seller,
            $buyerAmountCents,
            $sellerAmountCents,
            $platformFeeCents,
            $referenceType,
            $referenceId,
            $metadata
        ) {
            $existing = $this->findExistingReferenceTransaction(
                user: $buyer,
                type: 'trade_capture',
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            if ($existing) {
                return [
                    'buyer_capture' => $existing,
                    'seller_credit' => $this->findExistingReferenceTransaction($seller, 'trade_seller_pending_credit', $referenceType, $referenceId),
                    'platform_fee' => $this->findExistingReferenceTransaction($buyer, 'platform_fee', $referenceType, $referenceId),
                ];
            }

            $buyerWallet = $this->lockWalletForUser($buyer);
            $sellerWallet = $this->lockWalletForUser($seller, $buyerWallet->currency);

            if ($buyerWallet->reserved_balance_cents < $buyerAmountCents) {
                throw new RuntimeException('Insufficient reserved buyer funds.');
            }

            $buyerWallet->reserved_balance_cents -= $buyerAmountCents;
            $buyerWallet->save();

            $buyerCapture = $this->recordTransaction(
                wallet: $buyerWallet,
                type: 'trade_capture',
                direction: 'debit',
                amountCents: $buyerAmountCents,
                balanceBucket: 'reserved',
                status: 'completed',
                referenceType: $referenceType,
                referenceId: $referenceId,
                description: 'Reserved buyer funds captured for completed trade.',
                metadata: $metadata,
            );

            $sellerCredit = null;

            if ($sellerAmountCents > 0) {
                $sellerWallet->pending_balance_cents += $sellerAmountCents;
                $sellerWallet->save();

                $sellerCredit = $this->recordTransaction(
                    wallet: $sellerWallet,
                    type: 'trade_seller_pending_credit',
                    direction: 'credit',
                    amountCents: $sellerAmountCents,
                    balanceBucket: 'pending',
                    status: 'completed',
                    referenceType: $referenceType,
                    referenceId: $referenceId,
                    description: 'Seller proceeds credited to pending balance.',
                    metadata: $metadata,
                );
            }

            $platformFee = null;

            if ($platformFeeCents > 0) {
                $platformFee = $this->recordTransaction(
                    wallet: $buyerWallet,
                    type: 'platform_fee',
                    direction: 'debit',
                    amountCents: $platformFeeCents,
                    balanceBucket: 'reserved',
                    status: 'completed',
                    referenceType: $referenceType,
                    referenceId: $referenceId,
                    description: 'Platform fee retained from completed trade.',
                    metadata: $metadata,
                );
            }

            return [
                'buyer_capture' => $buyerCapture,
                'seller_credit' => $sellerCredit,
                'platform_fee' => $platformFee,
            ];
        });
    }

    public function releaseSellerPendingToAvailable(
        User $seller,
        int $amountCents,
        string $referenceType,
        int $referenceId,
        ?string $description = null,
        array $metadata = []
    ): WalletTransaction {
        $this->assertPositiveAmount($amountCents);

        return DB::transaction(function () use (
            $seller,
            $amountCents,
            $referenceType,
            $referenceId,
            $description,
            $metadata
        ) {
            $existing = $this->findExistingReferenceTransaction(
                user: $seller,
                type: 'seller_pending_release',
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            if ($existing) {
                return $existing;
            }

            $wallet = $this->lockWalletForUser($seller);

            if ($wallet->pending_balance_cents < $amountCents) {
                throw new RuntimeException('Insufficient pending wallet balance.');
            }

            $wallet->pending_balance_cents -= $amountCents;
            $wallet->available_balance_cents += $amountCents;
            $wallet->save();

            return $this->recordTransaction(
                wallet: $wallet,
                type: 'seller_pending_release',
                direction: 'transfer',
                amountCents: $amountCents,
                balanceBucket: 'pending_to_available',
                status: 'completed',
                referenceType: $referenceType,
                referenceId: $referenceId,
                description: $description ?: 'Seller pending balance released to available balance.',
                metadata: $metadata,
            );
        });
    }

    private function lockWalletForUser(User $user, ?string $currency = null): Wallet
    {
        $wallet = Wallet::where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (! $wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'currency' => $currency ?: config('services.stripe.currency', 'usd'),
                'available_balance_cents' => 0,
                'reserved_balance_cents' => 0,
                'pending_balance_cents' => 0,
            ]);

            $wallet = Wallet::whereKey($wallet->id)
                ->lockForUpdate()
                ->first();
        }

        return $wallet;
    }

    private function recordTransaction(
        Wallet $wallet,
        string $type,
        string $direction,
        int $amountCents,
        string $balanceBucket,
        string $status = 'completed',
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $description = null,
        ?string $stripeCheckoutSessionId = null,
        ?string $stripePaymentIntentId = null,
        array $metadata = []
    ): WalletTransaction {
        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'type' => $type,
            'direction' => $direction,
            'amount_cents' => $amountCents,
            'currency' => $wallet->currency,
            'balance_bucket' => $balanceBucket,
            'available_balance_after_cents' => $wallet->available_balance_cents,
            'reserved_balance_after_cents' => $wallet->reserved_balance_cents,
            'pending_balance_after_cents' => $wallet->pending_balance_cents,
            'stripe_checkout_session_id' => $stripeCheckoutSessionId,
            'stripe_payment_intent_id' => $stripePaymentIntentId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => $status,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    private function findExistingReferenceTransaction(
        User $user,
        string $type,
        string $referenceType,
        int $referenceId
    ): ?WalletTransaction {
        return WalletTransaction::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->first();
    }

    private function assertPositiveAmount(int $amountCents): void
    {
        if ($amountCents <= 0) {
            throw new RuntimeException('Wallet amount must be greater than zero.');
        }
    }
}