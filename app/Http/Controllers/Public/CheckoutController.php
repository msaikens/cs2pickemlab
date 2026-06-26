<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShopCartService;
use App\Services\ShopOrderService;
use App\Services\ShopStripeCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function create(ShopCartService $cart): View|RedirectResponse
    {
        if (! $cart->hasItems()) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Add at least one item before checkout.');
        }

        return view('public.shop.checkout', [
            'cartItems' => $cart->items(),
            'cartSubtotal' => $cart->subtotal(),
            'cartSubtotalDollars' => $cart->subtotalDollars(),
            'cartCount' => $cart->count(),
        ]);
    }

    public function store(
        Request $request,
        ShopCartService $cart,
        ShopOrderService $orders,
        ShopStripeCheckoutService $stripeCheckout,
    ): RedirectResponse {
        if (! $cart->hasItems()) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_email' => ['required', 'email', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:100'],

            'shipping_name' => ['nullable', 'string', 'max:100'],
            'shipping_address_line_1' => ['required', 'string', 'max:191'],
            'shipping_address_line_2' => ['nullable', 'string', 'max:191'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:40'],
            'shipping_country' => ['required', 'string', 'size:2'],
            'shipping_instructions' => ['nullable', 'string', 'max:2000'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $order = $orders->createPendingOrderFromCart(
                cart: $cart,
                customerData: $validated,
                user: $request->user(),
            );

            $checkoutSession = $stripeCheckout->createCheckoutSession($order);

            $cart->clear();

            session([
                'shop.last_order_id' => $order->id,
            ]);

            return redirect()->away($checkoutSession->url);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Checkout could not be started. Please try again or contact support.');
        }
    }

    public function success(Request $request): View
    {
        $order = null;

        if ($request->filled('session_id')) {
            $order = Order::query()
                ->with(['items.customizations', 'items.variant'])
                ->where('stripe_checkout_session_id', $request->query('session_id'))
                ->first();

            if ($order) {
                session([
                    'shop.last_order_id' => $order->id,
                ]);
            }
        }

        if (! $order && session()->has('shop.last_order_id')) {
            $order = Order::query()
                ->with(['items.customizations', 'items.variant'])
                ->find(session('shop.last_order_id'));
        }

        return view('public.shop.order-confirmation', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request): RedirectResponse
    {
        return redirect()
            ->route('cart.index')
            ->with('warning', 'Checkout cancelled. Your order was not paid.');
    }
}