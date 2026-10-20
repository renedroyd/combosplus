<?php

namespace Tests\Feature;

use App\Enums\TenantRole;
use App\Models\Order;
use App\Models\PlatformUser;
use App\Models\Tenant;
use App\Policies\OrderPolicy;
use App\Models\TenantMembership;
use App\Services\Tenancy\TenantAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_management_roles_are_explicit(): void
    {
        $tenant = Tenant::create(['id' => 'policy-roles']);
        $manager = PlatformUser::create(['name' => 'Manager', 'email' => 'manager-policy@example.test', 'password' => 'password']);
        $staff = PlatformUser::create(['name' => 'Staff', 'email' => 'staff-policy@example.test', 'password' => 'password']);

        TenantMembership::create(['tenant_id' => $tenant->id, 'user_id' => $manager->id, 'role' => TenantRole::Manager, 'status' => 'active', 'is_owner' => false]);
        TenantMembership::create(['tenant_id' => $tenant->id, 'user_id' => $staff->id, 'role' => TenantRole::Staff, 'status' => 'active', 'is_owner' => false]);

        try {
            $this->assertTrue($tenant->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($manager)));
            $this->assertTrue($tenant->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($staff)));
        } finally {
            $tenant->delete();
            $manager->delete();
            $staff->delete();
        }
    }

    public function test_customer_can_update_own_pending_order_using_platform_user_id(): void
    {
        $tenant = Tenant::create(['id' => 'policy-order-owner']);
        $customer = PlatformUser::create([
            'name' => 'Customer',
            'email' => 'customer-order-policy@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $customer->id,
            'role' => TenantRole::Customer,
            'status' => 'active',
            'is_owner' => false,
        ]);

        $order = new Order([
            'platform_user_id' => $customer->id,
            'user_id' => null,
            'status' => 'pending',
        ]);

        try {
            $this->assertTrue($tenant->run(
                fn () => app(OrderPolicy::class)->update($customer, $order)
            ));
        } finally {
            $tenant->delete();
            $customer->delete();
        }
    }

    public function test_order_deletion_requires_tenant_membership_and_management_role(): void
    {
        $tenant = Tenant::create(['id' => 'policy-order-delete']);
        $customer = PlatformUser::create([
            'name' => 'Customer',
            'email' => 'customer-order-delete@example.test',
            'password' => 'password',
        ]);
        $outsider = PlatformUser::create([
            'name' => 'Outsider',
            'email' => 'outsider-order-delete@example.test',
            'password' => 'password',
        ]);

        TenantMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $customer->id,
            'role' => TenantRole::Customer,
            'status' => 'active',
            'is_owner' => false,
        ]);

        $order = new Order([
            'platform_user_id' => $customer->id,
            'status' => 'pending',
        ]);

        try {
            $this->assertFalse($tenant->run(
                fn () => app(OrderPolicy::class)->delete($customer, $order)
            ));
            $this->assertFalse($tenant->run(
                fn () => app(OrderPolicy::class)->delete($outsider, $order)
            ));
        } finally {
            $tenant->delete();
            $customer->delete();
            $outsider->delete();
        }
    }

    public function test_membership_in_one_tenant_does_not_authorize_another(): void
    {
        $allowed = Tenant::create(['id' => 'policy-allowed']);
        $other = Tenant::create(['id' => 'policy-other']);
        $user = PlatformUser::create(['name' => 'Owner', 'email' => 'owner-policy@example.test', 'password' => 'password']);

        TenantMembership::create(['tenant_id' => $allowed->id, 'user_id' => $user->id, 'role' => TenantRole::Owner, 'status' => 'active', 'is_owner' => true]);

        try {
            $this->assertTrue($allowed->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($user)));
            $this->assertFalse($other->run(fn () => app(TenantAccessService::class)->canAccessCurrentTenant($user)));
        } finally {
            $allowed->delete();
            $other->delete();
            $user->delete();
        }
    }
}
