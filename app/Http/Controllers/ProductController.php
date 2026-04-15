<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos con filtros y ordenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::query();

        
        // Filtro por categorías
        if ($request->has('categorias')) {
            $query->whereIn('category_id', $request->categorias);
        }

        // Filtro por rango de precio
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Filtro por etiquetas (si tienes una relación o campo)
        if ($request->has('tags')) {
            $query->whereIn('tag', $request->tags); // Ajusta según tu modelo
        }

        // Filtro por valoración
        if ($request->filled('rating')) {
            $rating = $request->rating;
            if ($rating == 5) {
                $query->where('rating', '>=', 5);
            } elseif ($rating == 4) {
                $query->where('rating', '>=', 4);
            } elseif ($rating == 3) {
                $query->where('rating', '>=', 3);
            }
        }

        // Ordenamiento
        switch ($request->get('orden', 'relevancia')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc'); // o como definas relevancia
        }

        $products = $query->paginate(12);
        
        // Categorías para el filtro (conteo de productos)
        $categories = Category::withCount('products')->get();

        return view('store.index', compact('products', 'categories'));
    }
}