@extends('layouts.admin', [
    'title' => $order->order_number . ' | CS2 PickLab',
    'pageTitle' => 'Order Details',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-orders.css') }}">
@endpush

@section('content')
<section class="admin-orders-page">
    <header class="admin-orders-hero">
        <div>
            <p class="admin-orders-kicker">Order Details</p>
            <h2>{{ $order->order_number }}</h2>
            <p>
                {{ $order->customer_name }}
                &middot;
                {{ $order->customer_email }}
                &middot;
                ${{ $order->total_dollars }}
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="admin-orders-button secondary">
            Back to Orders
        </a>
    </header>

    @if(session('status'))
        <div class="admin-orders-alert success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="admin-orders-alert danger">
            <strong>Order could not be updated.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-order-detail-grid">
        <article class="admin-orders-card">
            <h3>Manage Order</h3>

            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="admin-order-form">
                @csrf
                @method('PATCH')

                <label for="status">Production Status</label>
                <select id="status" name="status" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>
                            {{ str($status)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>

                <label for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status" required>
                    @foreach($paymentStatuses as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(old('payment_status', $order->payment_status) === $paymentStatus)>
                            {{ str($paymentStatus)->replace('_', ' ')->title() }}
                        </option>
                    @endforeach
                </select>

                <label for="shipping_carrier">Shipping Carrier</label>
                <input
                    id="shipping_carrier"
                    name="shipping_carrier"
                    value="{{ old('shipping_carrier', $order->shipping_carrier) }}"
                    placeholder="UPS, USPS, FedEx, DHL"
                >

                <label for="tracking_number">Tracking Number</label>
                <input
                    id="tracking_number"
                    name="tracking_number"
                    value="{{ old('tracking_number', $order->tracking_number) }}"
                >

                <label for="admin_note">Admin / Order Note</label>
                <textarea id="admin_note" name="admin_note">{{ old('admin_note', $order->notes) }}</textarea>

                <button type="submit">Update Order</button>
            </form>
        </article>

        <article class="admin-orders-card">
            <h3>Customer</h3>

            <div class="admin-orders-row">
                <span>Name</span>
                <strong>{{ $order->customer_name }}</strong>
            </div>

            <div class="admin-orders-row">
                <span>Email</span>
                <strong>{{ $order->customer_email }}</strong>
            </div>

            @if($order->customer_phone)
                <div class="admin-orders-row">
                    <span>Phone</span>
                    <strong>{{ $order->customer_phone }}</strong>
                </div>
            @endif

            @if($order->user)
                <div class="admin-orders-row">
                    <span>Account</span>
                    <strong>{{ $order->user->displayName() }}</strong>
                </div>
            @endif

            <h3>Shipping</h3>

            @foreach($order->shippingAddressLines() as $line)
                <p>{{ $line }}</p>
            @endforeach

            @if($order->shipping_instructions)
                <div class="admin-orders-note">
                    <strong>Instructions</strong>
                    <p>{!! nl2br(e($order->shipping_instructions)) !!}</p>
                </div>
            @endif
        </article>
    </section>

    <article class="admin-orders-card">
        <h3>Items</h3>

        <div class="admin-order-items">
            @foreach($order->items as $item)
                <div class="admin-order-item">
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
                            <div class="admin-order-customizations">
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

        <div class="admin-orders-row">
            <span>Subtotal</span>
            <strong>${{ $order->subtotal_dollars }}</strong>
        </div>

        <div class="admin-orders-row">
            <span>Shipping</span>
            <strong>${{ $order->shipping_dollars }}</strong>
        </div>

        <div class="admin-orders-row">
            <span>Tax</span>
            <strong>${{ $order->tax_dollars }}</strong>
        </div>

        @if($order->discount_amount > 0)
            <div class="admin-orders-row">
                <span>Discount</span>
                <strong>-${{ $order->discount_dollars }}</strong>
            </div>
        @endif

        <div class="admin-orders-row total">
            <span>Total</span>
            <strong>${{ $order->total_dollars }}</strong>
        </div>
    </article>
</section>
@endsection