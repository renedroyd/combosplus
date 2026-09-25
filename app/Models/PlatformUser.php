<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class PlatformUser extends Authenticatable implements FilamentUser
{
    use CentralConnection;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

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

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->memberships()->where('status', 'active')->exists();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(TenantMembership::class, 'user_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function activeMembershipForTenant(string $tenantId): ?TenantMembership
    {
        return $this->memberships()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();
    }
}
