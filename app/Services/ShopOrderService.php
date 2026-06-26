<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopOrderService
{
    public function createPendingOrderFromCart(
        ShopCartService $cart,
        array $customerData,
        ?User $user = null,
    ): Order {
        $cartItems = $cart->items();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        return DB::transaction(function () use ($cartItems, $customerData, $user) {
            $subtotal = (int) $cartItems->sum('line_total');

            // Flat/free shipping placeholder. We can add shipping rules next.
            $shippingAmount = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            $total = max(0, $subtotal + $shippingAmount + $taxAmount - $discountAmount);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user?->id,

                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'] ?? null,

                'shipping_name' => $customerData['shipping_name'] ?? $customerData['customer_name'],
                'shipping_address_line_1' => $customerData['shipping_address_line_1'],
                'shipping_address_line_2' => $customerData['shipping_address_line_2'] ?? null,
                'shipping_city' => $customerData['shipping_city'],
                'shipping_state' => $customerData['shipping_state'],
                'shipping_postal_code' => $customerData['shipping_postal_code'],
                'shipping_country' => strtoupper($customerData['shipping_country'] ?? 'US'),
                'shipping_instructions' => $customerData['shipping_instructions'] ?? null,

                'status' => Order::STATUS_RECEIVED,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,

                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'currency' => 'USD',

                'notes' => $customerData['notes'] ?? null,
            ]);

            foreach ($cartItems as $cartItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['product_id'],
                    'product_variant_id' => $cartItem['variant_id'] ?? null,
                    'product_name' => $cartItem['name'],
                    'sku' => $cartItem['sku'] ?? null,
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['unit_price'],
                    'line_total' => $cartItem['line_total'],
                ]);

                foreach ($cartItem['selected_options'] ?? [] as $selectedOption) {
                    OrderItemCustomization::create([
                        'order_item_id' => $orderItem->id,
                        'product_option_id' => $selectedOption['option_id'] ?? null,
                        'label' => $selectedOption['option_name'],
                        'value' => $selectedOption['value_label'] ?? $selectedOption['value_text'] ?? null,
                        'price_delta' => $selectedOption['price_delta'] ?? 0,
                    ]);
                }

                if (! blank($cartItem['customization_notes'] ?? null)) {
                    OrderItemCustomization::create([
                        'order_item_id' => $orderItem->id,
                        'product_option_id' => null,
                        'label' => 'Customization notes',
                        'value' => $cartItem['customization_notes'],
                        'price_delta' => 0,
                    ]);
                }
            }

            return $order->fresh([
                'items.customizations',
                'items.product',
                'items.variant',
            ]);
        });
    }
}