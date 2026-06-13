@extends('layouts.admin', [
    'title' => 'Order ' . $order->order_number . ' | CS2 PickLab',
    'pageTitle' => 'Order Details',
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin-orders.css') }}">
@endpush

@section('content')
    <div class="order-admin-header">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="link-accent">
                ← Back to Orders
            </a>

            <h2 class="order-admin-title">Order {{ $order->order_number }}</h2>
            <p class="order-admin-subtitle">
                {{ $order->created_at?->format('M j, Y g:i A') }}
            </p>
        </div>

        <div class="order-admin-header-statuses">
            <span class="order-status order-status-{{ $order->status }}">
                {{ str_replace('_', ' ', ucfirst($order->status)) }}
            </span>

            <span class="order-status order-status-payment-{{ $order->payment_status }}">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    <div class="order-admin-layout">
        <main class="order-admin-main">
            <section class="order-admin-panel">
                <div class="order-admin-section-header">
                    <div>
                        <p class="order-admin-eyebrow">Order</p>
                        <h3 class="order-admin-section-title">Items</h3>
                    </div>
                </div>

                <div class="order-admin-item-list">
                    @forelse($order->items as $item)
                        <article class="order-admin-item-card">
                            <header class="order-admin-item-header">
                                <div>
                                    <h4>{{ $item->product_name }}</h4>

                                    <p>
                                        SKU: {{ $item->sku ?? 'No SKU' }}

                                        @if($item->variant)
                                            · Variant: {{ $item->variant->name }}
                                        @endif
                                    </p>
                                </div>

                                <div class="order-admin-item-price">
                                    <strong>${{ $item->line_total_dollars }}</strong>
                                    <span>{{ $item->quantity }} × ${{ $item->unit_price_dollars }}</span>
                                </div>
                            </header>

                            @if($item->customizations->isNotEmpty())
                                <section class="order-admin-subsection">
                                    <h5>Customizations</h5>

                                    <div class="order-admin-customization-grid">
                                        @foreach($item->customizations as $customization)
                                            <div class="order-admin-mini-card">
                                                <p class="order-admin-mini-label">{{ $customization->label }}</p>
                                                <p class="order-admin-mini-value">{{ $customization->value ?: '—' }}</p>

                                                @if($customization->price_delta !== 0)
                                                    <p class="order-admin-price-delta">
                                                        {{ $customization->price_delta > 0 ? '+' : '-' }}${{ number_format(abs($customization->price_delta) / 100, 2) }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if($item->uploads->isNotEmpty())
                                <section class="order-admin-subsection">
                                    <h5>Item Uploads</h5>

                                    <div class="order-admin-upload-list">
                                        @foreach($item->uploads as $upload)
                                            <div class="order-admin-upload-card">
                                                <p class="order-admin-upload-title">{{ $upload->label ?? 'Upload' }}</p>
                                                <p>{{ $upload->original_filename }}</p>
                                                <p>{{ $upload->file_path }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        </article>
                    @empty
                        <p class="order-admin-empty-note">
                            No items attached to this order.
                        </p>
                    @endforelse
                </div>
            </section>
        </main>

        <aside class="order-admin-sidebar">
            <section class="order-admin-panel">
                <h3 class="order-admin-sidebar-title">Production Status</h3>

                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="order-admin-status-form">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-input">
                            @foreach([
                                'draft' => 'Draft',
                                'pending_payment' => 'Pending Payment',
                                'paid' => 'Paid',
                                'design_needed' => 'Design Needed',
                                'design_ready' => 'Design Ready',
                                'printing' => 'Printing',
                                'quality_check' => 'Quality Check',
                                'shipped' => 'Shipped',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-primary">
                        Update Status
                    </button>
                </form>
            </section>

            <section class="order-admin-panel">
                <h3 class="order-admin-sidebar-title">Customer</h3>

                <div class="order-admin-detail-list">
                    <div>
                        <p>Name</p>
                        <strong>{{ $order->customer_name }}</strong>
                    </div>

                    <div>
                        <p>Email</p>
                        <strong>{{ $order->customer_email }}</strong>
                    </div>

                    <div>
                        <p>Phone</p>
                        <strong>{{ $order->customer_phone ?? '—' }}</strong>
                    </div>
                </div>
            </section>

            <section class="order-admin-panel">
                <h3 class="order-admin-sidebar-title">Totals</h3>

                <div class="order-admin-total-list">
                    <div>
                        <span>Subtotal</span>
                        <strong>${{ $order->subtotal_dollars }}</strong>
                    </div>

                    <div>
                        <span>Shipping</span>
                        <strong>${{ number_format($order->shipping_amount / 100, 2) }}</strong>
                    </div>

                    <div>
                        <span>Tax</span>
                        <strong>${{ number_format($order->tax_amount / 100, 2) }}</strong>
                    </div>

                    <div>
                        <span>Discount</span>
                        <strong>-${{ number_format($order->discount_amount / 100, 2) }}</strong>
                    </div>

                    <div class="order-admin-total-final">
                        <span>Total</span>
                        <strong>${{ $order->total_dollars }}</strong>
                    </div>
                </div>
            </section>

            <section class="order-admin-panel">
                <h3 class="order-admin-sidebar-title">Stripe</h3>

                <div class="order-admin-detail-list">
                    <div>
                        <p>Checkout Session</p>
                        <code>{{ $order->stripe_checkout_session_id ?? '—' }}</code>
                    </div>

                    <div>
                        <p>Payment Intent</p>
                        <code>{{ $order->stripe_payment_intent_id ?? '—' }}</code>
                    </div>

                    <div>
                        <p>Paid At</p>
                        <strong>{{ $order->paid_at?->format('M j, Y g:i A') ?? '—' }}</strong>
                    </div>
                </div>
            </section>

            @if($order->uploads->isNotEmpty())
                <section class="order-admin-panel">
                    <h3 class="order-admin-sidebar-title">Order Uploads</h3>

                    <div class="order-admin-upload-list">
                        @foreach($order->uploads as $upload)
                            <div class="order-admin-upload-card">
                                <p class="order-admin-upload-title">{{ $upload->label ?? 'Upload' }}</p>
                                <p>{{ $upload->original_filename }}</p>
                                <p>{{ $upload->file_path }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($order->notes)
                <section class="order-admin-panel">
                    <h3 class="order-admin-sidebar-title">Notes</h3>
                    <p class="order-admin-note">{{ $order->notes }}</p>
                </section>
            @endif
        </aside>
    </div>
@endsection