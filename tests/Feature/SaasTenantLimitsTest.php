<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Services\SaasPlanEntitlementService;
use Tests\TestCase;

class SaasTenantLimitsTest extends TestCase
{
    public function test_agreed_plan_defaults_are_resolved_by_plan_slug(): void
    {
        $this->assertSame(100, SaasPlanEntitlementService::defaultLimitForPlanSlug('inicial', 'max_products'));
        $this->assertSame(1, SaasPlanEntitlementService::defaultLimitForPlanSlug('inicial', 'max_users'));
        $this->assertSame(1, SaasPlanEntitlementService::defaultLimitForPlanSlug('inicial', 'max_branches'));

        $this->assertSame(1000, SaasPlanEntitlementService::defaultLimitForPlanSlug('profesional', 'max_products'));
        $this->assertSame(5, SaasPlanEntitlementService::defaultLimitForPlanSlug('profesional', 'max_users'));
        $this->assertSame(2, SaasPlanEntitlementService::defaultLimitForPlanSlug('profesional', 'max_branches'));

        $this->assertSame('unlimited', SaasPlanEntitlementService::defaultLimitForPlanSlug('empresarial', 'max_products'));
        $this->assertSame(15, SaasPlanEntitlementService::defaultLimitForPlanSlug('empresarial', 'max_users'));
        $this->assertSame(5, SaasPlanEntitlementService::defaultLimitForPlanSlug('empresarial', 'max_branches'));
        $this->assertNull(SaasPlanEntitlementService::defaultLimitForPlanSlug('custom-plan', 'max_products'));
    }

    public function test_branch_model_is_tenant_database_model(): void
    {
        $branch = new Branch();

        $this->assertSame('branches', $branch->getTable());
        $this->assertNull($branch->getConnectionName());
    }
}
