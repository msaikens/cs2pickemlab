<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShopOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($inner) use ($search) {
                    $inner
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($query) => $query->where('payment_status', $request->string('payment_status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::statuses(),
            'paymentStatuses' => Order::paymentStatuses(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.customizations', 'items.variant', 'uploads']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'paymentStatuses' => Order::paymentStatuses(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::statuses())],
            'payment_status' => ['required', Rule::in(Order::paymentStatuses())],
            'shipping_carrier' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:191'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusChangedToShipped = $order->status !== Order::STATUS_SHIPPED
            && $validated['status'] === Order::STATUS_SHIPPED;

        $statusChangedToCompleted = $order->status !== Order::STATUS_COMPLETED
            && $validated['status'] === Order::STATUS_COMPLETED;

        $statusChangedToCancelled = $order->status !== Order::STATUS_CANCELLED
            && $validated['status'] === Order::STATUS_CANCELLED;

        $order->forceFill([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'shipping_carrier' => $validated['shipping_carrier'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'notes' => $validated['admin_note'] ?? $order->notes,
            'shipped_at' => $statusChangedToShipped ? now() : $order->shipped_at,
            'completed_at' => $statusChangedToCompleted ? now() : $order->completed_at,
            'cancelled_at' => $statusChangedToCancelled ? now() : $order->cancelled_at,
        ])->save();

        return back()->with('status', "Order {$order->order_number} updated.");
    }
}