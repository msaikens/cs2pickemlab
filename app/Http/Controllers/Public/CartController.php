<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ShopCartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(ShopCartService $cart): View
    {
        return view('public.shop.cart', [
            'cartItems' => $cart->items(),
            'cartSubtotal' => $cart->subtotal(),
            'cartSubtotalDollars' => $cart->subtotalDollars(),
            'cartCount' => $cart->count(),
        ]);
    }

    public function store(Request $request, ShopCartService $cart): RedirectResponse
{
    $validated = $request->validate([
        'product_id' => ['required', 'integer', 'exists:products,id'],
        'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        'customization_notes' => ['nullable', 'string', 'max:2000'],
        'options' => ['nullable', 'array'],
        'options.*' => ['nullable', 'string', 'max:2000'],
    ]);

    $product = Product::query()
        ->with(['options.values', 'activeVariants'])
        ->where('id', $validated['product_id'])
        ->where('status', 'active')
        ->firstOrFail();

    $cart->add(
        product: $product,
        quantity: (int) $validated['quantity'],
        customizationNotes: $validated['customization_notes'] ?? null,
        selectedOptionsInput: $validated['options'] ?? [],
        variantId: isset($validated['variant_id']) ? (int) $validated['variant_id'] : null,
    );

    return redirect()
        ->route('cart.index')
        ->with('status', "{$product->name} added to your cart.");
    }

    public function update(Request $request, string $cartItemKey, ShopCartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
            'customization_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $cart->update(
            key: $cartItemKey,
            quantity: (int) $validated['quantity'],
            customizationNotes: $validated['customization_notes'] ?? null,
        );

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(string $cartItemKey, ShopCartService $cart): RedirectResponse
    {
        $cart->remove($cartItemKey);

        return back()->with('status', 'Item removed from cart.');
    }

    public function clear(ShopCartService $cart): RedirectResponse
    {
        $cart->clear();

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cart cleared.');
    }
}