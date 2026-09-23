<?php

namespace Tests\Feature;

use Tests\TestCase;

class TenancyConfigurationTest extends TestCase
{
    public function test_tenancy_uses_the_application_tenant_model(): void
    {
        $this->assertSame(
            \App\Models\Tenant::class,
            config('tenancy.tenant_model')
        );
    }

    public function test_tenant_database_migrations_have_a_dedicated_path(): void
    {
        $this->assertContains(
            database_path('migrations/tenant'),
            config('tenancy.migration_parameters.--path')
        );
    }
}
