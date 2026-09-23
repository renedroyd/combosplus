<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenancyConfigurationTest extends TestCase
{
    public function test_tenancy_uses_the_application_tenant_model(): void
    {
        $this->assertSame(
            Tenant::class,
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

    public function test_creating_a_tenant_provisions_the_operational_schema(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Tenant database provisioning requires the MySQL CI service.');
        }

        $tenant = Tenant::create([
            'id' => 'ci-tenant-'.bin2hex(random_bytes(6)),
        ]);

        $databaseName = $tenant->database()->getName();

        try {
            $this->assertNotSame(
                config('database.connections.mysql.database'),
                $databaseName
            );

            tenancy()->initialize($tenant);

            $this->assertTrue(Schema::connection('tenant')->hasTable('users'));
            $this->assertTrue(Schema::connection('tenant')->hasTable('products'));
            $this->assertTrue(Schema::connection('tenant')->hasTable('orders'));
            $this->assertTrue(Schema::connection('tenant')->hasTable('remesas'));
        } finally {
            tenancy()->end();
            $tenant->delete();
        }
    }
}
