<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ShopCartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    public function store(Request $request, ShopCartService $cart): RedirectResponse
    {
        if (! $cart->hasItems()) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Your cart is empty.');
        }

        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        return back()
            ->withInput()
            ->with('warning', 'Checkout review is wired. Payment/order creation is the next step after we confirm the OrderItem schema.');
    }

    public function success(): View
    {
        return view('public.shop.order-confirmation', [
            'order' => null,
        ]);
    }

    public function cancel(): RedirectResponse
    {
        return redirect()
            ->route('cart.index')
            ->with('warning', 'Checkout cancelled.');
    }
}