<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\OrderCancelatedTelegram;
use App\Notifications\OrderCreatedTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order); // Policy necesaria
        $order->load('items.product', 'address', 'paymentMethod');
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);

            try {
                // NOTIFICACIÓN TELEGRAM
                
                // 1. Notificar al administrador (siempre)
                $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
                Log::info('Admin Chat ID: ' . ($adminChatId ?? 'no definido'));

                if ($adminChatId) {
                    \Illuminate\Support\Facades\Notification::route('telegram', $adminChatId)
                        ->notify(new OrderCancelatedTelegram($order, 'admin'));
                    Log::info('Notificación admin enviada');
                } else {
                    Log::warning('TELEGRAM_ADMIN_CHAT_ID no está definido en .env');
                }
                
                // 2. Notificar al cliente (si tiene Telegram configurado)
                if ($order->user->telegram_chat_id) {
                    $order->user->notify(new OrderCancelatedTelegram($order, 'customer'));
                }
                
            } catch (\Exception $e) {
                    Log::error('Error enviando notificación: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
            }

            return back()->with('success', 'Pedido cancelado.');
        }
        return back()->with('error', 'No se puede cancelar este pedido.');
    }
}