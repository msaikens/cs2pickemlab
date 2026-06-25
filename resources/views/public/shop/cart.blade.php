@extends('layouts.public', [
    'title' => 'Cart | CS2 PickLab',
    'pageTitle' => 'Cart',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-cart.css') }}">
@endpush

@section('content')
<section class="shop-cart-page">
    <header class="shop-cart-hero">
        <div>
            <p class="shop-cart-kicker">Shop Cart</p>
            <h1>Your Cart</h1>
            <p>Review your CS2 PickLab shop items before checkout.</p>
        </div>

        <a href="{{ route('shop.index') }}" class="shop-cart-button secondary">
            Continue Shopping
        </a>
    </header>

    @if(session('status'))
        <div class="shop-cart-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="shop-cart-alert warning">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="shop-cart-alert danger">
            <strong>Cart could not be updated.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($cartItems->isEmpty())
        <section class="shop-cart-empty">
            <h2>Your cart is empty.</h2>
            <p>Add items from the shop to begin checkout.</p>

            <a href="{{ route('shop.index') }}" class="shop-cart-button primary">
                Browse Shop
            </a>
        </section>
    @else
        <section class="shop-cart-layout">
            <div class="shop-cart-items">
                @foreach($cartItems as $item)
                    <article class="shop-cart-item">
                        <div class="shop-cart-item-image">
                            @if($item['primary_image_path'])
                                <img
                                    src="{{ asset('storage/' . $item['primary_image_path']) }}"
                                    alt="{{ $item['name'] }}"
                                >
                            @else
                                <span>No Image</span>
                            @endif
                        </div>

                        <div class="shop-cart-item-main">
                            <h2>
                                <a href="{{ route('shop.show', $item['slug']) }}">
                                    {{ $item['name'] }}
                                </a>
                            </h2>

                            @if($item['sku'])
                                <p class="shop-cart-muted">SKU: {{ $item['sku'] }}</p>
                            @endif
@if($item['variant_name'])
    <p class="shop-cart-muted">
        Option: {{ $item['variant_name'] }}
    </p>
@endif
                            <p class="shop-cart-price">
    ${{ number_format($item['unit_price'] / 100, 2) }} each

    @if($item['option_delta_total'] > 0)
        <span>
            includes ${{ number_format($item['option_delta_total'] / 100, 2) }} in option upgrades
        </span>
    @endif
</p>

                            @if($item['requires_customization'] || $item['requires_upload'])
                                <p class="shop-cart-muted">
                                    This item may require customization details or an upload after checkout.
                                </p>
                            @endif

                            <form method="POST" action="{{ route('cart.items.update', $item['key']) }}" class="shop-cart-update-form">
                                @csrf
                                @method('PATCH')

                                <div class="shop-cart-form-row">
                                    <label for="quantity-{{ $item['key'] }}">Quantity</label>

                                    <input
                                        id="quantity-{{ $item['key'] }}"
                                        name="quantity"
                                        type="number"
                                        min="0"
                                        max="99"
                                        value="{{ $item['quantity'] }}"
                                        required
                                    >
                                </div>

                                @if($item['requires_customization'])
                                    <label for="customization-{{ $item['key'] }}">
                                        Customization notes
                                    </label>

                                    <textarea
                                        id="customization-{{ $item['key'] }}"
                                        name="customization_notes"
                                        maxlength="2000"
                                        placeholder="Add names, colors, sizing notes, or other details."
                                    >{{ $item['customization_notes'] }}</textarea>
                                @else
                                    <input type="hidden" name="customization_notes" value="{{ $item['customization_notes'] }}">
                                @endif

                                <button type="submit">
                                    Update
                                </button>
                            </form>
                        </div>

                        <div class="shop-cart-item-side">
                            <strong>${{ number_format($item['line_total'] / 100, 2) }}</strong>

                            <form method="POST" action="{{ route('cart.items.destroy', $item['key']) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="danger">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <aside class="shop-cart-summary">
                <h2>Summary</h2>

                <div class="shop-cart-summary-row">
                    <span>Items</span>
                    <strong>{{ $cartCount }}</strong>
                </div>

                <div class="shop-cart-summary-row total">
                    <span>Subtotal</span>
                    <strong>${{ $cartSubtotalDollars }}</strong>
                </div>

                <p>
                    Shipping, taxes, discounts, and payment are calculated during checkout.
                </p>

                <a href="{{ route('checkout.create') }}" class="shop-cart-button primary">
                    Checkout
                </a>

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="shop-cart-button ghost">
                        Clear Cart
                    </button>
                </form>
            </aside>
        </section>
    @endif
</section>
@endsection