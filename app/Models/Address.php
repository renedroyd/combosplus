<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'platform_user_id');
    }

    protected static function booted()
    {
        static::saved(function (Address $address): void {
            if (! $address->is_default) {
                return;
            }

            $address->user?->addresses()
                ->whereKeyNot($address->getKey())
                ->update(['is_default' => false]);
        });
    }
}
