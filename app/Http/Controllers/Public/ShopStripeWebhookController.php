<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class ShopStripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');

        if (blank($secret)) {
            report('Stripe shop webhook secret is not configured.');

            return response('Webhook secret missing.', 500);
        }

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                payload: $payload,
                sigHeader: $signature,
                secret: $secret,
            );
        } catch (UnexpectedValueException) {
            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'checkout.session.expired' => $this->handleCheckoutExpired($event->data->object),
            default => null,
        };

        return response('OK');
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $order = Order::query()
            ->where('stripe_checkout_session_id', $session->id)
            ->first();

        if (! $order) {
            report("Stripe checkout completed for unknown shop session {$session->id}");
            return;
        }

        $paymentIntentId = is_string($session->payment_intent ?? null)
            ? $session->payment_intent
            : null;

        $order->forceFill([
            'status' => Order::STATUS_PAID,
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'stripe_payment_intent_id' => $paymentIntentId,
            'paid_at' => now(),
        ])->save();
    }

    private function handleCheckoutExpired(object $session): void
    {
        $order = Order::query()
            ->where('stripe_checkout_session_id', $session->id)
            ->first();

        if (! $order) {
            return;
        }

        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return;
        }

        $order->forceFill([
            'status' => Order::STATUS_CANCELLED,
            'payment_status' => Order::PAYMENT_STATUS_FAILED,
        ])->save();
    }
}