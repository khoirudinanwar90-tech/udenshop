<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'loyalty_points' => collect($user->meta)->get('loyalty_points', 0),
        ];

        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('user', 'stats', 'recentOrders'));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('dashboard.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->with(['items.product', 'payment', 'shipment'])
            ->findOrFail($id);
            
        return view('dashboard.order_details', compact('order'));
    }
}
