<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subCategories')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function show($slug)
    {
        $category = Category::with('subCategories')->where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }
}
