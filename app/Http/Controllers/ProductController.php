<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('categorias')) {
            $query->whereIn('category_id', (array) $request->input('categorias'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->has('tags')) {
            $query->whereIn('tag', (array) $request->input('tags'));
        }

        if ($request->filled('rating')) {
            $rating = (int) $request->input('rating');
            if (in_array($rating, [3, 4, 5], true)) {
                $query->where('rating', '>=', $rating);
            }
        }

        switch ($request->get('orden', 'relevancia')) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderByDesc('id');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('store.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        return view('store.product', compact('product'));
    }
}
