<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $reviews
        ]);
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
            'photos' => 'nullable|array',
            'photos.*' => 'string' // assuming photo URLs or base64
        ]);

        $product = Product::findOrFail($productId);
        $user = $request->user();

        // Check if user has already reviewed the product
        $existing = Review::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already reviewed this product'
            ], 422);
        }

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'photos' => $request->photos,
            'is_approved' => true // Auto-approved for mock purposes or can be set to false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully',
            'data' => $review
        ], 201);
    }
}
