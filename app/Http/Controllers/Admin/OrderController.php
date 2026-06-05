<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->withCount('items')
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'items.product',
            'items.variant',
            'items.customizations.productOption',
            'items.uploads',
            'uploads',
            'user',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => [
                'required',
                'in:draft,pending_payment,paid,design_needed,design_ready,printing,quality_check,shipped,completed,cancelled,refunded',
            ],
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated.');
    }
}
