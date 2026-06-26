<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->with('product.images')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $wishlist
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 'success',
                'message' => 'Product is already in your wishlist',
                'data' => $exists
            ]);
        }

        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to wishlist successfully',
            'data' => $wishlist
        ], 201);
    }

    public function destroy(Request $request, $productId)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found in your wishlist'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from wishlist successfully'
        ]);
    }
}
