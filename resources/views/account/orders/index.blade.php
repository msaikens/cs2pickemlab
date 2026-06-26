@extends('layouts.public', [
    'title' => 'My Orders | CS2 PickLab',
    'pageTitle' => 'My Orders',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account-orders.css') }}">
@endpush

@section('content')
<section class="account-orders-page">
    <header class="account-orders-hero">
        <div>
            <p class="account-orders-kicker">Shop Orders</p>
            <h1>My Orders</h1>
            <p>View your CS2 PickLab shop order history, payment status, and production status.</p>
        </div>

        <a href="{{ route('shop.index') }}" class="account-orders-button secondary">
            Back to Shop
        </a>
    </header>

    <section class="account-orders-list">
        @forelse($orders as $order)
            <article class="account-order-card">
                <div>
                    <h2>
                        <a href="{{ route('account.orders.show', $order) }}">
                            {{ $order->order_number }}
                        </a>
                    </h2>

                    <p>
                        {{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}
                        &middot;
                        {{ $order->created_at->format('M j, Y') }}
                    </p>
                </div>

                <div class="account-order-meta">
                    <span class="status">{{ $order->statusLabel() }}</span>
                    <span class="payment">{{ $order->paymentStatusLabel() }}</span>
                    <strong>${{ $order->total_dollars }}</strong>
                </div>
            </article>
        @empty
            <section class="account-orders-empty">
                <h2>No orders yet.</h2>
                <p>When you place shop orders, they will appear here.</p>

                <a href="{{ route('shop.index') }}" class="account-orders-button primary">
                    Browse Shop
                </a>
            </section>
        @endforelse
    </section>

    @if($orders->hasPages())
        <div class="account-orders-pagination">
            {{ $orders->links() }}
        </div>
    @endif
</section>
@endsection