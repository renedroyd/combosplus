<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantAddressSchemaTest extends TestCase
{
    public function test_tenant_addresses_support_storefront_contact_fields(): void
    {
        $tenant = Tenant::create(['id' => 'address-schema']);

        try {
            $tenant->run(function (): void {
                $this->assertTrue(Schema::hasColumn('addresses', 'alias'));
                $this->assertTrue(Schema::hasColumn('addresses', 'phone'));
            });
        } finally {
            $tenant->delete();
        }
    }
}
