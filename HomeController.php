<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Review;

class HomeController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $heroBanners = Banner::where('type', 'hero')->orderBy('sort_order')->get();
        $promoBanners = Banner::where('type', 'promo')->orderBy('sort_order')->get();
        
        $categories = Category::all();
        $popularBrands = Brand::where('is_popular', true)->get();
        
        $latestProducts = $this->productService->getLatestProducts(8);
        $flashSaleProducts = $this->productService->getFlashSaleProducts(4);
        
        $blogs = Blog::where('is_published', true)->orderBy('created_at', 'desc')->limit(3)->get();
        
        // Mock testimonials (reviews with highest rating)
        $testimonials = Review::where('rating', 5)->with('user', 'product')->limit(3)->get();

        return view('store.index', compact(
            'heroBanners',
            'promoBanners',
            'categories',
            'popularBrands',
            'latestProducts',
            'flashSaleProducts',
            'blogs',
            'testimonials'
        ));
    }
}
