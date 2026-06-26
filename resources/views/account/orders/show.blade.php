@extends('layouts.public', [
    'title' => $order->order_number . ' | CS2 PickLab',
    'pageTitle' => 'Order Status',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-orders.css') }}">
@endpush

@section('content')
<section class="account-orders-page">
    <header class="account-orders-hero">
        <div>
            <p class="account-orders-kicker">Order Status</p>
            <h1>{{ $order->order_number }}</h1>
            <p>Placed {{ $order->created_at->format('M j, Y g:i A') }}</p>
        </div>

        <a href="{{ route('account.orders.index') }}" class="account-orders-button secondary">
            Back to Orders
        </a>
    </header>

    <section class="account-order-detail-grid">
        <article class="account-orders-card">
            <h2>Status</h2>

            <div class="account-orders-row">
                <span>Production Status</span>
                <strong>{{ $order->statusLabel() }}</strong>
            </div>

            <div class="account-orders-row">
                <span>Payment Status</span>
                <strong>{{ $order->paymentStatusLabel() }}</strong>
            </div>

            @if($order->paid_at)
                <div class="account-orders-row">
                    <span>Paid</span>
                    <strong>{{ $order->paid_at->format('M j, Y g:i A') }}</strong>
                </div>
            @endif

            @if($order->shipped_at)
                <div class="account-orders-row">
                    <span>Shipped</span>
                    <strong>{{ $order->shipped_at->format('M j, Y g:i A') }}</strong>
                </div>
            @endif

            @if($order->tracking_number)
                <div class="account-orders-row">
                    <span>Tracking</span>
                    <strong>
                        {{ $order->shipping_carrier ? $order->shipping_carrier . ' ' : '' }}
                        {{ $order->tracking_number }}
                    </strong>
                </div>
            @endif
        </article>

        <article class="account-orders-card">
            <h2>Shipping</h2>

            @foreach($order->shippingAddressLines() as $line)
                <p>{{ $line }}</p>
            @endforeach

            @if($order->shipping_instructions)
                <div class="account-orders-note">
                    <strong>Instructions</strong>
                    <p>{!! nl2br(e($order->shipping_instructions)) !!}</p>
                </div>
            @endif
        </article>
    </section>

    <article class="account-orders-card">
        <h2>Items</h2>

        <div class="account-order-items">
            @foreach($order->items as $item)
                <div class="account-order-item">
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
                            <div class="account-order-customizations">
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

        <div class="account-orders-row">
            <span>Subtotal</span>
            <strong>${{ $order->subtotal_dollars }}</strong>
        </div>

        <div class="account-orders-row">
            <span>Shipping</span>
            <strong>${{ $order->shipping_dollars }}</strong>
        </div>

        <div class="account-orders-row">
            <span>Tax</span>
            <strong>${{ $order->tax_dollars }}</strong>
        </div>

        @if($order->discount_amount > 0)
            <div class="account-orders-row">
                <span>Discount</span>
                <strong>-${{ $order->discount_dollars }}</strong>
            </div>
        @endif

        <div class="account-orders-row total">
            <span>Total</span>
            <strong>${{ $order->total_dollars }}</strong>
        </div>
    </article>
</section>
@endsection