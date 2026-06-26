<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class ShopStripeCheckoutService
{
    public function createCheckoutSession(Order $order): Session
    {
        $secret = config('services.stripe.secret');

        if (blank($secret)) {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        $order->loadMissing(['items.customizations', 'items.variant']);

        $stripe = new StripeClient($secret);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $order->customer_email,
            'client_reference_id' => $order->order_number,

            'line_items' => $order->items
                ->map(fn ($item) => [
                    'quantity' => $item->quantity,
                    'price_data' => [
                        'currency' => strtolower($order->currency ?: 'USD'),
                        'unit_amount' => $item->unit_price,
                        'product_data' => [
                            'name' => $this->lineItemName($item),
                            'description' => $this->lineItemDescription($item),
                            'metadata' => [
                                'order_item_id' => (string) $item->id,
                                'product_id' => (string) ($item->product_id ?? ''),
                                'product_variant_id' => (string) ($item->product_variant_id ?? ''),
                                'sku' => (string) ($item->sku ?? ''),
                            ],
                        ],
                    ],
                ])
                ->values()
                ->all(),

            'metadata' => [
                'source' => 'cs2_picklab_shop',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'user_id' => (string) ($order->user_id ?? ''),
            ],

            'payment_intent_data' => [
                'metadata' => [
                    'source' => 'cs2_picklab_shop',
                    'order_id' => (string) $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => (string) ($order->user_id ?? ''),
                ],
            ],

            'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', [], true) . '?order=' . urlencode($order->order_number),
        ]);

        $order->forceFill([
            'stripe_checkout_session_id' => $session->id,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
        ])->save();

        return $session;
    }

    private function lineItemName($item): string
    {
        $name = $item->product_name;

        if ($item->variant) {
            $name .= ' - ' . $item->variant->name;
        }

        return Str::limit($name, 250, '');
    }

    private function lineItemDescription($item): ?string
    {
        if ($item->customizations->isEmpty()) {
            return null;
        }

        $description = $item->customizations
            ->map(function ($customization) {
                $value = $customization->value;

                if ($customization->price_delta > 0) {
                    $value .= ' +' . $customization->price_delta_dollars;
                }

                return "{$customization->label}: {$value}";
            })
            ->implode(' | ');

        return Str::limit($description, 450, '');
    }
}