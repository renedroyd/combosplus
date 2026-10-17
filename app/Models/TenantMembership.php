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

    protected static function booted(): void
    {
        static::creating(function (self $membership): void {
            if (! $membership->countsAsTeamUser()) {
                return;
            }

            $tenantId = (string) $membership->tenant_id;

            // Before the first subscription is purchased, onboarding must be
            // able to finish creating the initial team. Once billing history
            // exists, all new active team seats are checked against the plan.
            if (! SaasSubscription::query()->where('tenant_id', $tenantId)->exists()) {
                return;
            }

            app(SaasTenantAccessService::class)->assertCanAddTeamMember($tenantId);
        });

        static::updating(function (self $membership): void {
            if (! $membership->countsAsTeamUser() || $membership->originalCountsAsTeamUser()) {
                return;
            }

            $tenantId = (string) $membership->tenant_id;

            if (! SaasSubscription::query()->where('tenant_id', $tenantId)->exists()) {
                return;
            }

            app(SaasTenantAccessService::class)->assertCanAddTeamMember($tenantId);
        });
    }

    private function countsAsTeamUser(): bool
    {
        $role = $this->role;
        $role = $role instanceof TenantRole ? $role->value : (string) $role;
        $status = $this->status ?? 'active';

        return $status === 'active' && ($this->is_owner || $role !== 'customer');
    }

    private function originalCountsAsTeamUser(): bool
    {
        $role = $this->getRawOriginal('role');
        $role = $role instanceof TenantRole ? $role->value : (string) $role;

        return $this->getRawOriginal('status') === 'active'
            && ((bool) $this->getRawOriginal('is_owner') || $role !== 'customer');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'user_id');
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
