<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Notifications\OrderCreatedTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    public function index()
    {
        if (! auth()->check()) {
            session()->put('intended_checkout', true);

            return view('checkout.guest');
        }

        $cart = auth()->user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $addresses = auth()->user()->addresses;
        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $subtotal = $cart->items->sum(fn ($item) => $item->price * $item->quantity);
        $shippingCost = 5.00;
        $total = $subtotal + $shippingCost;

        return view('checkout.index', compact(
            'cart',
            'addresses',
            'paymentMethods',
            'subtotal',
            'shippingCost',
            'total',
        ));
    }

    public function process(Request $request)
    {
        $rules = [
            'delivery_type' => 'required|in:pickup,delivery',
            'payment_method_id' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ];

        if ($request->input('delivery_type') === 'delivery') {
            $rules['address_id'] = 'required|integer';
        }

        $data = $request->validate($rules);
        $user = $request->user();

        try {
            [$order, $paymentMethod] = DB::transaction(function () use ($data, $user): array {
                $cart = $user->cart()->lockForUpdate()->first();

                if (! $cart) {
                    abort(422, 'Carrito vacío.');
                }

                $cart->load('items.product');

                if ($cart->items->isEmpty()) {
                    abort(422, 'Carrito vacío.');
                }

                if ($data['delivery_type'] === 'delivery') {
                    Address::query()
                        ->whereKey($data['address_id'])
                        ->where('user_id', $user->getAuthIdentifier())
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $paymentMethod = PaymentMethod::query()
                    ->whereKey($data['payment_method_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                $subtotal = $cart->items->sum(fn ($item) => $item->price * $item->quantity);
                $shippingCost = $data['delivery_type'] === 'delivery' ? 5.00 : 0.00;
                $total = $subtotal + $shippingCost;

                $order = Order::create([
                    'user_id' => $user->getAuthIdentifier(),
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

                return [$order->fresh(), $paymentMethod];
            });

            $this->sendOrderNotifications($order);

            return $this->redirectToPayment($order, $paymentMethod);
        } catch (\Throwable $e) {
            Log::error('Error al procesar orden.', [
                'message' => $e->getMessage(),
                'user_id' => $user?->getAuthIdentifier(),
            ]);

            return back()->with('error', 'Hubo un problema al procesar tu pedido. Intenta nuevamente.');
        }
    }

    protected function sendOrderNotifications(Order $order): void
    {
        try {
            $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');

            if ($adminChatId) {
                Notification::route('telegram', $adminChatId)
                    ->notify(new OrderCreatedTelegram($order, 'admin'));
            }

            if ($order->user?->telegram_chat_id) {
                $order->user->notify(new OrderCreatedTelegram($order, 'customer'));
            }
        } catch (\Throwable $e) {
            Log::error('Error enviando notificación de pedido.', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function redirectToPayment(Order $order, PaymentMethod $paymentMethod)
    {
        return match ($paymentMethod->code) {
            'cash' => redirect()->route('orders.show', $order)
                ->with('success', 'Pedido registrado. Por favor, realiza el pago en efectivo al recibir/retirar.'),
            'transfer' => redirect()->route('zelle.pay', $order),
            default => redirect()->route('orders.show', $order),
        };
    }
}
