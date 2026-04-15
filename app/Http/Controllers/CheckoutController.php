<?php
// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Notifications\OrderCreatedTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            session()->put('intended_checkout', true);
            return view('checkout.guest');
        }

        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $addresses = auth()->user()->addresses;
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $shippingCost = 5.00;
        $total = $subtotal + $shippingCost;

        return view('checkout.index', compact('cart', 'addresses', 'paymentMethods', 'subtotal', 'shippingCost', 'total'));
    }

    public function process(Request $request)
    {
        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Carrito vacío.');
        }

        $rules = [
            'delivery_type' => 'required|in:pickup,delivery',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:500',
        ];

        if ($request->delivery_type === 'delivery') {
            $rules['address_id'] = 'required|exists:addresses,id';
        }

        $data = $request->validate($rules);

        if ($data['delivery_type'] === 'delivery') {
            $address = Address::where('id', $data['address_id'])
                              ->where('user_id', auth()->id())
                              ->firstOrFail();
        }

        $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $shippingCost = $data['delivery_type'] === 'delivery' ? 5.00 : 0.00;
        $total = $subtotal + $shippingCost;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'shipping_address_id' => $data['delivery_type'] === 'delivery' ? $data['address_id'] : null,
                'payment_method_id' => $paymentMethod->id,
                'delivery_type' => $data['delivery_type'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'tax' => 0,
                'discount' => 0,
            ]);

            

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ]);
            }

            
            $cart->items()->delete();

            
            DB::commit();

            // 📱 ENVIAR NOTIFICACIONES
            try {
                // NOTIFICACIÓN TELEGRAM
                
                // 1. Notificar al administrador (siempre)
                $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
                Log::info('Admin Chat ID: ' . ($adminChatId ?? 'no definido'));

                if ($adminChatId) {
                    \Illuminate\Support\Facades\Notification::route('telegram', $adminChatId)
                        ->notify(new OrderCreatedTelegram($order, 'admin'));
                    Log::info('Notificación admin enviada');
                } else {
                    Log::warning('TELEGRAM_ADMIN_CHAT_ID no está definido en .env');
                }
                
                // 2. Notificar al cliente (si tiene Telegram configurado)
                if ($order->user->telegram_chat_id) {
                    $order->user->notify(new OrderCreatedTelegram($order, 'customer'));
                }
                
            } catch (\Exception $e) {
                    Log::error('Error enviando notificación: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
            }

            
            return $this->redirectToPayment($order, $paymentMethod);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar orden: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al procesar tu pedido. Intenta nuevamente.');
        }
    }

    protected function redirectToPayment($order, $paymentMethod)
    {
        
        switch ($paymentMethod->code) {
            case 'cash':
                return redirect()->route('orders.show', $order)
                    ->with('success', 'Pedido registrado. Por favor, realiza el pago en efectivo al recibir/retirar.');
            case 'transfer':
                $order->update(['payment_status' => 'pending']);

                $config = $paymentMethod->settings;
                $account = $config['account_number'] ?? 'no especificada';

                return redirect()->route('zelle.pay', $order);
            default:
                return redirect()->route('orders.show', $order);
        }
    }
}