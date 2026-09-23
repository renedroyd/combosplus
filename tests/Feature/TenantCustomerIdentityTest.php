<?php

namespace Tests\Feature;

use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantCustomerProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class TenantCustomerIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_identity_can_be_provisioned_as_tenant_customer(): void
    {
        $tenant = Tenant::create(['id' => 'customer-provision']);
        $platformUser = PlatformUser::create([
            'name' => 'Customer',
            'email' => 'customer-provision@example.test',
            'password' => 'password',
        ]);

        try {
            $customer = $tenant->run(fn () => app(TenantCustomerProvisioner::class)->ensure($platformUser));

            $this->assertSame($platformUser->id, $customer->id);
            $this->assertSame($platformUser->email, $customer->email);
            $this->assertSame(
                $platformUser->id,
                $tenant->run(fn () => User::query()->find($platformUser->id)?->id)
            );
        } finally {
            $tenant->delete();
            $platformUser->delete();
        }
    }

    public function test_conflicting_tenant_identity_is_rejected(): void
    {
        $tenant = Tenant::create(['id' => 'customer-conflict']);
        $platformUser = PlatformUser::create([
            'name' => 'Platform',
            'email' => 'platform@example.test',
            'password' => 'password',
        ]);

        try {
            $tenant->run(function () use ($platformUser): void {
                User::query()->create([
                    'id' => $platformUser->id,
                    'name' => 'Different',
                    'email' => 'different@example.test',
                    'password' => 'password',
                ]);
            });

            $this->expectException(RuntimeException::class);

            $tenant->run(fn () => app(TenantCustomerProvisioner::class)->ensure($platformUser));
        } finally {
            $tenant->delete();
            $platformUser->delete();
        }
    }
}
