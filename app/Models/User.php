<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_chat_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'platform_user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'platform_user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'platform_user_id');
    }

    public function routeNotificationForTelegram(): ?string
    {
        return $this->telegram_chat_id;
    }
}
