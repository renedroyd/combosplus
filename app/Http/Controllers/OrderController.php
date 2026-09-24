<?php

namespace App\Http\Controllers;

use App\Enums\TenantRole;
use App\Models\Order;
use App\Notifications\OrderCancelatedTelegram;
use App\Services\Tenancy\TenantAccessService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(private readonly TenantAccessService $tenantAccess) {}

    public function index()
    {
        $user = request()->user();
        $membership = $this->tenantAccess->membershipForCurrentTenant($user);
        abort_unless($membership, 403, 'No tienes acceso a este negocio.');

        $query = Order::query()->with('items.product')->latest();
        if ($membership->role === TenantRole::Customer) {
            $query->where('user_id', $user->getAuthIdentifier());
        }
        $orders = $query->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product', 'shippingAddress', 'billingAddress', 'paymentMethod');
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);
        if ($order->status !== 'pending') return back()->with('error', 'No se puede cancelar este pedido.');
        $order->update(['status' => 'cancelled']);
        try {
            $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
            if ($adminChatId) \Illuminate\Support\Facades\Notification::route('telegram', $adminChatId)->notify(new OrderCancelatedTelegram($order, 'admin'));
            if ($order->user->telegram_chat_id) $order->user->notify(new OrderCancelatedTelegram($order, 'customer'));
        } catch (\Exception $e) { Log::error('Error enviando notificación de cancelación: '.$e->getMessage()); }
        return back()->with('success', 'Pedido cancelado.');
    }
}
