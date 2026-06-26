<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cart = $this->cartService->getCart($userId, $sessionId);
        $totals = $this->cartService->getCartTotals($userId, $sessionId);

        return view('store.cart', compact('cart', 'totals'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array'
        ]);

        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $this->cartService->addItem(
                $userId, 
                $sessionId, 
                $request->product_id, 
                $request->quantity,
                $request->options
            );

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $this->cartService->updateItem($userId, $sessionId, $itemId, $request->quantity);
            return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $request, $itemId)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $this->cartService->removeItem($userId, $sessionId, $itemId);
            return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $this->cartService->applyCoupon($userId, $sessionId, $request->coupon_code);
            return redirect()->route('cart.index')->with('success', 'Kupon berhasil digunakan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeCoupon(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        try {
            $this->cartService->removeCoupon($userId, $sessionId);
            return redirect()->route('cart.index')->with('success', 'Kupon dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
