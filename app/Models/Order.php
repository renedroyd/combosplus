<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'platform_user_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * @deprecated Use shippingAddress() instead.
     */
    public function address()
    {
        return $this->shippingAddress();
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if ($order->order_number) {
                return;
            }

            do {
                $orderNumber = 'ORD-' . Str::upper(Str::random(12));
            } while (static::query()->where('order_number', $orderNumber)->exists());

            $order->order_number = $orderNumber;
        });
    }
}
