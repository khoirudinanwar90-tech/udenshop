<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class PageController extends Controller
{
    public function blog()
    {
        $posts = Blog::where('is_published', true)->orderBy('created_at', 'desc')->paginate(9);
        return view('store.blog', compact('posts'));
    }

    public function blogShow($slug)
    {
        $post = Blog::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('store.blog_show', compact('post'));
    }

    public function about()
    {
        return view('store.about');
    }

    public function contact()
    {
        return view('store.contact');
    }

    public function promo()
    {
        return view('store.promo');
    }
}
