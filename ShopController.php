<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Models\Category;
use App\Models\Brand;

class ShopController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'category',
            'sub_category',
            'brand',
            'min_price',
            'max_price',
            'stock',
            'sort'
        ]);

        $products = $this->productService->getFilteredProducts($filters, 12);
        
        $categories = Category::with('subCategories')->get();
        $brands = Brand::all();

        return view('store.shop', compact('products', 'categories', 'brands', 'filters'));
    }

    public function show($slug)
    {
        $product = $this->productService->getProductDetails($slug);
        
        if (!$product) {
            abort(404);
        }

        // Get related products (same category)
        $relatedProducts = \App\Models\Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('store.show', compact('product', 'relatedProducts'));
    }
}
