<?php

namespace App\Models;

use App\Enums\TenantRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class TenantMembership extends Model
{
    use CentralConnection;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
        'status',
        'is_owner',
    ];

    protected function casts(): array
    {
        return [
            'role' => TenantRole::class,
            'is_owner' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function active(): bool
    {
        return $this->status === 'active';
    }

    public function canManageTeam(): bool
    {
        return in_array($this->role, [
            TenantRole::Owner,
            TenantRole::Admin,
        ], true);
    }
}
