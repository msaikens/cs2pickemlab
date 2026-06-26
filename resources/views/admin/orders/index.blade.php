@extends('layouts.admin', [
    'title' => 'Shop Orders | CS2 PickLab',
    'pageTitle' => 'Shop Orders',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-orders.css') }}">
@endpush

@section('content')
<section class="admin-orders-page">
    <header class="admin-orders-hero">
        <div>
            <p class="admin-orders-kicker">Commerce</p>
            <h2>Shop Orders</h2>
            <p>View customer orders, payment state, production status, shipping, and tracking.</p>
        </div>
    </header>

    <section class="admin-orders-card">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="admin-orders-filters">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Order number, name, email, tracking"
            >

            <select name="status">
                <option value="">All statuses</option>

                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ str($status)->replace('_', ' ')->title() }}
                    </option>
                @endforeach
            </select>

            <select name="payment_status">
                <option value="">All payments</option>

                @foreach($paymentStatuses as $paymentStatus)
                    <option value="{{ $paymentStatus }}" @selected(request('payment_status') === $paymentStatus)>
                        {{ str($paymentStatus)->replace('_', ' ')->title() }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Filter</button>

            @if(request()->hasAny(['search', 'status', 'payment_status']))
                <a href="{{ route('admin.orders.index') }}">Reset</a>
            @endif
        </form>
    </section>

    <section class="admin-orders-list">
        @forelse($orders as $order)
            <article class="admin-order-row">
                <div>
                    <h3>
                        <a href="{{ route('admin.orders.show', $order) }}">
                            {{ $order->order_number }}
                        </a>
                    </h3>

                    <p>
                        {{ $order->customer_name }}
                        &middot;
                        {{ $order->customer_email }}
                    </p>

                    <p>
                        {{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}
                        &middot;
                        {{ $order->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>

                <div class="admin-order-row-meta">
                    <span class="status-{{ str($order->status)->replace('_', '-')->toString() }}">
                        Production: {{ $order->statusLabel() }}
                    </span>

                    <span class="payment-{{ str($order->payment_status)->replace('_', '-')->toString() }}">
                        Payment: {{ $order->paymentStatusLabel() }}
                    </span>
                    <strong>${{ $order->total_dollars }}</strong>
                </div>
            </article>
        @empty
            <section class="admin-orders-empty">
                No orders found.
            </section>
        @endforelse
    </section>

    @if($orders->hasPages())
        <div class="admin-orders-pagination">
            {{ $orders->links() }}
        </div>
    @endif
</section>
@endsection