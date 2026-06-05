@extends('layouts.admin', [
    'title' => 'Orders | CS2 PickLab',
    'pageTitle' => 'Orders',
])

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Orders</h2>
        <p class="page-subtitle">View customer orders, payment state, and production workflow status.</p>
    </div>
</div>

<div class="table-wrap">
    <table class="admin-table">
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
                        <p class="font-bold text-white">{{ $order->order_number }}</p>
                        <p class="text-muted-xs">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
                    </td>
                    <td>
                        <p class="font-bold text-white">{{ $order->customer_name }}</p>
                        <p class="text-muted-xs">{{ $order->customer_email }}</p>
                    </td>
                    <td>
                        <span class="status-pill">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                    </td>
                    <td>
                        <span class="status-pill">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td class="text-slate-300">{{ $order->items_count }}</td>
                    <td class="price-text">${{ $order->total_dollars }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-small-primary">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">
                        No orders yet. Stripe Checkout will create orders later.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
