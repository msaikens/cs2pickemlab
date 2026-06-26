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
            <p>Confirm your order details before payment.</p>
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
@if(session('error'))
    <div class="shop-cart-alert danger">
        {{ session('error') }}
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

                <label for="notes">Order notes</label>
                <textarea
                    id="notes"
                    name="notes"
                    maxlength="2000"
                    placeholder="Anything we should know about this order?"
                >{{ old('notes') }}</textarea>
            </section>

            <button type="submit" class="shop-cart-button primary">
                Continue to Payment
            </button>
        </form>

        <aside class="shop-cart-summary">
            <h2>Order Summary</h2>

            @foreach($cartItems as $item)
                <div class="shop-checkout-summary-item">
                    <span>
                        {{ $item['name'] }}
                        @if($item['variant_name'])
                        <small>
                            Option: {{ $item['variant_name'] }}
                        </small>
                        @endif
                        <small>x{{ $item['quantity'] }}</small>
                    </span>

                    <strong>${{ number_format($item['line_total'] / 100, 2) }}</strong>
                </div>
            @endforeach

            <div class="shop-cart-summary-row total">
                <span>Subtotal</span>
                <strong>${{ $cartSubtotalDollars }}</strong>
            </div>

            <p>
    You will be redirected to Stripe to complete secure payment.
</p>
        </aside>
    </section>
</section>
@endsection