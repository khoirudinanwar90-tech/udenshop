<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CheckoutService;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $cartService;

    public function __construct(CheckoutService $checkoutService, CartService $cartService)
    {
        $this->checkoutService = $checkoutService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cart = $this->cartService->getCart($userId, $sessionId);
        
        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $totals = $this->cartService->getCartTotals($userId, $sessionId);

        // Dummy data for shipping simulation (In real app, fetch from RajaOngkir)
        $provinces = [
            ['id' => '6', 'name' => 'DKI Jakarta'],
            ['id' => '9', 'name' => 'Jawa Barat'],
        ];
        
        $cities = [
            ['id' => '152', 'name' => 'Jakarta Pusat', 'province_id' => '6'],
            ['id' => '153', 'name' => 'Jakarta Selatan', 'province_id' => '6'],
            ['id' => '22', 'name' => 'Bandung', 'province_id' => '9'],
        ];

        return view('store.checkout', compact('cart', 'totals', 'provinces', 'cities'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'shipping_province_id' => 'required',
            'shipping_city_id' => 'required',
            'shipping_courier' => 'required',
            'shipping_service' => 'required',
            'payment_gateway' => 'required|in:midtrans,xendit',
            'notes' => 'nullable|string|max:500'
        ]);

        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $result = $this->checkoutService->processCheckout($request->all(), $userId, $sessionId);
            
            // Assuming payment result returns snap token or redirect URL
            if ($request->payment_gateway === 'midtrans' && isset($result['payment']['snap_token'])) {
                return view('store.payment_midtrans', ['snapToken' => $result['payment']['snap_token'], 'order' => $result['order']]);
            }
            
            return redirect()->route('dashboard.orders')->with('success', 'Pesanan berhasil dibuat. Menunggu pembayaran.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
