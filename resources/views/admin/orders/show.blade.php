@extends('layouts.admin', [
    'title' => 'Order ' . $order->order_number . ' | CS2 PickLab',
    'pageTitle' => 'Order Details',
])

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="link-accent">
        ← Back to Orders
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <section class="panel lg:col-span-2">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="page-eyebrow">Order</p>
                <h2 class="mt-1 text-3xl font-black text-white">{{ $order->order_number }}</h2>
                <p class="text-muted-sm">{{ $order->created_at?->format('M j, Y g:i A') }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="status-pill">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                <span class="status-pill">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>

        <h3 class="mb-4 text-xl font-black text-white">Items</h3>

        <div class="space-y-5">
            @forelse($order->items as $item)
                <div class="card-dark">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h4 class="text-lg font-black text-white">{{ $item->product_name }}</h4>
                            <p class="text-muted-xs">
                                SKU: {{ $item->sku ?? 'No SKU' }}
                                @if($item->variant)
                                    · Variant: {{ $item->variant->name }}
                                @endif
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="price-text">${{ $item->line_total_dollars }}</p>
                            <p class="text-muted-xs">
                                {{ $item->quantity }} × ${{ $item->unit_price_dollars }}
                            </p>
                        </div>
                    </div>

                    @if($item->customizations->isNotEmpty())
                        <div class="mt-5">
                            <h5 class="mb-2 font-bold text-white">Customizations</h5>
                            <div class="grid gap-2 md:grid-cols-2">
                                @foreach($item->customizations as $customization)
                                    <div class="rounded-lg border border-slate-800 bg-slate-900 p-3">
                                        <p class="text-muted-xs">{{ $customization->label }}</p>
                                        <p class="font-bold text-white">{{ $customization->value ?: '—' }}</p>

                                        @if($customization->price_delta !== 0)
                                            <p class="text-xs text-cyan-300">
                                                {{ $customization->price_delta > 0 ? '+' : '-' }}${{ number_format(abs($customization->price_delta) / 100, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($item->uploads->isNotEmpty())
                        <div class="mt-5">
                            <h5 class="mb-2 font-bold text-white">Item Uploads</h5>
                            <div class="space-y-2">
                                @foreach($item->uploads as $upload)
                                    <div class="rounded-lg border border-slate-800 bg-slate-900 p-3">
                                        <p class="font-bold text-white">{{ $upload->label ?? 'Upload' }}</p>
                                        <p class="text-muted-xs">{{ $upload->original_filename }}</p>
                                        <p class="text-muted-xs">{{ $upload->file_path }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted">No items attached to this order.</p>
            @endforelse
        </div>
    </section>

    <aside class="space-y-6">
        <section class="panel">
        <h3 class="mb-4 text-xl font-black text-white">Production Status</h3>

        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-4">
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
        <section class="panel">
            <h3 class="mb-4 text-xl font-black text-white">Customer</h3>

            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-muted-xs">Name</p>
                    <p class="font-bold text-white">{{ $order->customer_name }}</p>
                </div>

                <div>
                    <p class="text-muted-xs">Email</p>
                    <p class="font-bold text-white">{{ $order->customer_email }}</p>
                </div>

                <div>
                    <p class="text-muted-xs">Phone</p>
                    <p class="font-bold text-white">{{ $order->customer_phone ?? '—' }}</p>
                </div>
            </div>
        </section>

        <section class="panel">
            <h3 class="mb-4 text-xl font-black text-white">Totals</h3>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-muted">Subtotal</span>
                    <span class="font-bold text-white">${{ $order->subtotal_dollars }}</span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-muted">Shipping</span>
                    <span class="font-bold text-white">${{ number_format($order->shipping_amount / 100, 2) }}</span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-muted">Tax</span>
                    <span class="font-bold text-white">${{ number_format($order->tax_amount / 100, 2) }}</span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-muted">Discount</span>
                    <span class="font-bold text-white">-${{ number_format($order->discount_amount / 100, 2) }}</span>
                </div>

                <div class="mt-4 border-t border-slate-800 pt-4">
                    <div class="flex justify-between gap-4">
                        <span class="text-lg font-black text-white">Total</span>
                        <span class="text-lg font-black text-cyan-300">${{ $order->total_dollars }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel">
            <h3 class="mb-4 text-xl font-black text-white">Stripe</h3>

            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-muted-xs">Checkout Session</p>
                    <p class="break-all font-mono text-xs text-slate-300">{{ $order->stripe_checkout_session_id ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-muted-xs">Payment Intent</p>
                    <p class="break-all font-mono text-xs text-slate-300">{{ $order->stripe_payment_intent_id ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-muted-xs">Paid At</p>
                    <p class="font-bold text-white">{{ $order->paid_at?->format('M j, Y g:i A') ?? '—' }}</p>
                </div>
            </div>
        </section>

        @if($order->uploads->isNotEmpty())
            <section class="panel">
                <h3 class="mb-4 text-xl font-black text-white">Order Uploads</h3>

                <div class="space-y-2">
                    @foreach($order->uploads as $upload)
                        <div class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                            <p class="font-bold text-white">{{ $upload->label ?? 'Upload' }}</p>
                            <p class="text-muted-xs">{{ $upload->original_filename }}</p>
                            <p class="text-muted-xs">{{ $upload->file_path }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($order->notes)
            <section class="panel">
                <h3 class="mb-4 text-xl font-black text-white">Notes</h3>
                <p class="text-muted-sm">{{ $order->notes }}</p>
            </section>
        @endif
    </aside>
</div>
@endsection
