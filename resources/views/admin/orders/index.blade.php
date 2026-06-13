@extends('layouts.admin', [
    'title' => 'Orders | CS2 PickLab',
    'pageTitle' => 'Orders',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-orders.css') }}">
@endpush

@section('content')
    <div class="order-admin-header">
        <div>
            <h2 class="order-admin-title">Orders</h2>
            <p class="order-admin-subtitle">
                View customer orders, payment state, and production workflow status.
            </p>
        </div>
    </div>

    <div class="order-admin-table-wrap">
        <table class="order-admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <p class="order-admin-row-title">{{ $order->order_number }}</p>
                            <p class="order-admin-muted">
                                {{ $order->created_at?->format('M j, Y g:i A') }}
                            </p>
                        </td>

                        <td>
                            <p class="order-admin-row-title">{{ $order->customer_name }}</p>
                            <p class="order-admin-muted">{{ $order->customer_email }}</p>
                        </td>

                        <td>
                            <span class="order-status order-status-{{ $order->status }}">
                                {{ str_replace('_', ' ', ucfirst($order->status)) }}
                            </span>
                        </td>

                        <td>
                            <span class="order-status order-status-payment-{{ $order->payment_status }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>

                        <td>
                            {{ $order->items_count }}
                        </td>

                        <td>
                            <span class="order-admin-price">
                                ${{ $order->total_dollars }}
                            </span>
                        </td>

                        <td class="text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-small-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="order-admin-empty">
                            No orders yet. Stripe Checkout will create orders later.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="order-admin-pagination">
            {{ $orders->links() }}
        </div>
    @endif
@endsection