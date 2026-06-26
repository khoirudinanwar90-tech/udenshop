<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'payment'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'shipment']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processed,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // If tracking number is provided, update or create shipment
        if ($request->filled('tracking_number')) {
            $order->shipment()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'tracking_number' => $request->tracking_number,
                    'courier' => $order->shipping_courier,
                    'service' => $order->shipping_service,
                    'status' => 'shipped'
                ]
            );
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
