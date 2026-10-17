<?php

namespace App\Models;

use App\Enums\TenantRole;
use App\Services\SaasTenantAccessService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    /**
     * Serialize team-seat checks and membership writes against the central
     * tenant row so concurrent additions cannot consume the same seat.
     */
    public function save(array $options = []): bool
    {
        if (! $this->shouldCheckTeamSeatQuota()) {
            return parent::save($options);
        }

        $tenantId = (string) $this->tenant_id;
        $connection = (string) config(
            'tenancy.database.central_connection',
            config('database.default'),
        );

        return DB::connection($connection)->transaction(function () use ($tenantId, $options): bool {
            Tenant::query()->whereKey($tenantId)->lockForUpdate()->firstOrFail();

            // New tenants can finish onboarding before choosing/purchasing a
            // plan. Once billing history exists, active team seats are capped.
            if (SaasSubscription::query()->where('tenant_id', $tenantId)->exists()) {
                app(SaasTenantAccessService::class)->assertCanAddTeamMember($tenantId);
            }

            return parent::save($options);
        });
    }

    private function shouldCheckTeamSeatQuota(): bool
    {
        if (! $this->countsAsTeamUser()) {
            return false;
        }

        return ! $this->exists || ! $this->originalCountsAsTeamUser();
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
