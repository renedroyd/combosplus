<?php

namespace App\Models;

use App\Enums\TenantRole;
use App\Services\SaasTenantAccessService;
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
            $hasOwner = static::query()
                ->where('tenant_id', $tenantId)
                ->where('is_owner', true)
                ->exists();
            $hasSubscriptionHistory = SaasSubscription::query()
                ->where('tenant_id', $tenantId)
                ->exists();

            // A brand-new tenant must be able to create its first owner before
            // the owner submits the first subscription payment.
            if ($membership->is_owner && ! $hasOwner && ! $hasSubscriptionHistory) {
                return;
            }

            app(SaasTenantAccessService::class)->assertCanAddTeamMember($tenantId);
        });

        static::updating(function (self $membership): void {
            $wasTeamUser = $membership->originalCountsAsTeamUser();
            $willBeTeamUser = $membership->countsAsTeamUser();

            if ($willBeTeamUser && ! $wasTeamUser) {
                app(SaasTenantAccessService::class)
                    ->assertCanAddTeamMember((string) $membership->tenant_id);
            }
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
