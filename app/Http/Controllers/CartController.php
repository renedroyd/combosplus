<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = session()->get('cart_session_id');
        if (!$sessionId) {
            $sessionId = Str::random(40);
            session()->put('cart_session_id', $sessionId);
        }
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function add(Product $product)
    {   
        // Obtener o crear carrito del usuario
        $cart = $this->getCart();
 
        // Buscar si el producto ya está en el carrito
        $item = $cart->items()->where('product_id', $product->id)->first();
        
        if ($item) {
            $item->quantity += 1;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => 1,
                'price'      => $product->price, // Guardamos el precio actual
            ]);
 
        }

        // Obtener el total de items (suma de cantidades)
        $totalItems = $cart->items()->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => 'Producto añadido al carrito',
            'totalItems' => $totalItems,
        ]);
    }

    // Mostrar carrito
    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product');
        return view('store.cart', compact('cart'));
    }

    // Actualizar cantidad
    public function update(Request $request, CartItem $cartItem)
    {
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $envio = 5.00;
        $total = $subtotal + $envio;

        return response()->json([
            'success'    => true,
            'subtotal'   => number_format($subtotal, 2),
            'total'      => number_format($total, 2),
            'itemTotal'  => number_format($cartItem->price * $cartItem->quantity, 2),
            'totalItems' => $cart->items->sum('quantity'),
        ]);
    }

    // Eliminar producto del carrito
    public function destroy(CartItem $cartItem)
    {
        $cart = $this->getCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $cartItem->delete();

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $envio = 5.00;
        $total = $subtotal + $envio;

        return response()->json([
            'success'    => true,
            'subtotal'   => number_format($subtotal, 2),
            'total'      => number_format($total, 2),
            'totalItems' => $cart->items->sum('quantity'),
        ]);
    }

    // Método estático para obtener el contador del carrito (usado en layouts)
    public static function getCartItemsCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->with('items')->first()?->items->sum('quantity') ?? 0;
        }

        $sessionId = session()->get('cart_session_id');
        if ($sessionId) {
            return Cart::where('session_id', $sessionId)->with('items')->first()?->items->sum('quantity') ?? 0;
        }
        return 0;
    }
}