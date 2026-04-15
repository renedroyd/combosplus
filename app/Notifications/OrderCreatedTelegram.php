<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class OrderCreatedTelegram extends Notification
{
    use Queueable;

    protected Order $order;
    protected string $type; // 'admin' o 'customer'

    public function __construct(Order $order, string $type = 'admin')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        if ($this->type === 'admin') {
            return $this->adminMessage();
        }
        
        return $this->customerMessage();
    }

    protected function adminMessage()
    {
        $order = $this->order;
        
        $message = "🛒 *NUEVA ORDEN RECIBIDA* 🛒\n\n";
        $message .= "📦 *Orden #:* {$order->order_number}\n";
        $message .= "👤 *Cliente:* {$order->user->name}\n";
        $message .= "📧 *Email:* {$order->user->email}\n";
        $message .= "💰 *Total:* $" . number_format($order->total, 2) . "\n";
        $message .= "🚚 *Entrega:* " . ($order->delivery_type === 'delivery' ? 'A domicilio' : 'Recoger en tienda') . "\n";
        
        if ($order->delivery_type === 'delivery' && $order->shippingAddress) {
            $message .= "📍 *Dirección:* {$order->shippingAddress->address_line1}, {$order->shippingAddress->city}\n";
        }
        
        $message .= "💳 *Pago:* " . ($order->paymentMethod->name ?? 'No especificado') . "\n";
        $message .= "📝 *Notas:* " . ($order->notes ?? 'Sin notas');

        return TelegramMessage::create()
            ->to(env('TELEGRAM_ADMIN_CHAT_ID')) // Para admin, usamos el chat ID configurado
            ->content($message)
            ->button('Ver orden en admin', route('filament.admin.resources.orders.edit', $order));
    }

    protected function customerMessage()
    {
        $order = $this->order;
        
        $message = "✅ *¡Gracias por tu compra en CombosPlus+!* ✅\n\n";
        $message .= "Hemos recibido tu orden #{$order->order_number}\n";
        $message .= "Total: $" . number_format($order->total, 2) . "\n";
        $message .= "Te contactaremos cuando esté lista para entrega o recogida.\n\n";
        $message .= "📞 *Atención al cliente:* +53 5555 5555";

        return TelegramMessage::create()
            ->content($message)
            ->button('Ver mi orden', route('orders.show', $order));
    }
}