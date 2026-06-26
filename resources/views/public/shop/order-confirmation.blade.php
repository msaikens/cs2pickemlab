@extends('layouts.public', [
    'title' => 'Order Confirmation | CS2 PickLab',
    'pageTitle' => 'Order Confirmation',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop-cart.css') }}">
@endpush

@section('content')
<section class="shop-cart-page">
    <header class="shop-cart-hero">
        <div>
            <p class="shop-cart-kicker">Order Confirmation</p>
            <h1>Thanks for your order.</h1>
            <p>Your CS2 PickLab shop order has been created.</p>
        </div>

        <a href="{{ route('shop.index') }}" class="shop-cart-button secondary">
            Back to Shop
        </a>
    </header>

    @if(session('status'))
        <div class="shop-cart-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if($order)
        <section class="shop-order-confirmation-layout">
            <article class="shop-cart-card">
                <h2>Order Details</h2>

                <div class="shop-cart-summary-row">
                    <span>Order Number</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Status</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Payment</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Customer</span>
                    <strong>{{ $order->customer_name }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Email</span>
                    <strong>{{ $order->customer_email }}</strong>
                </div>

                @if($order->customer_phone)
                    <div class="shop-cart-summary-row">
                        <span>Phone</span>
                        <strong>{{ $order->customer_phone }}</strong>
                    </div>
                @endif

                @if($order->notes)
                    <div class="shop-order-notes">
                        <strong>Order Notes</strong>
                        <p>{!! nl2br(e($order->notes)) !!}</p>
                    </div>
                @endif
            </article>

            <article class="shop-cart-card">
                <h2>Items</h2>

                <div class="shop-order-items">
                    @foreach($order->items as $item)
                        <div class="shop-order-item">
                            <div>
                                <strong>{{ $item->product_name }}</strong>

                                @if($item->variant)
                                    <p>Option: {{ $item->variant->name }}</p>
                                @endif

                                @if($item->sku)
                                    <p>SKU: {{ $item->sku }}</p>
                                @endif

                                <p>
                                    Qty {{ $item->quantity }}
                                    &middot;
                                    ${{ $item->unit_price_dollars }} each
                                </p>

                                @if($item->customizations->isNotEmpty())
                                    <div class="shop-order-customizations">
                                        @foreach($item->customizations as $customization)
                                            <small>
                                                {{ $customization->label }}:
                                                {{ $customization->value }}

                                                @if($customization->price_delta > 0)
                                                    (+${{ $customization->price_delta_dollars }})
                                                @endif
                                            </small>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <strong>${{ $item->line_total_dollars }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="shop-cart-summary-row">
                    <span>Subtotal</span>
                    <strong>${{ $order->subtotal_dollars }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Shipping</span>
                    <strong>${{ number_format($order->shipping_amount / 100, 2) }}</strong>
                </div>

                <div class="shop-cart-summary-row">
                    <span>Tax</span>
                    <strong>${{ number_format($order->tax_amount / 100, 2) }}</strong>
                </div>

                @if($order->discount_amount > 0)
                    <div class="shop-cart-summary-row">
                        <span>Discount</span>
                        <strong>-${{ number_format($order->discount_amount / 100, 2) }}</strong>
                    </div>
                @endif

                <div class="shop-cart-summary-row total">
                    <span>Total</span>
                    <strong>${{ $order->total_dollars }}</strong>
                </div>

                @if($order->payment_status === \App\Models\Order::PAYMENT_STATUS_PAID)
    <p>
        Payment received. Your order is now in the production queue.
    </p>
@elseif($order->payment_status === \App\Models\Order::PAYMENT_STATUS_PENDING)
    <p>
        Payment is being confirmed. If you just paid, this page may update after Stripe sends confirmation.
    </p>
@else
    <p>
        Payment has not been completed for this order.
    </p>
@endif
            </article>
        </section>
    @else
        <section class="shop-cart-empty">
            <h2>No recent order found.</h2>
            <p>
                Your order confirmation session may have expired, or this page was opened directly.
            </p>

            <a href="{{ route('shop.index') }}" class="shop-cart-button primary">
                Back to Shop
            </a>
        </section>
    @endif
</section>
@endsection