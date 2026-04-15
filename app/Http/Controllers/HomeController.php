<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_visible', true)->take(6)->get();
        $featuredProducts = Product::where('featured', true)->take(8)->get();
        $offers = Product::where('on_sale', true)->take(4)->get();
        //$testimonials = Testimonial::latest()->take(3)->get(); // opcional
        return view('store.home', compact('categories', 'featuredProducts', 'offers'));
    }

    public function about()
    {
        return view('store.about');
    }
}
