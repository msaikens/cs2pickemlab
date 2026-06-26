@extends('layouts.public', [
    'title' => 'Checkout | CS2 PickLab',
    'pageTitle' => 'Checkout',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-cart.css') }}">
@endpush

@section('content')
<section class="shop-cart-page">
    <header class="shop-cart-hero">
        <div>
            <p class="shop-cart-kicker">Checkout</p>
            <h1>Checkout</h1>
            <p>Confirm your order and shipping details before payment.</p>
        </div>

        <a href="{{ route('cart.index') }}" class="shop-cart-button secondary">
            Back to Cart
        </a>
    </header>

    @if(session('warning'))
        <div class="shop-cart-alert warning">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="shop-cart-alert danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="shop-cart-alert danger">
            <strong>Checkout needs attention.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="shop-cart-layout">
        <form method="POST" action="{{ route('checkout.store') }}" class="shop-checkout-form">
            @csrf

            <section class="shop-cart-card">
                <h2>Customer Information</h2>

                <label for="customer_name">Name</label>
                <input
                    id="customer_name"
                    name="customer_name"
                    value="{{ old('customer_name', auth()->user()?->displayName()) }}"
                    required
                >

                <label for="customer_email">Email</label>
                <input
                    id="customer_email"
                    name="customer_email"
                    type="email"
                    value="{{ old('customer_email', auth()->user()?->email) }}"
                    required
                >

                <label for="customer_phone">Phone</label>
                <input
                    id="customer_phone"
                    name="customer_phone"
                    value="{{ old('customer_phone') }}"
                >
            </section>

            <section class="shop-cart-card">
                <h2>Shipping Information</h2>

                <label for="shipping_name">Ship To</label>
                <input
                    id="shipping_name"
                    name="shipping_name"
                    value="{{ old('shipping_name', old('customer_name', auth()->user()?->displayName())) }}"
                    placeholder="Leave blank to use customer name"
                >

                <label for="shipping_address_line_1">Address Line 1</label>
                <input
                    id="shipping_address_line_1"
                    name="shipping_address_line_1"
                    value="{{ old('shipping_address_line_1') }}"
                    required
                >

                <label for="shipping_address_line_2">Address Line 2</label>
                <input
                    id="shipping_address_line_2"
                    name="shipping_address_line_2"
                    value="{{ old('shipping_address_line_2') }}"
                    placeholder="Apartment, suite, unit, etc."
                >

                <div class="shop-checkout-field-grid">
                    <div>
                        <label for="shipping_city">City</label>
                        <input
                            id="shipping_city"
                            name="shipping_city"
                            value="{{ old('shipping_city') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="shipping_state">State</label>
                        <input
                            id="shipping_state"
                            name="shipping_state"
                            value="{{ old('shipping_state') }}"
                            required
                        >
                    </div>
                </div>

                <div class="shop-checkout-field-grid">
                    <div>
                        <label for="shipping_postal_code">ZIP / Postal Code</label>
                        <input
                            id="shipping_postal_code"
                            name="shipping_postal_code"
                            value="{{ old('shipping_postal_code') }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="shipping_country">Country</label>
                        <select id="shipping_country" name="shipping_country" required>
                            <option value="US" @selected(old('shipping_country', 'US') === 'US')>United States</option>
                            <option value="CA" @selected(old('shipping_country') === 'CA')>Canada</option>
                        </select>
                    </div>
                </div>

                <label for="shipping_instructions">Shipping Instructions</label>
                <textarea
                    id="shipping_instructions"
                    name="shipping_instructions"
                    maxlength="2000"
                    placeholder="Delivery notes, gate code, preferred handling, etc."
                >{{ old('shipping_instructions') }}</textarea>
            </section>

            <section class="shop-cart-card">
                <h2>Order Notes</h2>

                <label for="notes">Notes</label>
                <textarea
                    id="notes"
                    name="notes"
                    maxlength="2000"
                    placeholder="Anything we should know about this order?"
                >{{ old('notes') }}</textarea>
            </section>

            <button type="submit" class="shop-cart-button primary">
                Continue to Secure Payment
            </button>
        </form>

        <aside class="shop-cart-summary">
            <h2>Order Summary</h2>

            @foreach($cartItems as $item)
                <div class="shop-checkout-summary-item">
                    <div>
                        <span>
                            {{ $item['name'] }}
                            <small>x{{ $item['quantity'] }}</small>
                        </span>

                        @if($item['variant_name'])
                            <small>
                                Option: {{ $item['variant_name'] }}
                            </small>
                        @endif

                        @if(! empty($item['selected_options']))
                            <div class="shop-checkout-options">
                                @foreach($item['selected_options'] as $selectedOption)
                                    <small>
                                        {{ $selectedOption['option_name'] }}:
                                        {{ $selectedOption['value_label'] ?? $selectedOption['value_text'] }}

                                        @if(($selectedOption['price_delta'] ?? 0) > 0)
                                            (+${{ number_format($selectedOption['price_delta'] / 100, 2) }})
                                        @endif
                                    </small>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <strong>${{ number_format($item['line_total'] / 100, 2) }}</strong>
                </div>
            @endforeach

            <div class="shop-cart-summary-row total">
                <span>Subtotal</span>
                <strong>${{ $cartSubtotalDollars }}</strong>
            </div>

            <p>
                Shipping and tax rules can be added next. You will be redirected to Stripe to complete secure payment.
            </p>
        </aside>
    </section>
</section>
@endsection