<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        // Productos en oferta (suponiendo que tienes campos 'on_sale' y 'offer_price')
        $offers = Product::where('on_sale', true)
                         ->orWhereNotNull('offer_price')
                         ->paginate(12);

        return view('store.offers', compact('offers'));
    }
}