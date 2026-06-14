<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\StripeEvent;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            Log::error('Stripe webhook secret is missing.');

            return response('Webhook secret missing', 500);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $secret
            );
        } catch (UnexpectedValueException $e) {
            Log::warning('Invalid Stripe webhook payload.', [
                'message' => $e->getMessage(),
            ]);

            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Invalid Stripe webhook signature.', [
                'message' => $e->getMessage(),
            ]);

            return response('Invalid signature', 400);
        }

        try {
            $stripeEvent = StripeEvent::firstOrCreate(
                ['stripe_event_id' => $event->id],
                [
                    'type' => $event->type,
                    'payload' => json_decode($payload, true),
                    'processed_at' => null,
                ]
            );

            if ($stripeEvent->processed_at) {
                Log::info('Stripe webhook already processed.', [
                    'event_id' => $event->id,
                    'event_type' => $event->type,
                ]);

                return response('Webhook already processed', 200);
            }

            match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
                'checkout.session.expired' => $this->handleCheckoutExpired($event->data->object),
                'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object),
                'charge.refunded' => $this->handleChargeRefunded($event->data->object),
                'charge.dispute.created' => $this->handleDisputeCreated($event->data->object),
                default => Log::info('Unhandled Stripe webhook event.', [
                    'event_id' => $event->id,
                    'event_type' => $event->type,
                ]),
            };

            $stripeEvent->update([
                'type' => $event->type,
                'payload' => json_decode($payload, true),
                'processed_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Stripe webhook handler crashed.', [
                'event_id' => $event->id ?? null,
                'event_type' => $event->type ?? null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response('Webhook handler crashed', 500);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $metadata = $this->objectToArray($session->metadata ?? []);

        Log::info('Entered checkout.session.completed handler.', [
            'session_id' => $session->id ?? null,
            'payment_status' => $session->payment_status ?? null,
            'currency' => $session->currency ?? null,
            'metadata' => $metadata,
        ]);

        if (($metadata['type'] ?? null) !== 'wallet_topup') {
            Log::info('Checkout completed ignored because type is not wallet_topup.', [
                'session_id' => $session->id ?? null,
                'metadata' => $metadata,
            ]);

            return;
        }

        if (($session->payment_status ?? null) !== 'paid') {
            Log::warning('Wallet top-up checkout completed but payment status is not paid.', [
                'session_id' => $session->id ?? null,
                'payment_status' => $session->payment_status ?? null,
            ]);

            return;
        }

        $userId = (int) ($metadata['user_id'] ?? 0);
        $amountCents = (int) ($metadata['amount_cents'] ?? 0);
        $currency = strtolower($session->currency ?? config('services.stripe.currency', 'usd'));

        if ($userId <= 0 || $amountCents <= 0) {
            Log::error('Wallet top-up metadata invalid.', [
                'session_id' => $session->id ?? null,
                'metadata' => $metadata,
            ]);

            return;
        }

        $user = User::find($userId);

        if (! $user) {
            Log::error('Wallet top-up user not found.', [
                'session_id' => $session->id ?? null,
                'user_id' => $userId,
            ]);

            return;
        }

        app(WalletService::class)->creditTopUp(
            user: $user,
            amountCents: $amountCents,
            currency: $currency,
            stripeCheckoutSessionId: $session->id,
            stripePaymentIntentId: $session->payment_intent ?? null,
            metadata: [
                'stripe_customer' => $session->customer ?? null,
                'payment_status' => $session->payment_status ?? null,
                'mode' => $session->mode ?? null,
            ],
        );

        Log::info('Wallet top-up credited.', [
            'user_id' => $user->id,
            'amount_cents' => $amountCents,
            'currency' => $currency,
            'session_id' => $session->id,
            'payment_intent' => $session->payment_intent ?? null,
        ]);
    }

    private function handleCheckoutExpired(object $session): void
    {
        Log::info('Stripe checkout expired.', [
            'session_id' => $session->id ?? null,
            'metadata' => $this->objectToArray($session->metadata ?? []),
        ]);
    }

    private function handlePaymentIntentSucceeded(object $paymentIntent): void
    {
        Log::info('Stripe payment intent succeeded.', [
            'payment_intent' => $paymentIntent->id ?? null,
            'metadata' => $this->objectToArray($paymentIntent->metadata ?? []),
        ]);
    }

    private function handlePaymentIntentFailed(object $paymentIntent): void
    {
        Log::warning('Stripe payment intent failed.', [
            'payment_intent' => $paymentIntent->id ?? null,
            'metadata' => $this->objectToArray($paymentIntent->metadata ?? []),
        ]);
    }

    private function handleChargeRefunded(object $charge): void
    {
        Log::warning('Stripe charge refunded.', [
            'charge' => $charge->id ?? null,
            'payment_intent' => $charge->payment_intent ?? null,
        ]);
    }

    private function handleDisputeCreated(object $dispute): void
    {
        Log::critical('Stripe dispute created.', [
            'dispute' => $dispute->id ?? null,
            'charge' => $dispute->charge ?? null,
        ]);
    }

    private function objectToArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Stripe\StripeObject) {
            return $value->toArray();
        }

        if ($value instanceof \stdClass) {
            return (array) $value;
        }

        return [];
    }
}